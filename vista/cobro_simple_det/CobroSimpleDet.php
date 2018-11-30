<?php
/**
*@package pXP
*@file gen-CobroSimpleDet.php
*@author  (admin)
*@date 01-01-2018 06:21:25
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 * *   	
 ISSUE            FECHA:		      AUTOR                 DESCRIPCION
 * 123				20/08/2018     EGS						se modificaron a las funciones y formulario relacionado a relacion entre factura y cobro
 * 124				24/08/2018		EGS						se modifico para qu no se pueda adicionar facturas en los primeros estados de cobro de anticipos
 * 125				12/09/2018		EGS						se modifico para la vista del formulario de prorrateo muestre los campos de anticipo
 * 
 * */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.CobroSimpleDet = {
    
	require: '../../../sis_cobros/vista/cobro_simple/DocCompraCBR.php',
    ActList:'../../sis_cobros/control/CobroSimpleDet/listarCobroSimpleDet',
	requireclase: 'Phx.vista.DocCompraCBR',
	title: 'Documentos',
	nombreVista: 'CobroSimpleDet',
	tipoDoc: 'compra',
	formTitulo: 'Formulario de Documento Compra',
	
	constructor: function(config) {
		
		this.Atributos1 = [];
	    
		
		
	    
	    this.Atributos1.push({
			config:{
					labelSeparator:'',
					fieldLabel: 'ID DET',
					inputType:'hidden',
					name: 'id_cobro_simple_det'
			},
			type:'Field',
			grid:true,
			form:true 
		});
		
		this.Atributos1.push({
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_cobro_simple'
			},
			type:'Field',
			form:true 
		});
		
		this.Atributos1.push({
				config:{
					name: 'importe_mb',
					fieldLabel: 'Prorrateo BS',
					allowBlank: false,
					anchor: '80%',
					gwidth: 80,
					galign: 'right ',
					maxLength:1179650,
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}',  Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							Ext.util.Format.usMoney
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'paside.importe_mb',type:'numeric'},
					id_grupo:1,
					
					grid:true,
					form:false
			});
			
		this.Atributos1.push({
				config:{
					name: 'importe_mt',
					fieldLabel: 'Prorrateo USD',
					allowBlank: false,
					anchor: '80%',
					gwidth: 80,
					galign: 'right ',
					maxLength:1179650,
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}',  Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							Ext.util.Format.usMoney
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'paside.importe_mb',type:'numeric'},
					id_grupo:1,
					
					grid:true,
					form:false
			});	
			
			
		
		
		
		
		Phx.vista.CobroSimpleDet.superclass.constructor.call(this,config);
	    
		
		
		
		
	    this.crearFormAuto();
        
       this.addButton('btnNewDoc',{ 
       	    text: 'Adicionar factura', 
       	    iconCls: 'blist', 
       	    disabled: false, 
       	    handler: this.mostarFormAuto, 
       	    tooltip: 'Permite relacionar un documento existente al Cbte'});
       	    
       	    
        this.addButton('btnShowDoc',
            {
                text: 'Ver Detalle',
                iconCls: 'brenew',
                disabled: true,
                handler: this.showDoc,
                tooltip: 'Muestra el detalle del documento'
            }
        );	    

         
        
		
        //this.Cmp.id_plantilla.store.baseParams = Ext.apply(this.Cmp.id_plantilla.store.baseParams, {tipo_plantilla:this.tipoDoc});

        this.cmbDepto.setVisible(false);
        this.cmbGestion.setVisible(false);
        this.cmbPeriodo.setVisible(false);
        this.getBoton('btnWizard').setVisible(false);
        this.getBoton('btnImprimir').setVisible(false);
        this.getBoton('btnExpTxt').setVisible(false);

        
    },
   
    
    loadValoresIniciales: function() {
    	Phx.vista.CobroSimpleDet.superclass.loadValoresIniciales.call(this);
        //this.Cmp.tipo.setValue(this.tipoDoc); 
        
   },
   capturaFiltros:function(combo, record, index){
        this.store.baseParams.tipo = this.tipoDoc;
        Phx.vista.CobroSimpleDet.superclass.capturaFiltros.call(this,combo, record, index);
    },
    
    onReloadPage: function(m) {    	
        this.maestro = m;
        
        this.preparaMenu();
        
        this.Atributos[this.getIndAtributo('id_cobro_simple')].valorInicial = this.maestro.id_cobro_simple;
        //Filtro para los datos
        this.store.baseParams = {
            id_cobro_simple: this.maestro.id_cobro_simple
        };
        
       
        this.load({
            params: {
                start: 0,
                limit: 50
            }
        });
    },
    validarFiltros: function(){
    	return true;
    },
    preparaMenu:function(tb){
    	Phx.vista.DocCompraVenta.superclass.preparaMenu.call(this,tb)
    	
    	if(this.maestro.estado == 'borrador') {	
			this.getBoton('btnNewDoc').enable();
			this.getBoton('del').enable();
		} 
		else {
			this.getBoton('btnNewDoc').disable();
			this.getBoton('del').disable();
		}
		this.getBoton('btnShowDoc').enable();
		
		//////////I-EGS///24/08/2018  ---124
		console.log('tipo',this.maestro.codigo_tipo_cobro_simple);
		this.obtenerVariableGlobal();
		
		if(this.maestro.codigo_tipo_cobro_simple == this.codigo_ant) {
			if (this.maestro.estado == 'borrador' ||this.maestro.estado == 'vbtesoreria' ||this.maestro.estado == 'pendiente'  ) {
			this.getBoton('btnNewDoc').disable();
			this.getBoton('del').disable();
				
			}else{
			
			this.getBoton('btnNewDoc').enable();
			this.getBoton('del').enable();
				
				
			}
			
		} 
		//////////F-EGS///24/08/2018   ---124
	
		
    },
    
    liberaMenu:function(tb){
        Phx.vista.DocCompraVenta.superclass.liberaMenu.call(this,tb);
        this.getBoton('btnShowDoc').disable();
    },
        //////////I-EGS///24/08/2018  ---124
     obtenerVariableGlobal: function(config){

         Phx.CP.loadingShow();
        
         Ext.Ajax.request({
         		    url:'../../sis_seguridad/control/Subsistema/obtenerVariableGlobal',
		
		 params:{
			           codigo: 'v_cobro_anticipo'
				 },
		         success: function(resp){
			             	 Phx.CP.loadingHide();
					          var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
						      if (reg.ROOT.error) {
							            Ext.Msg.alert('Error','Error a recuperar la variable global')
							  } else {
									  
							 													
										 //console.log(reg.ROOT.datos.valor);
										 var codigo = reg.ROOT.datos.valor;
										 var regularizado_anticipo = codigo.split(","); 
										 ///console.log(regularizado_anticipo[0]);
										 this.codigo_ant=regularizado_anticipo[0];  
										 ///console.log(this.codigo_ant);
									
								}
				 },
				failure: this.conexionFailure,
				timeout: this.timeout,
				scope:this
			});
	  			
        },
    //////////F-EGS///24/08/2018  ---124
    bnew: false,
    bedit: false,
    bsave: false,
    ActDel:'../../sis_cobros/control/CobroSimpleDet/eliminarCobroSimpleDet',
    id_store:'id_cobro_simple_det',
	fields: [
		{name:'id_cobro_simple_det', type: 'numeric'},
		{name:'id_doc_compra_venta', type: 'string'},
		{name:'revisado', type: 'string'},
		{name:'movil', type: 'string'},
		{name:'tipo', type: 'string'},
		{name:'importe_excento', type: 'numeric'},
		{name:'id_plantilla', type: 'numeric'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'nro_documento', type: 'string'},
		{name:'nit', type: 'string'},
		{name:'importe_ice', type: 'numeric'},
		{name:'nro_autorizacion', type: 'string'},
		{name:'importe_iva', type: 'numeric'},
		{name:'importe_descuento', type: 'numeric'},
		{name:'importe_doc', type: 'numeric'},
		{name:'sw_contabilizar', type: 'string'},
		{name:'tabla_origen', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'id_depto_conta', type: 'numeric'},
		{name:'id_origen', type: 'numeric'},
		{name:'obs', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'codigo_control', type: 'string'},
		{name:'importe_it', type: 'numeric'},
		{name:'razon_social', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'importe_pendiente', type: 'numeric'},
		{name:'importe_anticipo', type: 'numeric'},
		{name:'importe_retgar', type: 'numeric'},
		{name:'importe_neto', type: 'numeric'},
		'desc_depto','desc_plantilla',
		'importe_descuento_ley','importe_aux_neto',
		'importe_pago_liquido','nro_dui','id_moneda','desc_moneda',
		'desc_tipo_doc_compra_venta','id_tipo_doc_compra_venta','nro_tramite',
		'desc_comprobante','id_int_comprobante','id_auxiliar','codigo_auxiliar','nombre_auxiliar','tipo_reg',
		'estacion', 'id_punto_venta', 'nombre', 'id_agencia', 'codigo_noiata','desc_funcionario2','id_funcionario',
		'importe_mb','importe_mt'
	],
	abrirFormulario: function(tipo, record){
   	       var me = this;
   	       
   	       if(this.maestro.id_depto_conta){
   	       	
   	                me.objSolForm = Phx.CP.loadWindows('../../../sis_cobros/vista/cobro_simple/FormRendicionCBR.php',
			                                me.formTitulo,
			                                {
			                                    modal:true,
			                                    width:'90%',
												height:(me.regitrarDetalle == 'si')? '100%':'60%',
		
		
			                                    
			                                }, { data: { 
				                                	 objPadre: me ,
				                                	 tipoDoc: me.tipoDoc,	                                	 

				                                	 id_gestion: this.maestro.id_gestion,
				                                	 id_periodo: this.maestro.id_periodo,

				                                	 id_depto: this.maestro.id_depto_conta,
				                                	 tmpPeriodo: me.tmpPeriodo,
		                                             tmpGestion: me.tmpGestion,
				                                	 tipo_form : tipo,
				                                	 datosOriginales: record,
				                                	 readOnly: (tipo=='noedit')? true: false
			                                    },
			                                     bsubmit: (tipo=='noedit')? false: true ,
			                                    regitrarDetalle: 'si'
			                                }, 
			                                this.idContenedor,
			                                'FormRendicionCBR',
			                                {
			                                    config:[{
			                                              event:'successsave',
			                                              delegate: this.onSaveForm,
			                                              
			                                            }],
			                                    
			                                    scope:this
			                                 }); 
	                                 
	           }                        
   },
   
   
   //formulario de autorizaciones
	crearFormAuto:function(){
		  this.formAuto = new Ext.form.FormPanel({
            baseCls: 'x-plain',
            autoDestroy: true,           
            border: false,
            layout: 'form',
            autoHeight: true,           
    
            items: [
		            {
		                name: 'id_doc_compra_venta_cbr',
		                xtype:"combo",
		                fieldLabel: 'Documento ID',
		                allowBlank: false,
		                emptyText:'Elija una plantilla...',
		                store:new Ext.data.JsonStore(
		                {
		                    url: '../../sis_contabilidad/control/DocCompraVenta/listarDocCompraVentaCobro',
		                    id: 'id_doc_compra_venta',
		                    root:'datos',
		                    sortInfo:{
		                        field:'desc_plantilla',
		                        direction:'ASC'
		                    },
		                    totalProperty:'total',
		                    fields: ['id_doc_compra_venta','revisado','nro_documento','nit',
		                    'desc_plantilla', 'desc_moneda','importe_doc','nro_documento',
		                    'tipo','razon_social','fecha','importe_pendiente','importe_cobrado_mb','importe_cobrado_mt','saldo_por_cobrar_pendiente','saldo_por_cobrar_retgar','importe_retgar','importe_cobrado_retgar_mb','importe_cobrado_retgar_mt','importe_anticipo','importe_cobrado_ant_mb','importe_cobrado_ant_mt','saldo_por_cobrar_anticipo'],
		                    remoteSort: true,
		                    baseParams:{par_filtro:'mon.codigo#pla.desc_plantilla#dcv.razon_social#dcv.nro_documento#dcv.nit#dcv.importe_doc#importe_retgar'}
		                }),
		             /////125				12/09/2018		EGS	
		           		tpl:'<tpl for=".">\ <div class="x-combo-list-item"><p><b>{razon_social},  NIT: {nit}</b></p>\<p>{desc_plantilla} </p><p>Doc: {nro_documento} de Fecha: {fecha}</p>\<p>Doc: {importe_doc}  - {desc_moneda}</p><p>Pendiente: {importe_pendiente}  - {desc_moneda}</p><p>Retgar: {importe_retgar}  - {desc_moneda}</p><p>Anticipo: {importe_anticipo}  - {desc_moneda}</p><p>Por cobrar {saldo_por_cobrar_pendiente} - {desc_moneda}</p><p>Por Cobrar Ret Gar: {saldo_por_cobrar_retgar}  - {desc_moneda}</p><p>Por Cobrar Ant: {saldo_por_cobrar_anticipo}  - {desc_moneda}</p><p>Cobrado {importe_cobrado_mb} - BS</p><p>Cobrado RetGar {importe_cobrado_retgar_mb} BS</p><p>Cobrado Anti {importe_cobrado_ant_mb} - BS</p><p><font color="green"> Cobrado {importe_cobrado_mt} - USD</font></p><p><font color="green"> Cobrado RetGar {importe_cobrado_retgar_mt} - USD</font></p><p><font color="green"> Cobrado Anticipo {importe_cobrado_ant_mt} - USD</font></p></div></tpl>',
		           ///125				12/09/2018		EGS	        
		                       
		                valueField: 'id_doc_compra_venta',
		                hiddenValue: 'id_doc_compra_venta',
		                displayField: 'desc_plantilla',
		                //gdisplayField:'nro_documento',
		                listWidth:'280',
		                forceSelection:true,
		                typeAhead: false,
		                triggerAction: 'all',
		                lazyRender:true,
		                mode:'remote',
		                pageSize:20,
		                queryDelay:500,
		                gwidth: 100,
		                minChars:2
		            },
		            {
			                name: 'monto_prorrateo',
			                xtype:"moneyfield",		
			                decimalPrecision:5,	                
			                fieldLabel: 'Monto a pagar',
			                qtip:'Monto a pagar correpondiente a la factura',
			                currencyChar:' ',
			                allowBlank: true
			       }
		            
            ]
        });
        
		
		
		this.wAuto = new Ext.Window({
            title: 'Configuracion',
            collapsible: true,
            maximizable: true,
            autoDestroy: true,
            width: 380,
            height: 170,
            layout: 'fit',
            plain: true,
            bodyStyle: 'padding:5px;',
            buttonAlign: 'center',
            items: this.formAuto,
            modal:true,
             closeAction: 'hide',
            buttons: [{
                text: 'Guardar',
                handler: this.saveAuto,
                scope: this
                
            },
             {
                text: 'Cancelar',
                handler: function(){ this.wAuto.hide() },
                scope: this
            }]
        });
        
         this.cmpIdDocCompraVenta = this.formAuto.getForm().findField('id_doc_compra_venta_cbr');
         this.cmpMontoProrrateo = this.formAuto.getForm().findField('monto_prorrateo');
         
         
	},
   
   mostarFormAuto:function(){
   		 var tipo = this.maestro.codigo_tipo_cobro_simple ;
   		 console.log(tipo);
  	     this.wAuto.show();
  	     
  	     this.cmpIdDocCompraVenta.reset();
  	     this.cmpMontoProrrateo.reset();
  	       
  	      this.cmpIdDocCompraVenta.store.baseParams.id_proveedor =  this.maestro.id_proveedor;
  	      this.cmpIdDocCompraVenta.store.baseParams.tipo_cobro = this.maestro.codigo_tipo_cobro_simple ;//////////////////EGS-I-20/08/2018//// 123

  	      this.cmpIdDocCompraVenta.modificado = true;

   },
   
   showDoc:  function() {
        this.abrirFormulario('noedit', this.sm.getSelected());
   },
   
   saveAuto: function(){
		    var d = this.getSelectedData();
		    Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_cobros/control/CobroSimpleDet/relacionarFacturaCobro',
                params: { 
                	      id_doc_compra_venta: this.cmpIdDocCompraVenta.getValue(),
                	      id_cobro_simple: this.maestro.id_cobro_simple,
                	      monto_prorrateo: this.cmpMontoProrrateo.getValue()
                	    },
                success: this.successSinc,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
		
	},
	successSinc:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(!reg.ROOT.error){
            	if(this.wOt){
            		this.wOt.hide(); 
            	}
            	if(this.wAuto){
            		this.wAuto.hide(); 
            	}
                
                this.reload();
             }else{
                alert('ocurrio un error durante el proceso')
            }
    },
	
};
</script>