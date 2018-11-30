<?php
/**
*@package pXP
*@file Cobro.php
*@author  (EGS)
*@date 
*@description te permite ver
*		ISSUE				FECHA				AUTHOR					Descripcion
 * 		#1					20/11/2018				EGS					Se aumento los botones de documentos WFy el gantt
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Cobro=Ext.extend(Phx.gridInterfaz,{
	nombreVista: 'Cobro',
	constructor:function(config){
		this.maestro=config.maestro;

		//Historico
		this.historico = 'no';

    	//llama al constructor de la clase padre
		Phx.vista.Cobro.superclass.constructor.call(this,config);
		this.init();
		
		//Adicion de botones en la barra de herramientas
	     
      this.iniciarEventos();

		//this.load({params:{start:0, limit:this.tam_pag}});
		this.bloquearMenus();
		//	#1		20/11/2018	EGS		
		this.addBotonesGantt();
		this.addButton('btnChequeoDocumentosWf',
	            {
	                text: 'Documentos',
	                grupo:[0,1,2,3],
	                iconCls: 'bchecklist',
	                disabled: true,
	                handler: this.loadCheckDocumentosWf,
	                tooltip: '<b>Documentos del Trámite</b><br/>Permite ver los documentos asociados al NRO de trámite.'
	            });

	//	#1		20/11/2018	EGS		

	},
	//	#1		20/11/2018	EGS		
		 loadCheckDocumentosWf:function() {
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
                tooltip: '<b>Mues un reporte gantt en formato de imagen</b>',
                handler:this.diagramGantt,
                scope: this
            }, {
                id:'b-ganttd-' + this.idContenedor,
                text: 'Gantt Dinámico',
                tooltip: '<b>Muestra el reporte gantt facil de entender</b>',
                handler:this.diagramGanttDinamico,
                scope: this
            }
        ]}
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
	//	#1		20/11/2018	EGS		
    	
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
					name: 'importe_cobro_factura',
					fieldLabel: 'Cobro a Factura MB',
					allowBlank: false,
					anchor: '80%',
					gwidth: 100,
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
					filters:{pfiltro:'ic.nro_tramite',type:'string'},
					id_grupo:0,
					grid:true,
					bottom_filter: false,
					form:false
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
			grid:false,
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
   			bottom_filter:false,
   		    grid:false,
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
			grid : false,
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
			grid : false,
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
			grid : false,
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
			grid : false,
			form : true
		},
        
        {
			config:{
				name: 'importe',
				fieldLabel: 'Importe de Cobro',
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
   			bottom_filter:false,
   		    grid:false,
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
				bottom_filter:false
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
				bottom_filter:false
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
			grid:false,
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
				bottom_filter: true,
				filters: {pfiltro:'dcv.id_doc_compra_venta',type:'numeric'}
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
	ActList:'../../sis_cobros/control/CobroRecibo/listarCobro',
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
		{name:'id_doc_compra_venta', type: 'string'},
		{name:'id_gestion', type: 'numeric'},
		{name:'id_periodo', type: 'numeric'},'tipo_cambio',
		{name:'importe_cobro_factura', type: 'numeric'},
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
	bdel:false,
	bsave:false,
	bnew:false,
	bedit:false,
	
	

   preparaMenu: function(n) {

		var data = this.getSelectedData();
		var tb = this.tbar;
		Phx.vista.Cobro.superclass.preparaMenu.call(this, n);
    
     
		return tb
	},

		
	liberaMenu: function() {
		var tb = Phx.vista.Cobro.superclass.liberaMenu.call(this);
		
	
                           
		return tb
	},

    
    	imprimirRecibo : function() {
			var rec = this.sm.getSelected();
			var data = rec.data;
			if (data) {
				Phx.CP.loadingShow();
				Ext.Ajax.request({
					url : '../../sis_cobros/control/CobroRecibo/cobroRecibo',
					params : {
						'id_proceso_wf' : data.id_proceso_wf
					},
					success : this.successExport,
					failure : this.conexionFailure,
					timeout : this.timeout,
					scope : this
				});
			}

		},
		
		mostraFactura: function(){
		var data = this.getSelectedData();
		var win = Phx.CP.loadWindows(
			'../../../sis_cobros/vista/factura/FormFiltro.php',
			'Facturas', {
			    width: '80%',
			    height: '70%'
			},
			data,
			this.idContenedor,
			'FormFiltro'//clase de la vista
		);
	},
	onReloadPage: function (m) {
		//alert ('asda');
            this.maestro = m;
            this.store.baseParams = {id_doc_compra_venta: this.maestro.id_doc_compra_venta};
            this.load({params: {start: 0, limit: 50}})
        },
     loadValoresIniciales: function () {
            Phx.vista.Cobro.superclass.loadValoresIniciales.call(this);
            this.Cmp.id_doc_compra_venta.setValue(this.maestro.id_doc_compra_venta);
            
        }
    
    

})
</script>