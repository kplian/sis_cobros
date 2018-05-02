<?php
/**
*@package pXP
*@file gen-CobroSimple.php
*@author  (admin)
*@date 31-12-2017 12:33:30
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.CobroSimple=Ext.extend(Phx.gridInterfaz,{
	nombreVista: 'CobroSimple',
	constructor:function(config){
		this.maestro=config.maestro;

		//Historico
		this.historico = 'no';

    	//llama al constructor de la clase padre
		Phx.vista.CobroSimple.superclass.constructor.call(this,config);
		this.init();
		
		//Adicion de botones en la barra de herramientas
		this.addButton('ant_estado',{ argument: {estado: 'anterior'},text:'Atras',iconCls: 'batras',disabled:true,handler:this.antEstado,tooltip: '<b>Pasar al Anterior Estado</b>'});
        this.addButton('sig_estado',{ text:'Siguiente', iconCls: 'badelante', disabled: true, handler: this.sigEstado, tooltip: '<b>Pasar al Siguiente Estado</b>'});
        this.addBotonesGantt();
        this.addButton('btnChequeoDocumentosWf',
            {
                text: 'Documentos',
                grupo:[0,1,2,3,4],
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.loadCheckDocumentosSolWf,
                tooltip: '<b>Documentos de la Solicitud</b><br/>Subir los documetos requeridos en la solicitud seleccionada.'
            });
        
        this.addButton('btnObs',{
                    text :'Obs Wf',
                    grupo:[0,1,2,3,4],
                    iconCls : 'bchecklist',
                    disabled: true,
                    handler : this.onOpenObs,
                    tooltip : '<b>Observaciones</b><br/><b>Observaciones del WF</b>'
        });

  
        this.addButton('btnDetalleDocumentoCobroSimple',
            {
                text: 'Detalle documento ps',
                grupo:[0,1,2,3,4],
                iconCls: 'bprint',
                disabled: true,
                handler: this.loadDetalleDocCobroSimple,
                tooltip: '<b>Documentos de la Solicitud</b><br/>Subir los documetos requeridos en la solicitud seleccionada.'
            });

     

        this.iniciarEventos();
      
        Ext.apply(this.store.baseParams, {
			estado: 'borrador',
			tipo_interfaz: this.nombreVista
		});

		this.load({params:{start:0, limit:this.tam_pag}});

	

	},
		
    loadDetalleDocCobroSimple : function(){   
    	
         var NumSelect=this.sm.getCount();       
         if(NumSelect!=0) {
               var data=this.sm.getSelected().data;
               console.log(data);

                      Phx.CP.loadingShow();
                      Ext.Ajax.request({
                            url:'../../sis_cobros/control/CobroSimpleRep/reporteLCV',
                            params:{
                                   'id_cobro_simple':data.id_cobro_simple, 
                            },
                            success:this.successExport,
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                      });
         }
         else {
                Ext.MessageBox.alert('Estado', 'Antes debe seleccionar un item.');
         }
    },
    	
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_cobro_simple'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_estado_wf'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id_config_cambiaria'
			},
			type : 'Field',
			id_grupo : 0,
			form : true
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_proceso_wf'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_depto_lb'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_cuenta_bancaria'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_gestion'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_periodo'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'nro_tramite',
				fieldLabel: 'Nro.Tramite',
				allowBlank: true,
				anchor: '80%',
				gwidth: 160,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'pagsim.nro_tramite',type:'string'},
				id_grupo:1,
				grid:true,
				form:false,
				bottom_filter:true
		},
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha',
				allowBlank: false,
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'pagsim.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name:'id_tipo_cobro_simple',
				fieldLabel:'Tipo Solicitud Pago',
				allowBlank: false,
				emptyText:'Tipo...',
				typeAhead: true,
				lazyRender:true,
				mode: 'remote',
				gwidth: 180,
				anchor: '100%',
				store: new Ext.data.JsonStore({
					url: '../../sis_cobros/control/TipoCobroSimple/listarTipoCobroSimple',
					id: 'id_tipo_cobro_simple',
					root: 'datos',
					sortInfo:{
						field: 'nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_tipo_cobro_simple','nombre','codigo'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'nombre', sw_solicitud: 'si'
					}
				}),
				valueField: 'id_tipo_cobro_simple',
				displayField: 'nombre',
				gdisplayField: 'desc_tipo_cobro_simple',
				hiddenName: 'id_tipo_cobro_simple',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode:'remote',
				pageSize: 10,
				queryDelay: 1000,
				resizable: true,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_tipo_cobro_simple']);
				}
			},
			type:'ComboBox',
			id_grupo:1,
			filters:{pfiltro:'tps.codigo#tps.nombre',type:'string'},
			grid:true,
			form:true
		},
		{
   			config:{
       		    name:'id_funcionario',
       		    hiddenName: 'id_funcionario',
   				origen:'FUNCIONARIOCAR',
   				fieldLabel:'Solicitante',
   				allowBlank:false,
                gwidth:200,
                anchor: '100%',
   				valueField: 'id_funcionario',
   			    gdisplayField: 'desc_funcionario',
   			    baseParams: { es_combo_solicitud : 'si' },
      			renderer:function(value, p, record){return String.format('{0}', record.data['desc_funcionario']);}
       	     },
   			type:'ComboRec',//ComboRec
   			id_grupo:0,
   			filters:{pfiltro:'fun.desc_funcionario1',type:'string'},
   			bottom_filter:true,
   		    grid:true,
   			form:true
		},
		{     
			config:{
				name:'id_depto_conta',
				origen:'DEPTO',
				fieldLabel: 'Departamento Contabilidad',
				url: '../../sis_parametros/control/Depto/listarDepto',
				emptyText : 'Departamento Contabilidad ...',
				allowBlank:false,
				anchor: '80%',
				gdisplayField: 'desc_depto_conta',
				gwidth: 200,
				baseParams: {par_filtro: 'deppto.nombre#deppto.codigo',codigo_subsistema:'CONTA'}
			  },
			type:'ComboRec',
			id_grupo:0,
   			filters:{pfiltro:'dep.codigo',type:'string'},
   			bottom_filter:true,
			form:true,
   		    grid:true
		},
		{
            config:{
                name: 'id_moneda',
                origen: 'MONEDA',
                allowBlank: false,
                fieldLabel: 'Moneda',
                anchor: '100%',
                gdisplayField: 'desc_moneda',//mapea al store del grid
                gwidth: 50,
                //baseParams: { 'filtrar_base': 'si' },
                renderer: function (value, p, record){return String.format('{0}', record.data['desc_moneda']);}
             },
            type: 'ComboRec',
            id_grupo: 1,
            filters: { pfiltro:'mon.codigo',type:'string'},
            grid: true,
            form: true
        },
        {
			config : {
				name : 'forma_cambio',
				fieldLabel : 'Cambio',
				qtip : 'Tipo cambio oficial, compra, venta o convenido',
				allowBlank : false,
				gwidth : 100,
				width : 250,
				typeAhead : true,
				triggerAction : 'all',
				lazyRender : true,
				mode : 'local',
				valueField : 'oficial',
				store : ['oficial', 'compra','venta','convenido']
			},
			type : 'ComboBox',
			id_grupo : 2,
			filters : {
				type : 'list',
				pfiltro : 'incbte.forma_cambio',
				options : ['oficial', 'compra','venta','convenido'],
			},
			grid : true,
			form : true
		},
        {
			config : {
				name : 'tipo_cambio',
				readOnly : true,
				fieldLabel : 'TC',
				allowBlank : false,
				anchor : '80%',
				gwidth : 70,
				maxLength : 20,
				decimalPrecision : 10
			},
			type : 'NumberField',
			filters : {
				pfiltro : 'pagsim.tipo_cambio',
				type : 'numeric'
			},
			id_grupo : 2,
			grid : true,
			form : true
		}, 
		{
			config : {
				name : 'tipo_cambio_mt',
				fieldLabel : '(TC)',
				allowBlank : false,
				readOnly : true,
				anchor : '80%',
				gwidth : 70,
				maxLength : 20,
				decimalPrecision : 6
			},
			type : 'NumberField',
			filters : {
				pfiltro : 'pagsim.tipo_cambio_mt',
				type : 'numeric'
			},
			id_grupo : 2,
			grid : true,
			form : true
		}, 
		{
			config : {
				name : 'tipo_cambio_ma',
				fieldLabel : '(TC)',
				allowBlank : false,
				readOnly : true,
				anchor : '80%',
				gwidth : 70,
				maxLength : 20,
				decimalPrecision : 6
			},
			type : 'NumberField',
			filters : {
				pfiltro : 'pagsim.tipo_cambio_ma',
				type : 'numeric'
			},
			id_grupo : 2,
			grid : true,
			form : true
		},
        
        {
			config:{
				name: 'importe',
				fieldLabel: 'Importe a Cobrar',
				allowBlank: false,				
				allowNegative :false,
				minValue: 1.00,
				anchor: '80%',
				gwidth: 160,
				maxLength:100
			},
				type:'MoneyField',
				filters:{pfiltro:'pagsim.importe',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config:{
                name: 'id_proveedor',
                origen: 'PROVEEDOR',
                allowBlank: true,
                fieldLabel: 'Proveedor',
                anchor: '100%',
                gdisplayField: 'desc_proveedor',//mapea al store del grid
                gwidth: 150,
                //baseParams: { 'filtrar_base': 'si' },
                renderer: function (value, p, record){return String.format('{0}', record.data['desc_proveedor']);}
             },
            type: 'ComboRec',
            id_grupo: 1,
            filters: { pfiltro:'pro.desc_proveedor',type:'string'},
            grid: true,
            form: true,
            bottom_filter:true,
        },
        {
   			config:{
       		    name:'id_funcionario_pago',
       		    hiddenName: 'id_funcionario_pago',
   				origen:'FUNCIONARIOCAR',
   				fieldLabel:'Pagar a Funcionario',
   				allowBlank:true,
                gwidth:200,
                anchor: '100%',
   				valueField: 'id_funcionario',
   			    gdisplayField: 'desc_funcionario_pago',
   			    baseParams: { es_combo_solicitud : 'si' },
       			renderer:function(value, p, record){return String.format('{0}', record.data['desc_funcionario_pago']);}
       	     },
   			type:'ComboRec',//ComboRec
   			id_grupo:0,
   			filters:{pfiltro:'fun.desc_funcionario1',type:'string'},
   			bottom_filter:true,
   		    grid:true,
   			form:true
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:30
			},
				type:'TextField',
				filters:{pfiltro:'pagsim.estado',type:'string'},
				id_grupo:1,
				grid:true,
				form:false,
				bottom_filter:true
		},
		{
			config:{
				name: 'obs',
				fieldLabel: 'Glosa',
				allowBlank: false,
				anchor: '100%',
				gwidth: 300,
				maxLength:500
			},
				type:'TextArea',
				filters:{pfiltro:'pagsim.obs',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter:true
		},
		{
			config:{
				name:'id_obligacion_pago',
				fieldLabel:'Obligacion de Pago',
				allowBlank: true,
				emptyText:'Seleccione un registro ...',
				typeAhead: false,
				lazyRender:true,
				mode: 'remote',
				gwidth: 180,
				anchor: '100%',
				store: new Ext.data.JsonStore({
					url: '../../sis_tesoreria/control/ObligacionPago/listarObligacionPagoPS',
					id: 'id_obligacion_pago',
					root: 'datos',
					sortInfo:{
						field: 'num_tramite',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_obligacion_pago','num_tramite','fecha','obs','tipo_obligacion','total_pago','tipo_solicitud','desc_funcionario1'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'op.num_tramite',  cobro_simple : 'si' }
				}),
				valueField: 'id_obligacion_pago',
				displayField: 'num_tramite',
				gdisplayField: 'desc_obligacion_pago',
				hiddenName: 'id_obligacion_pago',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode:'remote',
				pageSize: 10,
				queryDelay: 1000,
				resizable: true,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_obligacion_pago']);
				}
			},
			type:'ComboBox',
			id_grupo:1,
			filters:{pfiltro:'op.num_tramite',type:'string'},
			grid:true,
			form:true
		},
		{
			config: {
				name: 'id_caja',
				fieldLabel: 'Caja',
				allowBlank: true,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_tesoreria/control/Caja/listarCaja',
					id: 'id_caja',
					root: 'datos',
					sortInfo: {
						field: 'codigo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_caja', 'codigo', 'desc_moneda','id_depto','cajero'],
					remoteSort: true,
					baseParams: {par_filtro: 'caja.codigo', tipo_interfaz:'solicitudcaja', con_detalle:'no'}
				}),
				valueField: 'id_caja',
				displayField: 'codigo',
				gdisplayField: 'desc_caja',
				hiddenName: 'id_caja',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 100,
				minChars: 2,
				tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{codigo}</b></p><p>CAJERO: {cajero}</p></div></tpl>',
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['codigo']);
				},
				hidden: true
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.codigo',type: 'string'},
			grid: true,
			form: true
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'pagsim.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'pagsim.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'pagsim.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'pagsim.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				format: 'd/m/Y', 
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'pagsim.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Pago Simple',
	ActSave:'../../sis_cobros/control/CobroSimple/insertarCobroSimple',
	ActDel:'../../sis_cobros/control/CobroSimple/eliminarCobroSimple',
	ActList:'../../sis_cobros/control/CobroSimple/listarCobroSimple',
	id_store:'id_cobro_simple',
	fields: [
		{name:'id_cobro_simple', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_depto_conta', type: 'numeric'},
		{name:'nro_tramite', type: 'string'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'id_funcionario', type: 'numeric'},
		{name:'estado', type: 'string'},
		{name:'id_estado_wf', type: 'numeric'},
		{name:'id_proceso_wf', type: 'numeric'},
		{name:'obs', type: 'string'},
		{name:'id_cuenta_bancaria', type: 'numeric'},
		{name:'id_depto_lb', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_depto_conta', type: 'string'},
		{name:'desc_funcionario', type: 'string'},
		{name:'desc_cuenta_bancaria', type: 'string'},
		{name:'desc_depto_lb', type: 'string'},
		{name:'id_moneda', type: 'numeric'},
		{name:'id_proveedor', type: 'numeric'},
		{name:'desc_moneda', type: 'string'},
		{name:'desc_proveedor', type: 'string'},
		{name:'id_funcionario_pago', type: 'numeric'},
		{name:'id_tipo_cobro_simple', type: 'numeric'},
		{name:'desc_funcionario_pago', type: 'string'},
		{name:'desc_tipo_cobro_simple', type: 'string'},
		{name:'codigo_tipo_cobro_simple', type: 'string'},		
		{name:'importe', type: 'numeric'},
		{name:'id_obligacion_pago', type: 'numeric'},
		{name:'desc_obligacion_pago', type: 'string'},
		{name:'id_caja', type: 'numeric'},
		{name:'desc_caja', type: 'string'},
		{name:'id_gestion', type: 'numeric'},
		{name:'id_periodo', type: 'numeric'},'tipo_cambio',
        'tipo_cambio_mt',
        'tipo_cambio_ma',
        'id_config_cambiaria',
        'importe_mt',
        'importe_mb',
        'importe_ma',
        'forma_cambio'
		
		
		
	],
	sortInfo:{
		field: 'id_cobro_simple',
		direction: 'DESC'
	},
	bdel:true,
	bsave:true,
	antEstado: function(res){
		var rec=this.sm.getSelected(),
			obsValorInicial;

		Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/AntFormEstadoWf.php',
			'Estado de Wf',
			{   modal: true,
			    width: 450,
			    height: 250
			}, 
			{    data: rec.data, 
				 estado_destino: res.argument.estado,
			     obsValorInicial: obsValorInicial }, this.idContenedor,'AntFormEstadoWf',
			{
			    config:[{
			              event:'beforesave',
			              delegate: this.onAntEstado,
			            }],
			   scope:this
			});
	},
	sigEstado: function(){
		var me = this,
     	    configExtra = [],
     		obsValorInicial,
     		rec=this.sm.getSelected();

     	this.eventosExtra = function(obj){};

     	if(rec.data.estado == 'vbtesoreria'){
     		configExtra = [
  				{     
					config:{
						name:'id_depto_lb',
						origen:'DEPTO',
						fieldLabel: 'Departamento Libro Bancos',
						url: '../../sis_parametros/control/Depto/listarDepto',
						emptyText : 'Departamento Libro Bancos...',
						allowBlank:false,
						anchor: '80%',
						baseParams: { tipo_filtro: 'DEPTO_UO', estado:'activo', codigo_subsistema:'TES', modulo:'LB', id_depto_origen: rec.data.id_depto}
					},   
					type:'ComboRec',
					form:true
				},
				{
					config:{
						name: 'id_cuenta_bancaria',
						fieldLabel: 'Cuenta Bancaria',
						allowBlank: false,
						emptyText:'Elija una Cuenta...',
						store:new Ext.data.JsonStore(
							{
							url: '../../sis_tesoreria/control/CuentaBancaria/listarCuentaBancariaUsuario',
							id: 'id_cuenta_bancaria',
							root:'datos',
							sortInfo:{
								field:'id_cuenta_bancaria',
								direction:'ASC'
							},
							totalProperty:'total',
							baseParams: {'tipo_interfaz':me.tipo_interfaz, id_moneda: rec.data.id_moneda},
							fields: [ 'id_cuenta_bancaria','nro_cuenta','nombre_institucion','codigo_moneda','centro','denominacion'],
							remoteSort: true }),
						tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>Moneda: {codigo_moneda}, {nombre_institucion}</p><p>{denominacion}, Centro: {centro}</p></div></tpl>',
						valueField: 'id_cuenta_bancaria',
						hiddenValue: 'id_cuenta_bancaria',
						displayField: 'nro_cuenta',
						disabled : true,
						listWidth:'280',
						forceSelection:true,
						typeAhead: false,
						triggerAction: 'all',
						lazyRender:true,
						mode:'remote',
						pageSize:20,
						queryDelay:500,
						anchor: '80%',
						minChars:2
				   },
					type:'ComboBox',
					form:true
				}
			];

			this.eventosExtra = function(obj){
				obj.Cmp.id_depto_lb.on('select',function(data,rec,ind){
					
			        this.Cmp.id_cuenta_bancaria.enable();
			        this.Cmp.id_cuenta_bancaria.reset();

		    		Ext.apply(this.Cmp.id_cuenta_bancaria.store.baseParams, {'tipo_interfaz':me.tipo_interfaz, par_filtro :'nro_cuenta', 'permiso':'fondos_avance', id_depto_lb : obj.Cmp.id_depto_lb.getValue()});
		    		this.Cmp.id_cuenta_bancaria.modificado = true;
		
				}, obj);	
	
			};



     	}

		this.objWizard = Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/FormEstadoWf.php',
            'Estado de Wf',
            {
                modal: true,
                width: 700,
                height: 450
            }, 
            {
            	configExtra: configExtra,
            	eventosExtra: this.eventosExtra,
            	data: {
                   id_estado_wf: rec.data.id_estado_wf,
                   id_proceso_wf: rec.data.id_proceso_wf,
                   id_cobro_simple: rec.data.id_cobro_simple,
                   fecha_ini: rec.data.fecha
               },
               obsValorInicial: obsValorInicial,
            }, this.idContenedor, 'FormEstadoWf',
            {
                config:[{
                          event:'beforesave',
                          delegate: this.onSaveWizard,
                          
                        },
                        {
                          event:'requirefields',
                          delegate: function () {
	                          	this.onButtonEdit();
					        	this.window.setTitle('Registre los campos antes de pasar al siguiente estado');
					        	this.formulario_wizard = 'si';
                          }
                          
                        }],
              
                scope:this
	    }); 
	},
	addBotonesGantt: function() {
        this.menuAdqGantt = new Ext.Toolbar.SplitButton({
            id: 'b-diagrama_gantt-' + this.idContenedor,
            text: 'Gantt',
            disabled: true,
            grupo:[0,1,2,3],
            iconCls : 'bgantt',
            handler:this.diagramGanttDinamico,
            scope: this,
            menu:{
	            items: [{
	                id:'b-gantti-' + this.idContenedor,
	                text: 'Gantt Imagen',
	                tooltip: '<b>Muestra un reporte gantt en formato de imagen</b>',
	                handler:this.diagramGantt,
	                scope: this
	            }, {
	                id:'b-ganttd-' + this.idContenedor,
	                text: 'Gantt Dinámico',
	                tooltip: '<b>Muestra el reporte gantt facil de entender</b>',
	                handler:this.diagramGanttDinamico,
	                scope: this
	            }]
            }
        });
		this.tbar.add(this.menuAdqGantt);
    },
    diagramGantt: function (){			
		var data=this.sm.getSelected().data.id_proceso_wf;
		Phx.CP.loadingShow();
		Ext.Ajax.request({
			url:'../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
			params:{'id_proceso_wf':data},
			success: this.successExport,
			failure: this.conexionFailure,
			timeout: this.timeout,
			scope: this
		});			
	},
	
	diagramGanttDinamico: function (){			
		var data=this.sm.getSelected().data.id_proceso_wf;
		window.open('../../../sis_workflow/reportes/gantt/gantt_dinamico.html?id_proceso_wf='+data)		
	},

	preparaMenu: function(n) {

		var data = this.getSelectedData();
		var tb = this.tbar;
		Phx.vista.CobroSimple.superclass.preparaMenu.call(this, n);

		this.getBoton('ant_estado').disable();
		this.getBoton('sig_estado').disable();

		//Si está en modo histórico,no habilita ninguno de los botones que generan transacciones
		if(this.historico=='no'){
			
			
			if(data.estado == 'borrador') {			
				
				this.getBoton('sig_estado').enable();
			} else if(data.estado == 'finalizado'){
				this.getBoton('sig_estado').disable();
				this.getBoton('del').disable();
				this.getBoton('edit').disable();
			} else if(data.estado == 'pendiente'||data.estado == 'vbtesoreria'||data.estado == 'pendiente_pago'){
				this.getBoton('sig_estado').disable();
				this.getBoton('ant_estado').disable();
				this.getBoton('del').disable();
				this.getBoton('edit').disable();
			} else {
				this.getBoton('ant_estado').enable();
				this.getBoton('sig_estado').enable();
				this.getBoton('del').enable();
				this.getBoton('edit').enable();
			}	

			 
		}

		//Habilita el resto de los botones
        this.getBoton('diagrama_gantt').enable();
        this.getBoton('btnObs').enable();
        this.getBoton('btnChequeoDocumentosWf').enable();

       
		//agregado
        this.getBoton('btnDetalleDocumentoCobroSimple').enable();

        
		return tb
	},

		
	liberaMenu: function() {
		var tb = Phx.vista.CobroSimple.superclass.liberaMenu.call(this);
		if (tb) {
			this.getBoton('sig_estado').disable();
			this.getBoton('ant_estado').disable();
            this.getBoton('diagrama_gantt').disable();
            this.getBoton('btnObs').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            
            
            this.getBoton('btnDetalleDocumentoCobroSimple').disable();
              
		}
		return tb
	},
	loadCheckDocumentosSolWf:function() {
        var rec=this.sm.getSelected();
        rec.data.nombreVista = this.nombreVista;
        Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
            'Documentos del Proceso',
            {
                width:'90%',
                height:500
            },
            rec.data,
            this.idContenedor,
            'DocumentoWf'
        )
    },
    onOpenObs:function() {
        var rec=this.sm.getSelected();            
        var data = {
        	id_proceso_wf: rec.data.id_proceso_wf,
        	id_estado_wf: rec.data.id_estado_wf,
        	num_tramite: rec.data.num_tramite
        }
        
        Phx.CP.loadWindows('../../../sis_workflow/vista/obs/Obs.php',
                'Observaciones del WF',
                {
                    width: '80%',
                    height: '70%'
                },
                data,
                this.idContenedor,
                'Obs');
    },
    onSaveWizard:function(wizard,resp){
        Phx.CP.loadingShow();
        Ext.Ajax.request({
            url:'../../sis_cobros/control/CobroSimple/siguienteEstado',
            params:{
            	    id_cobro_simple:     wizard.data.id_cobro_simple,
            	    id_proceso_wf_act:  resp.id_proceso_wf_act,
	                id_estado_wf_act:   resp.id_estado_wf_act,
	                id_tipo_estado:     resp.id_tipo_estado,
	                id_funcionario_wf:  resp.id_funcionario_wf,
	                id_depto_wf:        resp.id_depto_wf,
	                obs:                resp.obs,
	                instruc_rpc:		resp.instruc_rpc,
	                json_procesos:      Ext.util.JSON.encode(resp.procesos),
	                id_depto_lb:  		resp.id_depto_lb,
	                id_cuenta_bancaria: resp.id_cuenta_bancaria
                },
            success: this.successWizard,
            failure: this.conexionFailure, 
            argument: { wizard:wizard },
            timeout: this.timeout,
            scope: this
        });
    },
    successWizard:function(resp){
        Phx.CP.loadingHide();
        resp.argument.wizard.panel.destroy()
        this.reload();
    },
    onAntEstado: function(wizard,resp){
        Phx.CP.loadingShow();
        var operacion = 'cambiar';

        Ext.Ajax.request({
            url:'../../sis_cobros/control/CobroSimple/anteriorEstado',
            params:{
                id_proceso_wf: resp.id_proceso_wf,
                id_estado_wf:  resp.id_estado_wf,  
                obs: resp.obs,
                operacion: operacion,
                id_cobro_simple: resp.data.id_cobro_simple
             },
            argument:{wizard:wizard},  
            success: this.successAntEstado,
            failure: this.conexionFailure,
            timeout: this.timeout,
            scope: this
        });
    },
    successAntEstado:function(resp){
        Phx.CP.loadingHide();
        resp.argument.wizard.panel.destroy()
        this.reload();
    },
    
    
    tabsouth: [{
		url: '../../../sis_cobros/vista/cobro_simple_det/CobroSimpleDet.php',
        title: 'Facturas/Recibos',
        height: '40%',
        cls: 'CobroSimpleDet'
    }],
    
  
    iniciarEventos: function(){
    	this.Cmp.id_tipo_cobro_simple.on('select',function(combo,record,index){
    		this.manejoComponentes(record.data.codigo, 'si');
    	},this);
    	
    	
    	this.Cmp.id_moneda.on('select', function(){
												this.getConfigCambiaria('si');
											}, this);
											
											
        this.Cmp.fecha.on('select', function(value, date){	
        	this.getConfigCambiaria('si') ;
        }, this);
			
			
	    this.Cmp.forma_cambio.on('select', function(){ this.getConfigCambiaria('si') }, this);
    },
    
    manejoComponentes : function (codigo,reset) {
    	//Oculta os componentespor defecto,luego en funcion del caso se los habilita/deshabilita
    	this.Cmp.id_proveedor.setDisabled(true);
		this.Cmp.id_proveedor.allowBlank=true;
		this.Cmp.id_funcionario_pago.setDisabled(true);
		this.Cmp.id_funcionario_pago.allowBlank=true;		
		this.Cmp.id_obligacion_pago.hide();
		this.Cmp.id_obligacion_pago.allowBlank=true;
		this.Cmp.id_caja.hide();
		this.Cmp.id_caja.allowBlank=true;

    	if (reset == 'si') {
			this.Cmp.id_proveedor.setValue('');
			this.Cmp.id_proveedor.selectedIndex=-1;
			this.Cmp.id_funcionario_pago.setValue('');
			this.Cmp.id_funcionario_pago.selectedIndex=-1;
			this.Cmp.id_obligacion_pago.allowBlank=true;
			this.Cmp.id_obligacion_pago.selectedIndex=-1;
			this.Cmp.id_obligacion_pago.setValue('');
			this.Cmp.id_caja.selectedIndex=-1;
			this.Cmp.id_caja.setValue('');
		} 
	
		if(codigo == 'PAG_PRO' || codigo == 'SOLO_PAG'){
			this.Cmp.id_proveedor.setDisabled(false);
			this.Cmp.id_proveedor.allowBlank=false;
			if(codigo == 'SOLO_PAG'){
				this.Cmp.importe.setVisible(true);
			     this.Cmp.importe.allowBlank=false;
			}
			
		} else if(codigo == 'PAG_FUN'){
			this.Cmp.id_funcionario_pago.setDisabled(false);
			this.Cmp.id_funcionario_pago.allowBlank=false;
		} else if(codigo == 'PAG_DEV'){
			this.Cmp.id_proveedor.setDisabled(false);
			this.Cmp.id_proveedor.allowBlank=false;    			
			this.Cmp.importe.setVisible(true);
			this.Cmp.importe.allowBlank=false;
			this.Cmp.id_obligacion_pago.show();
			this.Cmp.id_obligacion_pago.allowBlank=false;
		} else if(codigo == 'ADU_GEST_ANT'){
			this.Cmp.id_proveedor.setDisabled(false);
			this.Cmp.id_proveedor.allowBlank=false;    			
			this.Cmp.importe.setVisible(true);
			this.Cmp.importe.allowBlank=false;
			this.Cmp.id_obligacion_pago.hide();
			this.Cmp.id_obligacion_pago.allowBlank=true;
		} else if(codigo == 'PAG_CAJ'){
			this.Cmp.id_proveedor.setDisabled(false);
			this.Cmp.id_proveedor.allowBlank=false;    			
			this.Cmp.importe.setVisible(false);
			this.Cmp.importe.allowBlank=true;
			this.Cmp.id_obligacion_pago.hide();
			this.Cmp.id_obligacion_pago.allowBlank=true;
			this.Cmp.id_caja.show();
			this.Cmp.id_caja.allowBlank=false;
		} else {
			this.Cmp.id_proveedor.setDisabled(false);
			this.Cmp.id_proveedor.allowBlank=false;
		}
    },
    onButtonEdit: function(){
    	var rec=this.sm.getSelected();
    	
    	Phx.vista.CobroSimple.superclass.onButtonEdit.call(this);     	
    	this.manejoComponentes(rec.data.codigo_tipo_cobro_simple,'no');  	
    	
    }, 
    
    getConfigCambiaria : function(sw_valores) {

			var localidad = 'nacional';
			
			if (this.swButton == 'EDIT') {
				var rec = this.sm.getSelected();
				localidad = rec.data.localidad;
				
			}

			//Verifica que la fecha y la moneda hayan sido elegidos
			if (this.Cmp.fecha.getValue() && this.Cmp.id_moneda.getValue() && this.Cmp.forma_cambio.getValue()) {
				Phx.CP.loadingShow();
				var forma_cambio = this.Cmp.forma_cambio.getValue();
				if(forma_cambio=='convenido'){
					this.Cmp.tipo_cambio.setReadOnly(false);
					this.Cmp.tipo_cambio_mt.setReadOnly(false);
				}
				else{
					this.Cmp.tipo_cambio.setReadOnly(true);
					this.Cmp.tipo_cambio_mt.setReadOnly(true);
				}
				this.Cmp.tipo_cambio_ma.setReadOnly(true);
				
				
				Ext.Ajax.request({
				url:'../../sis_contabilidad/control/ConfigCambiaria/getConfigCambiaria',
				params:{
					fecha: this.Cmp.fecha.getValue(),
					id_moneda: this.Cmp.id_moneda.getValue(),
					localidad: localidad,
					sw_valores: sw_valores,
					forma_cambio: forma_cambio
				}, success: function(resp) {
					Phx.CP.loadingHide();
					var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
					if (reg.ROOT.error) {
						this.Cmp.tipo_cambio.reset();
						this.Cmp.tipo_cambio_mt.reset();
						this.Cmp.tipo_cambio_ma.reset();
						Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
					} else {
						
						//cambia labels
						
						this.Cmp.tipo_cambio.label.update(reg.ROOT.datos.v_tc1 +' (tc)');
						this.Cmp.tipo_cambio_mt.label.update(reg.ROOT.datos.v_tc2 +' (tc)');
						this.Cmp.tipo_cambio_ma.label.update(reg.ROOT.datos.v_tc3 +' (tc)');
						if (sw_valores == 'si'){
						    //poner valores por defecto
						 	this.Cmp.tipo_cambio.setValue(reg.ROOT.datos.v_valor_tc1);
						    this.Cmp.tipo_cambio_mt.setValue(reg.ROOT.datos.v_valor_tc2);
						    this.Cmp.tipo_cambio_ma.setValue(reg.ROOT.datos.v_valor_tc3);
						}
						
					   
					   this.Cmp.id_config_cambiaria.setValue(reg.ROOT.datos.id_config_cambiaria);
					}
					

				}, failure: function(a,b,c,d){
					this.Cmp.tipo_cambio.reset();
					this.Cmp.tipo_cambio_mt.reset();
					this.Cmp.tipo_cambio_ma.reset();
					this.conexionFailure(a,b,c,d)
				},
				timeout: this.timeout,
				scope:this
				});
			}

		},	
    
    

})
</script>