<?php
/**
*@package pXP
*@file gen-Factura.php
*@author  (admin)
*@date 18-08-2015 15:57:09
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 * ISSUE				FECHA			AUTHOR		  DESCRIPCION
 * 1B				17/08/2018			EGS				se hizo cambios para cobros regularizados y retencion de garantias , se movio y se habilito columnas
 * 1C				20/09/2018			EGS				se aumento codigo para el campo id_contrato
 * #3               12/09/2019          EGS             e habilito exportador de grilla
 * #7               16/06/2020          EGS             Se agrega filtro para reporte de facturas completas
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Factura = Ext.extend(Phx.gridInterfaz,{
	
    fheight: '80%',
    fwidth: '70%',
    tabEnter: true,
    tipoDoc: 'venta',
    regitrarDetalle: 'si',
    nombreVista: 'Factura',
    constructor:function(config){
    	this.maestro=config.maestro;
		var me = this;
		me.configurarAtributos(me);
		
			//llama al constructor de la clase padre
		Phx.vista.Factura.superclass.constructor.call(this,config);
        //Botón para Imprimir el Comprobante
		/*
		this.addButton('btnImprimirR', {
				text : 'Imprimir',
				iconCls : 'bprint',
				disabled : false,
				handler : this.imprimirReporte,
				tooltip : '<b>Imprimir Reporte PDF</b><br/>Imprime la Factura con sus Cobros'
		});
		*/
		
		
		
          this.addButton('btnImprimir', {
               // id: 'imprimir',
                iconCls: 'bexcel',//'bpdf',
                grupo: this.pdfGroups,
                tooltip: '<b>Exporta</b> Exporta las facturas con sus cobros respecto el criterio',
                text: 'Exporta',
                xtype: 'splitbutton',
                //handler: this.onButtonExcel,
                argument: {
                    'news': true,
                    def: 'reset'
                },
                scope: this,
                menu: [{
                    text: 'Factura',
                    iconCls: 'bexport',
                    argument: {
                        'news': true,
                        def: 'csv'
                    },
                    
                    scope: this,
                      menu: [{
			                    text: 'Pdf',
			                    iconCls: 'bpdf',
			                    argument: {
			                        'news': true,
			                        def: 'csv'
			                    },
			                    handler: this.imprimirReporte,
			                    scope: this,
			                    
			                }, {
			                    text: 'Excel',
			                    iconCls: 'bexcel',
			                    argument: {
			                        'news': true,
			                        def: 'pdf'
			                    },
			                    handler: this.imprimirReporteExcel,
			                    scope: this
			                }],
                    
                }, {
                    text: 'Cliente',
                    iconCls: 'bexport',
                    argument: {
                        'news': true,
                        def: 'pdf'
                    },
                    
                    scope: this,
		               menu: [{
		                    text: 'Pdf',
		                    iconCls: 'bpdf',
		                    argument: {
		                        'news': true,
		                        def: 'csv'
		                    },
		                    handler: this.imprimirReporteCliente,
		                    scope: this
		                }, {
		                    text: 'Excel',
		                    iconCls: 'bexcel',
		                    argument: {
		                        'news': true,
		                        def: 'pdf'
		                    },
		                    handler: this.imprimirReporteClienteExcel,
		                    scope: this
		                }],
                }]
                });
                
                this.addButton('btnImprimirTodoCliente', {
                id: 'todo',
                iconCls: 'bexcel',//'bpdf',
                grupo: this.pdfGroups,
                tooltip: '<b>Clientes</b> Imprime todo los clientes con importes y saldos totales',
                text: 'Lista Todos los Clientes',
                xtype: 'splitbutton',
                //handler: this.onButtonExcel,
                argument: {
                    'news': true,
                    def: 'reset'
                },
               // handler: this.imprimirReporteTodoCliente,
                scope: this,
                menu: [{
		                    text: 'Pdf',
		                    iconCls: 'bpdf',
		                    argument: {
		                        'news': true,
		                        def: 'csv'
		                    },
		                    handler: this.imprimirReporteTodoCliente,
		                    scope: this
		                }, {
		                    text: 'Excel',
		                    iconCls: 'bexcel',
		                    argument: {
		                        'news': true,
		                        def: 'pdf'
		                    },
		                    handler: this.imprimirReporteTodoClienteExcel,
		                    scope: this
		                }],
                }),
                
                ////EGS-I-17/08/2018///////////
                    /*
                this.addButton('btnImprimirTodoFactura', {
                id: 'todo2',
                iconCls: 'bexcel',//'bpdf',
                grupo: this.pdfGroups,
                tooltip: '<b>Todas las facturas</b> Imprime todas las facturas existentes',
                text: 'Lista Todas las Facturas',
                xtype: 'splitbutton',
                //handler: this.onButtonExcel,
                argument: {
                    'news': true,
                    def: 'reset'
                },
               // handler: this.imprimirReporteTodoCliente,
                scope: this,
                menu: [{
		                    text: 'Pdf',
		                    iconCls: 'bpdf',
		                    argument: {
		                        'news': true,
		                        def: 'csv'
		                    },
		                    handler: this.imprimirReporteTodoFactura,
		                    scope: this
		                }, {
		                    text: 'Excel',
		                    iconCls: 'bexcel',
		                    argument: {
		                        'news': true,
		                        def: 'pdf'
		                    },
		                    handler: this.imprimirReporteTodoFacturaExcel,
		                    scope: this
		                }],
                }),*/
               ////EGS-F-17/08/2018///////////
            

		
		//this.iniciarEventos();
		this.init();
        this.reporteCobro();// #7
		this.obtenerVariableGlobal();
		//this.load({params:{start:0, limit:this.tam_pag}});
	},
	
	Atributos1:[],
	
	configurarAtributos: function(me){
		this.Atributos2 = [],	
		this.Atributos2 = [
		
			{
				//configuracion del componente
				config:{
						labelSeparator:'',
						inputType:'hidden',
						name: 'id',
						gwidth: 40,
				},
				type:'Field',
				form:true ,
				grid:false,
				bottom_filter: false,
				filters: {pfiltro:'id',type:'numeric'}
			},
						{
				config:{
					name: 'fecha',
					fieldLabel: 'Fecha Factura',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					format: 'd/m/Y',
					readOnly:true,
					renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
				},
					type:'DateField',
					filters:{pfiltro:'dcv.fecha',type:'date'},
					id_grupo:0,
					bottom_filter:false,
					grid:true,
					form:false
			},
			
			{
				config:{
					name: 'nro_documento',
					fieldLabel: 'Nro Fact/Doc ',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					maxLength:100
				},
					type:'TextField',
					filters:{pfiltro:'dcv.nro_documento',type:'string'},
					id_grupo:0,
					grid:true,
					bottom_filter: true,
					form:false
			},	
			
			{
				config:{
					name: 'razon_social',
					fieldLabel: 'Razón Social',
					allowBlank: false,
					//maskRe: /[A-Za-z0-9 ]/,
	                //fieldStyle: 'text-transform:uppercase',
					style:'text-transform:uppercase;',
					renderer:function (value,p,record){
						if(record.data.codigo_aplicacion == ''){
							return  String.format('<font color="red">{0}</font>',  value);
						}
						return  String.format('<font color="green"><b>{0}</b></font>',  value);
						
					 },
					 listeners:{
				          'change': function(field, newValue, oldValue){
				          			  console.log('keyup ...  ')
				          			  field.suspendEvents(true);
				                      field.setValue(newValue.toUpperCase());
				                      field.resumeEvents(true);
				                  }
				     },
					anchor: '80%',
					gwidth: 100,
					maxLength:180
				},
					type:'TextField',
					filters:{pfiltro:'dcv.razon_social',type:'string'},
					id_grupo:0,
					grid:true,
					bottom_filter: true,
					form:false
			},
			     {
	            config:{
	                name: 'nit',
	                fieldLabel: 'NIT',
	                qtip: 'Número de indentificación del proveedor',
	                allowBlank: false,
	                emptyText:'nit ...',
	                store:new Ext.data.JsonStore(
	                {
	                    url: '../../sis_contabilidad/control/DocCompraVenta/listarNroNit',
	                    id: 'nit',
	                    root:'datos',
	                    sortInfo:{
	                        field:'nit',
	                        direction:'ASC'
	                    },
	                    totalProperty:'total',
	                    fields: ['nit','razon_social'],
	                    remoteSort: true
	                }),
	                valueField: 'nit',
	                hiddenValue: 'nit',
	                displayField: 'nit',
	                gdisplayField:'nit',
	                queryParam: 'nit',
	                listWidth:'280',
	                forceSelection:false,
	                autoSelect: false,
	                typeAhead: false,
	                typeAheadDelay: 75,
	                hideTrigger:true,
	                triggerAction: 'query',
	                lazyRender:false,
	                mode:'remote',
	                pageSize:20,
	                queryDelay:500,
	                gwidth: 100,
	                minChars:1
	            },
	            type:'ComboBox',
	            filters:{pfiltro:'dcv.nit',type:'string'},
	            id_grupo: 0,
	            grid: true,
	            bottom_filter: true,
	            form: false
	        },
			{
				config:{
					name: 'importe_doc',
					fieldLabel: 'Importe Factura',
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
					filters:{pfiltro:'dcv.importe_doc',type:'numeric'},
					id_grupo:0,
					bottom_filter: true,
					grid:true,
					form:false
			},
				
			
			{
	            config:{
	                name:'id_moneda',
	                origen:'MONEDA',
	                allowBlank:false,
	                fieldLabel:'Moneda',
	                gdisplayField:'desc_moneda',//mapea al store del grid
	                gwidth:70,
	                width:250,
	                renderer:function (value, p, record){return String.format('{0}', record.data['desc_moneda']);}
	             },
	            type:'ComboRec',
	            id_grupo:0,
	            filters:{   
	                pfiltro:'incbte.desc_moneda',
	                type:'string'
	            },
	            grid:true,
	            form:false
	        },
	       //I- EGS 1B 24/08/2018
	       			{
				config:{
					name: 'importe_pago_liquido',
					//fieldLabel: 'Liquido Pagado',
					fieldLabel: 'Pago Anterior',
					allowBlank: true,
					readOnly:true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_pago_liquido',type:'numeric'},
					id_grupo:1,
					
					grid:true,
					form: false
			},
	        {
				config:{
					name: 'importe_pendiente',
					fieldLabel: 'Cuenta Pendiente',
					qtip: 'Usualmente una cuenta pendiente de  cobrar o  pagar (dependiendo si es compra o venta), posterior a la emisión del documento',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_pendiente',type:'numeric'},
					id_grupo:1,
					
					grid:true,
					form:false
			},
	        	{
				config:{
					name: 'importe_retgar',
					fieldLabel: 'Ret. Garantia',
					qtip: 'Importe retenido por garantia',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_retgar',type:'numeric'},
					id_grupo:1,
					
					grid:true,
					form:false
			},	
			
			    {
				config:{
					name: 'importe_anticipo',
					fieldLabel: 'Anticipo',
					qtip: 'Importe Anticipo',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_anticipo',type:'numeric'},
					id_grupo:1,
					
					grid:true,
					form:false
			},	
	        
	        
			//F-EGS 1B 24/08/2018
			{
				config:{
					name: 'id_cobro_simple',
					fieldLabel: 'Cobro Simple',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					maxLength:100
				},
					type:'TextField',
					filters:{pfiltro:'ic.nro_tramite',type:'string'},
					id_grupo:0,
					grid:false,
					bottom_filter: false,
					form:false
			},
			
			
			 
		
			{
				config:{
					name: 'importe_total_cobrado_mb',
					fieldLabel: 'Total cobrado MB',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',//EGS 1B 24/08/2018
					maxLength:100,
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
					type:'TextField',
					filters:{pfiltro:'(COALESCE(doc.importe_mb,0)+ COALESCE(docrg.importe_mb,0)+ COALESCE(docanti.importe_mb,0))',type:'numeric'},
					id_grupo:0,
					grid:true,
					bottom_filter: false,
					form:false
			},	
			
			
				{
				config:{
					name: 'importe_total_cobrado_mt',
					fieldLabel: 'importe_total_cobrado_mt',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',//EGS 1B 24/08/2018
					maxLength:100
				},
					type:'TextField',
					filters:{pfiltro:'importe_total_cobrado_mt',type:'string'},
					id_grupo:0,
					grid:false,
					bottom_filter: false,
					form:false
			},
				{
				config:{
					name: 'saldo_por_cobrar',
					fieldLabel: 'Saldo por Cobrar',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',/////EGS 1B 24/08/2018
					maxLength:100,
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
					type:'TextField',
					filters:{pfiltro:'COALESCE(dcv.importe_pendiente,0) + COALESCE(dcv.importe_retgar,0)+ COALESCE(dcv.importe_anticipo,0) -  (COALESCE(doc.importe_mb,0)+ COALESCE(docrg.importe_mb,0)+ COALESCE(docanti.importe_mb,0))',type:'numeric'},
					id_grupo:0,
					grid:true,
					bottom_filter: false,
					form:false
			},
			
			
			
			
			
			{
				//configuracion del componente
				config:{
						labelSeparator:'',
						inputType:'hidden',
						name: 'id_doc_compra_venta',
						gwidth: 40,
				},
				type:'Field',
				form:true ,
				grid:false,
				bottom_filter: false,
				filters: {pfiltro:'dcv.id_doc_compra_venta',type:'numeric'}
			},
			
			{
				//configuracion del componente
				config:{
						labelSeparator:'',
						inputType:'hidden',
						name: 'tipo'
				},
				
				type:'Field',
				form:true 
			},
	        {
	            //configuracion del componente
	            config:{
	                    labelSeparator:'',
	                    inputType:'hidden',
	                    name: 'porc_descuento_ley',
	                    allowDecimals: true,
	                    decimalPrecision: 10
	            },
	            type:'NumberField',
	            form:true 
	        },
	        {
	            //configuracion del componente
	            config:{
	                    labelSeparator:'',
	                    inputType:'hidden',
	                    name: 'porc_iva_cf',
	                    allowDecimals: true,
	                    decimalPrecision: 10
	            },
	            type:'NumberField',
	            form:true 
	        },
	        {
	            //configuracion del componente
	            config:{
	                    labelSeparator:'',
	                    inputType:'hidden',
	                    name: 'porc_iva_df',
	                    allowDecimals: true,
	                    decimalPrecision: 10
	            },
	            type:'NumberField',
	            form:true 
	        },
	        {
	            //configuracion del componente
	            config:{
	                    labelSeparator:'',
	                    inputType:'hidden',
	                    name: 'porc_it',
	                    allowDecimals: true,
	                    decimalPrecision: 10
	            },
	            type:'NumberField',
	            form:true 
	        },
	        {
	            //configuracion del componente
	            config:{
	                    labelSeparator:'',
	                    inputType:'hidden',
	                    name: 'porc_ice',
	                    allowDecimals: true,
	                    decimalPrecision: 10
	            },
	            type:'NumberField',
	            form:true 
	        },
	        
	        {
				//configuracion del componente
				config:{
						labelSeparator:'',
						inputType:'hidden',
						name: 'id_depto_conta'
				},
				type:'Field',
				form:true 
			},
			{
				config:{
					name: 'revisado',
					fieldLabel: 'Revisado',
					allowBlank: true,
					anchor: '80%',
					gwidth: 80,
					maxLength:3,
	                renderer: function (value, p, record, rowIndex, colIndex){  
	                	     
	            	       //check or un check row
	            	       var checked = '',
	            	           state = '',
	            	       	   momento = 'no';
	                	   if(value == 'si'){
	                	      checked = 'checked';
	                	   }
	                	   if(record.data.id_int_comprobante){
	                	      state = 'disabled';
	                	   }
	                	   if(record.data.tipo_reg != 'summary'){
	            	         return  String.format('<div style="vertical-align:middle;text-align:center;"><input style="height:37px;width:37px;" type="checkbox"  {0} {1}></div>',checked, state);
	            	       }
	            	       else{
	            	       	  return '';
	            	       } 
	                 }
				},
				type: 'TextField',
				filters: { pfiltro:'dcv.revisado',type:'string'},
				id_grupo: 1,
				grid: false,
				form: false
			},
	   			
			
	        {
	            config:{
	                name: 'nro_autorizacion',
	                fieldLabel: 'Autorización',
	                allowBlank: false,
	                emptyText:'autorización ...',
	                store:new Ext.data.JsonStore(
	                {
	                    url: '../../sis_contabilidad/control/DocCompraVenta/listarNroAutorizacion',
	                    id: 'nro_autorizacion',
	                    root:'datos',
	                    sortInfo:{
	                        field:'nro_autorizacion',
	                        direction:'ASC'
	                    },
	                    totalProperty:'total',
	                    fields: ['nro_autorizacion','nit','razon_social'],
	                    remoteSort: true
	                }),
	                valueField: 'nro_autorizacion',
	                hiddenValue: 'nro_autorizacion',
	                displayField: 'nro_autorizacion',
	                gdisplayField:'nro_autorizacion',
	                queryParam: 'nro_autorizacion',
	                listWidth:'280',
	                forceSelection:false,
	                autoSelect: false,
	                hideTrigger:true,
	                typeAhead: false,
	                typeAheadDelay: 75,
	                //triggerAction: 'query',
	                lazyRender:false,
	                mode:'remote',
	                pageSize:20,
	                queryDelay:500,
	                gwidth: 150,
	                minChars:1
	            },
	            type:'ComboBox',
	            filters:{pfiltro:'dcv.nro_autorizacion',type:'string'},
	            id_grupo: 0,
	            grid: false,
	            bottom_filter: false,
	            form: false
	        },			
		
				
			{
				config:{
					name: 'codigo_control',
					fieldLabel: 'Código de Control',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					maxLength:200
				},
					type:'TextField',
					filters:{pfiltro:'dcv.codigo_control',type:'string'},
					id_grupo:0,
					grid:false,
					form:false
			},			
			
			{
				config:{
					name: 'importe_excento',
					fieldLabel: 'Exento',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type: 'NumberField',
					filters: {pfiltro:'dcv.importe_excento',type:'numeric'},
					id_grupo:1,
					
					grid: false,
					form: false
			},			
			{
				config:{
					name: 'importe_descuento',
					fieldLabel: 'Descuento',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_descuento',type:'numeric'},
					id_grupo:1,
					
					grid:false,
					form:false
			},
			{
				config:{
					name: 'importe_neto',
					fieldLabel: 'Importe c/d',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					maxLength:1179650,
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_doc',type:'numeric'},
					id_grupo:1,
					grid:false,
					form:false
			},	
			{
				config:{
					name: 'importe_aux_neto',
					fieldLabel: 'Neto',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					maxLength:1179650,
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00') );
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					id_grupo:1,
					
					grid:false,
					form:false
			},			
			{
				config:{
					name: 'importe_iva',
					fieldLabel: 'IVA',
					allowBlank: true,
					readOnly:true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type: 'NumberField',
					filters: { pfiltro:'dcv.importe_iva',type:'numeric'},
					id_grupo: 1,
					
					grid: false,
					form: false
			},			
			{
				config:{
					name: 'importe_pago_liquido',
					//fieldLabel: 'Liquido Pagado',
					fieldLabel: 'Pago Anterior',
					allowBlank: true,
					readOnly:true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_pago_liquido',type:'numeric'},
					id_grupo:1,
					
					grid:false,
					form: true
			},			
		   {
	            config:{
	                name: 'id_plantilla',
	                fieldLabel: 'Tipo Documento',
	                allowBlank: false,
	                emptyText:'Elija una plantilla...',
	                store:new Ext.data.JsonStore(
	                {
	                    url: '../../sis_parametros/control/Plantilla/listarPlantilla',
	                    id: 'id_plantilla',
	                    root:'datos',
	                    sortInfo:{
	                        field:'desc_plantilla',
	                        direction:'ASC'
	                    },
	                    totalProperty:'total',
	                    fields: ['id_plantilla','nro_linea','desc_plantilla','tipo',
	                    'sw_tesoro', 'sw_compro','sw_monto_excento','sw_descuento',
	                    'sw_autorizacion','sw_codigo_control','tipo_plantilla','sw_nro_dui','sw_ice'],
	                    remoteSort: true,
	                    baseParams:{par_filtro:'plt.desc_plantilla',sw_compro:'si',sw_tesoro:'si'}
	                }),
	                tpl:'<tpl for="."><div class="x-combo-list-item"><p>{desc_plantilla}</p></div></tpl>',
	                valueField: 'id_plantilla',
	                hiddenValue: 'id_plantilla',
	                displayField: 'desc_plantilla',
	                gdisplayField:'desc_plantilla',
	                listWidth:'280',
	                forceSelection:true,
	                typeAhead: false,
	                triggerAction: 'all',
	                lazyRender:true,
	                mode:'remote',
	                pageSize:20,
	                queryDelay:500,
	               
	                gwidth: 150,
	                minChars:2,
	                renderer:function (value, p, record){
	                	var color = 'black';
	                	if(record.data.tabla_origen != 'ninguno'){
	                		color = 'blue';
	                	}
	                	return String.format("<b><font color='{0}'>{1}</font></b>", color, record.data['desc_plantilla']);
	                }
	            },
	            type:'ComboBox',
	            filters:{pfiltro:'pla.desc_plantilla',type:'string'},
	            id_grupo: 0,
	            grid: true,
	            bottom_filter: false,
	            form: false
	        },
	          ////1C				20/09/2018			EGS	
	        		
			{
	            config:{
	                name: 'nro_contrato',
	                fieldLabel: 'Nro Contrato',
	                allowBlank: false,
	                gwidth: 100
	            },
	            type:'Field',
	            filters:{ pfiltro:'con.numero', type:'string'},
	            id_grupo: 0,
	            grid: true,
	            bottom_filter: true,
	            form: false
	       },
		
		///1C				20/09/2018			
			{
	            config:{
	                name: 'id_tipo_doc_compra_venta',
	                fieldLabel: (me.tipoDoc == 'compra')?'Tipo Compra':'Estado',
	                allowBlank: false,
	                emptyText:'tipo...',
	                store: new Ext.data.JsonStore({
	                    url: '../../sis_contabilidad/control/TipoDocCompraVenta/listarTipoDocCompraVenta',
	                    id: 'id_tipo_doc_compra_venta',
	                    root: 'datos',
	                    sortInfo: {
	                        field: 'id_tipo_doc_compra_venta',
	                        direction: 'ASC'
	                    },
	                    totalProperty: 'total',
	                    fields: ['id_tipo_doc_compra_venta','codigo','nombre','obs','tipo'],
	                    remoteSort: true,
	                    baseParams: { par_filtro:'nombre',tipo: me.tipoDoc}
	                }),
	                tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo} - {nombre}</p></div></tpl>',
	                valueField: 'id_tipo_doc_compra_venta',
	                hiddenName: 'id_tipo_doc_compra_venta',
	                editable : false,
	                displayField: 'nombre',
	                gdisplayField: 'desc_tipo_doc_compra_venta',
	                listWidth: '280',
	                forceSelection: true,
	                typeAhead:  false,
	                triggerAction: 'all',
	                lazyRender: true,
	                mode: 'remote',
	                pageSize: 20,
	                queryDelay: 500,
	               
	                gwidth: (me.tipoDoc == 'compra')?250:100,
	                minChars:2,
	                renderer: function (value, p, record){
		                	return String.format('{0}', record.data['desc_tipo_doc_compra_venta']);
	                	
	                	}
	            },
	            type:'ComboBox',
	            filters:{pfiltro:'tdcv.nombre',type:'string'},
	            id_grupo: 1,
	            egrid: false,
	            grid: false,
	            bottom_filter: false,
	            form: false
	        },
			
			{
	            config:{
	                name: 'desc_comprobante',
	                fieldLabel: 'Cbte',
	                allowBlank: false,
	                gwidth: 100
	            },
	            type:'Field',
	            filters:{ pfiltro:'ic.id_int_comprobante#ic.nro_cbte', type:'string'},
	            id_grupo: 0,
	            grid: false,
	            //bottom_filter: true,
	            form: false
	       },
		   {
				config:{
					name: 'id_int_comprobante',
					fieldLabel: 'Id Int Comprobante',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					maxLength:100
				},
					type:'TextField',
					id_grupo:0,
					grid:false,
					form:false,
					bottom_filter: false,
					filters:{pfiltro:'ic.id_int_comprobante',type:'numeric'}
			},
			{
				config:{
					name: 'nro_tramite',
					fieldLabel: 'Nro Tramite',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
					maxLength:100
				},
					type:'TextField',
					filters:{pfiltro:'ic.nro_tramite',type:'string'},
					id_grupo:0,
					grid:false,
					bottom_filter: false,
					form:false
			},
			{
				config:{
					name: 'estado_cbte',
					fieldLabel: 'Estado Cbte.',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					maxLength:10
				},
					type:'TextField',
					filters:{pfiltro:'ic.estado_reg',type:'string'},
					id_grupo:1,
					grid:false,
					form:false
			},
			{
				config:{
					name: 'fecha_cbte',
					fieldLabel: 'Fecha Cbte.',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					maxLength:10,
					format: 'd/m/Y',
					readOnly:true,
					renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
				},
					type:'TextField',
					filters:{pfiltro:'ic.fecha',type:'date'},
					id_grupo:1,
					grid:false,
					form:false
			},		
	        
			{
				config:{
					name: 'dia',
					fieldLabel: 'Dia',
					allowBlank: true,
					allowNEgative: false,
					allowDecimal: false,
					maxValue: 31,
					minValue: 1,
					width: 40,
					
					gwidth: 100
				},
					type:'NumberField',
					id_grupo:0,
					grid:false,
					form: false
			},			
			{
				config:{
					name: 'nro_dui',
					fieldLabel: 'DUI',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					maxLength :16,
					minLength:9
				},
					type:'TextField',
					filters:{pfiltro:'dcv.nro_dui',type:'string'},
					id_grupo:0,
					grid:false,
					form:false
			},			
			{
				config:{
					name: 'obs',
					fieldLabel: 'Observaciones',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					maxLength: 400
				},
					type:'TextArea',
					filters:{ pfiltro:'dcv.obs',type:'string' },
					id_grupo:0,
					grid: false,
					bottom_filter: false,
					form: false
			},
			
			
			{
				config:{
					name: 'importe_descuento_ley',
					fieldLabel: 'Descuentos de Ley',
					allowBlank: true,
					readOnly:true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_descuento_ley',type:'numeric'},
					id_grupo:1,
					
					grid:false,
					form:false
			},
			{
				config:{
					name: 'importe_ice',
					fieldLabel: 'ICE',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_ice',type:'numeric'},
					id_grupo:1,
					
					grid:false,
					form:false
			},
			{
				config:{
					name: 'importe_it',
					fieldLabel: 'IT',
					allowBlank: true,
					anchor: '80%',
					readOnly:true,
					gwidth: 100,
					galign: 'right ',
					renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
				},
					type:'NumberField',
					filters:{pfiltro:'dcv.importe_it',type:'numeric'},
					id_grupo:1,
					
					grid:false,
					form: false
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
					filters:{pfiltro:'dcv.estado',type:'string'},
					id_grupo:1,
					grid:false,
					form:false
			},
			{
				config:{
					name: 'sw_contabilizar',
					fieldLabel: 'Contabilizar',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					maxLength: 3
				},
					type: 'TextField',
					filters: { pfiltro:'dcv.sw_contabilizar', type:'string' },
					id_grupo: 1,
					grid: false,
					form: false
			},
			{
				config:{
					name: 'nombre_auxiliar',
					fieldLabel: 'Cuenta Corriente',
					allowBlank: false,
					anchor: '80%',
					gwidth: 150,
					maxLength:180, 
					renderer:function (value,p,record){
						if(value){
						  return  String.format('({0}) - {1}',record.data.codigo_auxiliar, record.data.nombre_auxiliar);
						}
	            	}   
				
				},
					type:'TextField',
					filters:{pfiltro:'aux.codigo_auxiliar#aux.nombre_auxiliar',type:'string'},
					id_grupo:0,
					grid: false,
					bottom_filter: false,
					form: false
			},
		   {
			   config:{
				   name: 'estacion',
				   fieldLabel: 'Estacion',
				   allowBlank: true,
				   anchor: '80%',
				   gwidth: 100,
				   maxLength :16,
				   minLength:16
			   },
			   type:'TextField',
			   filters:{pfiltro:'dcv.estacion',type:'string'},
			   id_grupo:0,
			   grid:false,
			   form:false
		   },

		   {
			   config:{
				   name: 'nombre',
				   fieldLabel: 'IATA/No IATA',
				   allowBlank: true,
				   anchor: '80%',
				   gwidth: 200,
				   maxLength :16,
				   minLength:16
			   },
			   type:'TextField',
			   filters:{pfiltro:'pv.nombre',type:'string'},
			   id_grupo:0,
			   grid:false,
			   form:false
		   },
		   {
			   config:{
				   name: 'codigo_noiata',
				   fieldLabel: 'Cod NO IATA',
				   allowBlank: true,
				   anchor: '80%',
				   gwidth: 100,
				   maxLength :16,
				   minLength:16
			   },
			   type:'TextField',
			   filters:{pfiltro:'age.codigo_noiata',type:'string'},
			   id_grupo:0,
			   grid:false,
			   form:false
		   },
		   {
			   config:{
				   name: 'desc_funcionario2',
				   fieldLabel: 'Funcionario',
				   allowBlank: true,
				   anchor: '80%',
				   gwidth: 100,
				   maxLength :16,
				   minLength:16
			   },
			   type:'TextField',
			   filters:{pfiltro:'fun.desc_funcionario2',type:'string'},
			   id_grupo:0,
			   grid:false,
			   form:false
		   },
		   {
			   config:{
				   name: 'codigo_aplicacion',
				   fieldLabel: 'Aplicación',
				   allowBlank: true,
				   anchor: '80%',
				   gwidth: 100,
				   maxLength :16,
				   minLength:16
			   },
			   type:'TextField',
			   filters:{pfiltro:'dcv.codigo_aplicacion',type:'string'},
			   id_grupo:0,
			   grid:false,
			   form:false
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
					filters:{pfiltro:'dcv.estado_reg',type:'string'},
					id_grupo:1,
					grid:false,
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
					grid:false,
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
					filters:{pfiltro:'dcv.fecha_reg',type:'date'},
					id_grupo:1,
					grid:false,
					form:false
			},
			{
				config:{
					name: 'id_usuario_ai',
					fieldLabel: '',
					allowBlank: true,
					anchor: '80%',
					gwidth: 100,
					maxLength:4
				},
					type:'Field',
					filters:{pfiltro:'dcv.id_usuario_ai',type:'numeric'},
					id_grupo:1,
					grid:false,
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
					grid:false,
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
					filters:{pfiltro:'dcv.fecha_mod',type:'date'},
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
					filters:{pfiltro:'dcv.usuario_ai',type:'string'},
					id_grupo:1,
					grid:true,
					form:false
			},
			{
	   			config:{
	   				sysorigen:'sis_contabilidad',
	       		    name:'id_cuenta',
	   				origen:'CUENTA',
	   				allowBlank:false,
	   				fieldLabel:'',
	   				gdisplayField:'desc_cuenta',//mapea al store del grid
	   				gwidth:500,
	   				width: 350,
	   				listWidth: 350,
	   				scope: this,
		   			renderer:function (value, p, record){
		   			    var color = 'green';
		   			    if(record.data["tipo_reg"] != 'summary'){
 	
		   			    }
		   			    else{
		   			    	//cargar resumen en el panel
		   			    	var importe_doc = record.data["importe_doc"]?record.data["importe_doc"]:0,
		   			    		importe_total_cobrado_mb = record.data["importe_total_cobrado_mb"]?record.data["importe_total_cobrado_mb"]:0;  
		   			    		saldo_por_cobrar = record.data["saldo_por_cobrar"]?record.data["saldo_por_cobrar"]:0;  		
		   			    		   			    	
		   			    	Ext.util.Format.number(value,'0,000.00')	   			    			   			    
		   			    	Ext.util.Format.number(value,'0,000.00')
		   			    	Ext.util.Format.number(value,'0,000.00')

	   			    		var html = String.format("<table style='width:100%; border-collapse:collapse;'> \
		   			    							  <tr>\
													    <td >Total Importe Factura BS: </td>\
													    <td style='padding: 8px;'>{0}</td> \
													  </tr >\
													  <tr>\
													      <td >Total Importe Cobrado BS: </td>\
													    <td style='padding: 8px;'>{1}</td> \
													  </tr>\
													  <tr>\
													        <td >Total Saldo por Cobrar BS: </td>\
													    <td style='padding: 8px;' >{2}</td>\
													  </tr>\
													  </table>" ,
													   Ext.util.Format.number(importe_doc,'0,000.00'), 
													   Ext.util.Format.number(importe_total_cobrado_mb,'0,000.00'), 
													   Ext.util.Format.number(saldo_por_cobrar,'0,000.00'),
													 
												
													  
													  
												);
		   			    	
		   			    	Phx.CP.getPagina(me.idContenedorPadre).panelResumen.update(html)
		   			    	return '<b><p align="right">&nbsp;&nbsp; </p></b>';
		   			    }
		   			    
		   			}
	       	     },
	   			type:'ComboRec',
	   			id_grupo:0,
	   			filters:{	
			        pfiltro:'cue.nombre_cuenta#cue.nro_cuenta#cc.codigo_cc#cue.nro_cuenta#cue.nombre_cuenta#aux.codigo_auxiliar#aux.nombre_auxiliar#par.codigo#par.nombre_partida#ot.desc_orden',
					type:'string'
				},
	   			grid:true,
	   			form:true
		   	},
			
		];
		
	  this.Atributos= this.Atributos1.concat(this.Atributos2);
	},
	

	
	obtenerVariableGlobal: function(){
		//Verifica que la fecha y la moneda hayan sido elegidos
		Phx.CP.loadingShow();
		Ext.Ajax.request({
				url:'../../sis_seguridad/control/Subsistema/obtenerVariableGlobal',
				params:{
					codigo: 'conta_libro_compras_detallado'  
				},
				success: function(resp){
					Phx.CP.loadingHide();
					var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
					
					if (reg.ROOT.error) {
						Ext.Msg.alert('Error','Error a recuperar la variable global')
					} else {
						if(reg.ROOT.datos.valor == 'no'){
							this.regitrarDetalle = 'no';
						}
					}
				},
				failure: this.conexionFailure,
				timeout: this.timeout,
				scope:this
			});
		
	},
	
	
	 
		           
          
	
            		
	
	tam_pag:50,	
	title:'Facturas',
	//ActSave:'../../sis_contabilidad/control/DocCompraVenta/modificarBasico',
	//ActDel:'../../sis_contabilidad/control/DocCompraVenta/eliminarDocCompraVenta',
	//ActList:'../../sis_contabilidad/control/DocCompraVenta/listarDocCompraVenta',
	ActList:'../../sis_cobros/control/CobroRecibo/listarDocCompraVentaCobro',
	//id_store:'id',
	id_store:'id_doc_compra_venta',
	fields: [
	
		
		{name:'id', type: 'string'},
		{name:'id_doc_compra_venta', type: 'numeric'},
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
		{name:'fecha_cbte', type: 'date',dateFormat:'Y-m-d'},
		{name:'estado_cbte', type: 'string'},
		
		{name:'fecha_cobro', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'nro_tramite_cobro', type: 'string'},
		{name:'id_cobro_simple', type: 'numeric'},
		{name:'desc_moneda_cobro', type: 'string'},
		{name:'importe_cobro', type: 'numeric'},
		{name:'importe_cobro_factura', type: 'numeric'},
		
		{name:'id_proveedor', type: 'numeric'},
		{name:'desc_proveedor', type: 'string'},
				
		{name:'importe_total_cobrado_mb', type: 'numeric'},
		{name:'importe_total_cobrado_mt', type: 'numeric'},
		{name:'saldo_por_cobrar', type: 'numeric'},'codigo_aplicacion',
		
		{name:'id_contrato', type: 'numeric'}, // 1C				20/09/2018			EGS	
		{name:'nro_contrato', type: 'string'}
	],
	sortInfo:{
		field: 'id_doc_compra_venta',
		direction: 'ASC',
	},
	
	

	bdel: false,
	bsave: false,
	bnew: false,
	bedit: false,
	bexcel: true,//#3
	
	
	//recibe los parametros del formulario 
	onReloadPage:function(param){
		
		var me = this;
		this.initFiltro(param);
	},
	
	initFiltro: function(param){
		this.store.baseParams=param;
		this.load( { params: { start:0, limit: this.tam_pag } });
	},
	
       
     preparaMenu:function(tb){
        Phx.vista.Factura.superclass.preparaMenu.call(this,tb)
        var data = this.getSelectedData();
        //this.getBoton('btnImprimirR').enable();
         this.getBoton('btnImprimir').enable();
          //this.getBoton('btnImprimirTodoCliente').enable();
      	
		
    },
    
    liberaMenu:function(tb){
        Phx.vista.Factura.superclass.liberaMenu.call(this,tb);
        //this.getBoton('btnImprimirR').disable(); //desahabilita si no se escoge registro enble para q siempre este habilitado
       this.getBoton('btnImprimir').disable();
        //this.getBoton('btnImprimirTodoCliente').enable();
        
                    
    },
    	imprimirReporte : function() {
			var rec = this.sm.getSelected();
			var data = rec.data;
			if (data) {
				Phx.CP.loadingShow();
				Ext.Ajax.request({
					url : '../../sis_cobros/control/CobroRecibo/cobroReporteFactura',
					params : {
						'id_doc_compra_venta' : data.id_doc_compra_venta,
						'razon_social':'',
						 'formato':'pdf'
					},
					success : this.successExport,
					failure : Phx.CP.conexionFailure,
					timeout : this.timeout,
					scope : this
				});
			}

		},
		
		imprimirReporteCliente: function() {
			var rec = this.sm.getSelected();
			var data = rec.data;
			if (data) {
				Phx.CP.loadingShow();
				Ext.Ajax.request({
					url : '../../sis_cobros/control/CobroRecibo/cobroReporteFactura',
					params : {
						'razon_social':data.razon_social,
					    'id_doc_compra_venta' :'',
					     'formato':'pdf'
					},
					success : this.successExport,
					failure : Phx.CP.conexionFailure,
					timeout : this.timeout,
					scope : this
				});
			}

		},
    	
 			imprimirReporteExcel: function() {
			var rec = this.sm.getSelected();
			var data = rec.data;
			if (data) {
				Phx.CP.loadingShow();
				Ext.Ajax.request({
					url : '../../sis_cobros/control/CobroRecibo/cobroReporteFactura',
					params : {
						'id_doc_compra_venta':data.id_doc_compra_venta,
					    'razon_social' :'',
					    'formato':'xls'
					},
					success : this.successExport,
					failure : Phx.CP.conexionFailure,
					timeout : this.timeout,
					scope : this
				});
			}

		},
			imprimirReporteClienteExcel: function() {
			var rec = this.sm.getSelected();
			var data = rec.data;
			if (data) {
				Phx.CP.loadingShow();
				Ext.Ajax.request({
					url : '../../sis_cobros/control/CobroRecibo/cobroReporteFactura',
					params : {
						'razon_social':data.razon_social,
					    'id_doc_compra_venta' :'',
					    'formato':'xls'
					},
					success : this.successExport,
					failure : Phx.CP.conexionFailure,
					timeout : this.timeout,
					scope : this
				});
			}

		},
		
			imprimirReporteTodoCliente: function() {
			//var rec = this.sm.getSelected(); //obliga la seleccion en la grilla
			//var data = rec.data; //utiliza una propiedad 
			//if (data) {
				Phx.CP.loadingShow();
				Ext.Ajax.request({
					url : '../../sis_cobros/control/CobroRecibo/cobroReporteFactura',
					params : {
						'todo':'cliente',
					    'formato':'pdf'
					},
					success : this.successExport,
					failure : Phx.CP.conexionFailure,
					timeout : this.timeout,
					scope : this
				});
			//}

		},
		imprimirReporteTodoClienteExcel: function() {
			//var rec = this.sm.getSelected(); //obliga la seleccion en la grilla
			//var data = rec.data; //utiliza una propiedad 
			//if (data) {
				//Phx.CP.loadingShow();
				Ext.Ajax.request({
					url : '../../sis_cobros/control/CobroRecibo/cobroReporteFactura',
					params : {
						'todo':'cliente',
					    'formato':'xls'
					},
					success : this.successExport,
					failure : Phx.CP.conexionFailure,
					timeout : this.timeout,
					scope : this
				});
			//}

		},
			///EGS-I-17/08/2018//
			
		imprimirReporteTodoFactura: function() {
			//var rec = this.sm.getSelected(); //obliga la seleccion en la grilla
			//var data = rec.data; //utiliza una propiedad 
			//if (data) {
				Phx.CP.loadingShow();
				Ext.Ajax.request({
					url : '../../sis_cobros/control/CobroRecibo/cobroReporteFactura',
					params : {
						'todo':'factura',
					    'formato':'pdf'
					},
					success : this.successExport,
					failure : Phx.CP.conexionFailure,
					timeout : this.timeout,
					scope : this
				});
			//}

		},	
		
		imprimirReporteTodoFacturaExcel: function() {
			//var rec = this.sm.getSelected(); //obliga la seleccion en la grilla
			//var data = rec.data; //utiliza una propiedad 
			//if (data) {
				//Phx.CP.loadingShow();
				Ext.Ajax.request({
					url : '../../sis_cobros/control/CobroRecibo/cobroReporteFactura',
					params : {
						'todo':'factura',
					    'formato':'xls'
					},
					success : this.successExport,
					failure : Phx.CP.conexionFailure,
					timeout : this.timeout,
					scope : this
				});
			//}

		},
   		///EGS-F-17/08/2018//
   
    	tabsouth: [{
		 url:'../../../sis_cobros/vista/factura/Cobro.php',
          title:'Cobros a Factura', 
          width:'100%',
          height:'50%',
          cls:'Cobro'
	}],
    reporteCobro: function() {// #7
        this.reporCobro = new Ext.Toolbar.SplitButton({
            id: 'btnReCobro' + this.idContenedor,
            text: 'Lista Todas las Facturas',
            disabled: false,
            grupo:[0],
            iconCls : 'bexcel',
            handler:this.formFiltroRe,
            scope: this,
            menu:{
                items: [{
                    id:'b-cobro-pdf-' + this.idContenedor,
                    text: 'Filtrar',
                    tooltip: '<b>Filtro de parametros a visualizar</b>',
                    handler:this.formFiltroRe,
                    scope: this
                }
                ]}
        });
        this.tbar.add(this.reporCobro);
    },

    formFiltroRe: function(){// #7
        var data = this.getSelectedData();
        var win = Phx.CP.loadWindows(
            '../../../sis_cobros/vista/factura/FormFiltroReporte.php',
            'Filtros', {
                width: '25%',
                height: '40%'
            },
            data,
            this.idContenedor,
            'FormFiltroReporte',
            {
                config:[{
                    event:'beforesave',
                    delegate: this.reporteCo,
                }],
                scope:this
            }

        )
    },

    reporteCo : function (wizard,resp){// #7

        console.log('resp',resp);
        Phx.CP.loadingShow();
        Ext.Ajax.request({
            url:'../../sis_cobros/control/CobroRecibo/cobroReporteFactura',
            params:
                {
                    'codigo_aplicacion':resp.codigo_aplicacion,
                    'todo':'factura',
                    'formato':resp.tipo_formato,

                },
            success: this.successExport,
            failure: this.conexionFailure,
            timeout: 3.6e+6,
            scope:this
        });
    },
	
  
    
})
</script>