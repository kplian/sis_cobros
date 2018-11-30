<?php
/**
*@package pXP
*@file    SolModPresupuesto.php
*@author  Rensi Arteaga Copari 
*@date    30-01-2014
*@description permites subir archivos a la tabla de documento_sol
 * ISSUE				FECHA			AUTHOR		  DESCRIPCION
 *  1A					24/08/2018			EGS  		se aumento campo para comprobante  y se hizo mejoras en los combos visualmente
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormFiltroCobro=Ext.extend(Phx.frmInterfaz,{
    constructor:function(config)
    {   
    	
    	//console.log('configuracion.... ',config)
    	this.panelResumen = new Ext.Panel({html:''});
    	this.Grupos = [{

	                    xtype: 'fieldset',
	                    border: false,
	                    autoScroll: true,
	                    layout: 'form',
	                    items: [],
	                    id_grupo: 0
				               
				    },
				     this.panelResumen
				    ];
				    
				    
	  
				    
       Phx.vista.FormFiltroCobro.superclass.constructor.call(this,config);
       this.init(); 
       this.iniciarEventos(); 
    
       
        
        if(config.detalle){
        	
			//cargar los valores para el filtro
			this.loadForm({data: config.detalle});
			var me = this;
			setTimeout(function(){
				me.onSubmit()
			}, 1000);
			
		}  
       
        
        
    },
    
  
    
    Atributos:[
    
 
    /*		
           {
	   			config:{
	   				name : 'tipo_filtro',
	   				fieldLabel : 'Filtros',
	   				items: [
		                {boxLabel: 'Gestión', name: 'tipo_filtro', inputValue: 'gestion', checked: true},
		                {boxLabel: 'Solo fechas', name: 'tipo_filtro', inputValue: 'fechas'}
		            ],
		            
		    
	   			},
	   			type : 'RadioGroupField',
	   			id_grupo : 0,
	   			form : true
	   	   },*/
	   	   /*
	   	     {
	   			config:{
	   				name : 'tipo_filtro1',
	   				fieldLabel : 'Buscar por ',
	   				items: [
		                {boxLabel: 'Factura', name: 'tipo_filtro1', inputValue: 'factura', checked: true},
		                {boxLabel: 'Cobro', name: 'tipo_filtro1', inputValue: 'cobro'}
		            ],
		            
		    
	   			},
	   			type : 'RadioGroupField',
	   			id_grupo : 0,
	   			form : true
	   	   },*/
           {
	   			config:{
	   				name : 'id_gestion',
	   				origen : 'GESTION',
	   				fieldLabel : 'Gestion',
	   				gdisplayField: 'desc_gestion',
	   				allowBlank : true,
	   				width: 150
	   			},
	   			type : 'ComboRec',
	   			id_grupo : 0,
	   			form : true
	   	   },
	   	   {
				config:{
					name: 'desde',
					fieldLabel: 'Desde',
					allowBlank: true,
					format: 'd/m/Y',
					width: 150
				},
				type: 'DateField',
				id_grupo: 0,
				form: true
		  },
		  {
				config:{
					name: 'hasta',
					fieldLabel: 'Hasta',
					allowBlank: true,
					format: 'd/m/Y',
					width: 150
				},
				type: 'DateField',
				id_grupo: 0,
				form: true
		  },
		  {
			config:{
				name:'id_depto',
				hiddenName: 'id_depto',
				url: '../../sis_parametros/control/Depto/listarDeptoFiltradoXUsuario',
				origen:'DEPTO',
				allowBlank:true,
				fieldLabel: 'Depto',
				baseParams:{estado:'activo',codigo_subsistema:'CONTA'},
				width: 150
			},
			type:'ComboRec',
			id_grupo:0,
			form:true
         },
   
               
             {
				config:{
					name: 'nro_tramite',
					fieldLabel: 'Nro. Trámite Cobrado',
					allowBlank: true,
					resizable:true,
					emptyText: 'Nro. Trámite Cobrado',
					store: new Ext.data.JsonStore({
	
		    					url: '../../sis_cobros/control/CobroRecibo/listarCobroCombo',
		    					id: 'id_cobro_simple',
		    					root: 'datos',
		    					sortInfo:{
		    						field: 'id_cobro_simple',
		    						direction: 'ASC'
		    					},
		    					totalProperty: 'total',
		    					fields: ['id_cobro_simple','nro_tramite','nombre'],
		    					// turn on remote sorting
		    					remoteSort: true,
		    					baseParams:{par_filtro:'nro_tramite'}
		    				}),
		    				//1A	EGS  24/08/2018	
		    		tpl:'<tpl for=".">\
		                       <div class="x-combo-list-item"><p><b>Nro Tramite:</b>{nro_tramite}</p>\
		                       <p><b>Tipo Cobro: </b>{nombre}</p>\
		                     </div></tpl>', 
		                    //1A	EGS  24/08/2018	
	        	    valueField: 'nro_tramite',
	        	    displayField: 'nro_tramite',
	        	    gdisplayField: 'nro_tramite',
	        	    hiddenName: 'nro_tramite',
	        	    hideTrigger:false, //oculta la pestana del campo
	        	    queryParam:'query',
	        	    triggerAction: 'all',
	        	    lazyRender:false,
	        	    queryDelay:1000,
	        	    pageSize:5,
					forceSelection:false,
					typeAhead: false,
					anchor: '95%',//1A	EGS  24/08/2018	
					gwidth: 200,
					mode: 'remote',
					minChars:1, //las letras q se escriben para que se active la accion sea de busqueda o algo
				},
		           			
				type:'ComboBox',
				filters:{pfiltro:'pagsim.numero_tramite',type:'string'},
				bottom_filter: true,
				id_grupo:0,
				grid:false,
				form:true
			},
			
			/////EGS-I-20/08/2018
			       {
				config:{
					name: 'id_tipo_cobro_simple',
					fieldLabel: 'Tipo de Cobro',
					allowBlank: true,
					resizable:true,
					emptyText: 'Tipo de Cobro',
					store: new Ext.data.JsonStore({
	
		    					url: '../../sis_cobros/control/CobroRecibo/listarTipoCobro',
		    					id: 'id_tipo_cobro_simple',
		    					root: 'datos',
		    					sortInfo:{
		    						field: 'id_tipo_cobro_simple',
		    						direction: 'ASC'
		    					},
		    					totalProperty: 'total',
		    					fields: ['id_tipo_cobro_simple','codigo','nombre'],
		    					// turn on remote sorting
		    					remoteSort: true,
		    					baseParams:{par_filtro:'id_tipo_cobro_simple'}
		    				}),
		    				
		    		
	        	    valueField: 'id_tipo_cobro_simple',
	        	    displayField: 'nombre',
	        	    gdisplayField: 'id_tipo_cobro_simple',
	        	    hiddenName: 'id_tipo_cobro_simple',
	        	    hideTrigger:false, //oculta la pestana del campo
	        	    queryParam:'query',
	        	    triggerAction: 'all',
	        	    lazyRender:false,
	        	    queryDelay:1000,
	        	    pageSize:5,
					forceSelection:false,
					typeAhead: false,
					anchor: '95%',//1A	EGS  24/08/2018	
					gwidth: 180,
					mode: 'remote',
					minChars:1, //las letras q se escriben para que se active la accion sea de busqueda o algo
				},
		           			
				type:'ComboBox',
				filters:{pfiltro:'pagsim.id_tipo_cobro_simple',type:'string'},
				bottom_filter: true,
				id_grupo:0,
				grid:false,
				form:true
			},

              
            {
            config:{
                name: 'desc_proveedor',
                origen: 'PROVEEDOR',
                allowBlank: true,
                fieldLabel: 'Proveedor',
                anchor: '95%',
                gdisplayField: 'desc_proveedor',//mapea al store del grid
                hiddenValue: 'desc_proveedor',
                gwidth: 150,
                //baseParams: { 'filtrar_base': 'si' },
                renderer: function (value, p, record){return String.format('{0}', record.data['desc_proveedor']);}
             },
            type: 'ComboRec',
            id_grupo: 0,
            filters: { pfiltro:'pro.desc_proveedor',type:'string'},
           // grid: true,
            form: true,
            bottom_filter:true,
        },	
        /*
        {
   			config:{
   				sysorigen: 'sis_contabilidad',
       		    name: 'nombre_auxiliar',
   				origen: 'AUXILIAR',
   				allowBlank: true,
   				gdisplayField: 'desc_auxiliar',
   				fieldLabel: 'Auxiliar',
   				width: 150
       	     },
   			type:'ComboRec',
   			id_grupo: 0,
   			form: true
	   	},*/
	   	

	],

	labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
	east: {
		url: '../../../sis_cobros/vista/factura/CobroRe.php',
		title: 'Cobro', 
		width: '80%',
		height: '80%',
		cls: 'CobroRe'
	},
	title: 'Filtro',
	
	// Funcion guardar del formulario
	onSubmit: function(o) {    	
		var me = this;
		if (me.form.getForm().isValid()) {		
			var parametros = me.getValForm();
			
			var gest=this.Cmp.id_gestion.lastSelectionText;
			var dpto=this.Cmp.id_depto.lastSelectionText;
		
			var nro_tram=this.Cmp.nro_tramite.lastSelectionText;

			var tipo_cobro= this.Cmp.id_tipo_cobro_simple.getValue();////EGS/20/08/2018//// 
			//var nit=this.Cmp.nit.lastSelectionText;
			//var nro_doc=this.Cmp.nro_documento.lastSelectionText;

			var desc_pro=this.Cmp.desc_proveedor.lastSelectionText;
			//var nom_aux=this.Cmp.nombre_auxiliar.lastSelectionText;
			//var razon_social=this.Cmp.razon_social.lastSelectionText;		
					
			this.onEnablePanel(this.idContenedor + '-east', 
				Ext.apply(parametros,{	'gest': gest,
										'dpto': dpto,
										'nro_tramite' : nro_tram,
										 'id_tipo_cobro_simple':tipo_cobro,////EGS/20/08/2018////
										 //'nit' : nit ,
										 //'nro_doc' : nro_doc,
										 'desc_proveedor':desc_pro,
										// 'nombre_auxiliar' : nom_aux,
										 //'razon_social':razon_social
									 }));
       }
    },
	//
    iniciarEventos:function(){

    	
  
    	
    },
    

    
    loadValoresIniciales: function(){
    	Phx.vista.FormFiltroCobro.superclass.loadValoresIniciales.call(this);
    	
    	
    	
    	
    }
    
})    
</script>