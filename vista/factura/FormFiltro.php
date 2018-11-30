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
Phx.vista.FormFiltro=Ext.extend(Phx.frmInterfaz,{
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
				    
				    
	  
				    
       Phx.vista.FormFiltro.superclass.constructor.call(this,config);
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
   /*
        {
			config: {
				name: 'nro_tramite',
				allowBlank: true,
				fieldLabel: 'Nro. Trámite Cobrado',
				width: 150,
			    listeners:{
                            'change': function(field, newValue, oldValue){

                                field.suspendEvents(true);
                                field.setValue(newValue.toUpperCase());
                                field.resumeEvents(true);
                            }
                        },
			},
			type: 'Field',
			id_grupo: 0,
			form: true
		},
		*//*
			{
                    config:{
                        name: 'nro_tramite',
                        fieldLabel: 'Nro. Trámite Cobrado',
                        qtip: 'Nro. Trámite Cobrado',
                        allowBlank: true,
                        maskRe: /[A-Za-z0-9 &-. ñ Ñ]/,
                        //fieldStyle:'text-transform:uppercase',
                        listeners:{
                            'change': function(field, newValue, oldValue){

                                field.suspendEvents(true);
                                field.setValue(newValue.toUpperCase());
                                field.resumeEvents(true);
                            }
                        },
                        emptyText:'Cobro..',
                        store:new Ext.data.JsonStore(
                            {
                                url: '../../sis_cobros/control/CobroRecibo/listarCobroCombo',
                                id: 'id_cobro_simple',
                                root:'datos',
                                sortInfo:{
                                    field:'id_cobro_simple',
                                    direction:'ASC'
                                },
                                totalProperty:'total',
                                fields: ['id_cobro_simple','nro_tramite'],
                                remoteSort: true,
								baseParams:{par_filtro:'pagsim.nro_tramite'}
                            }),
                      
                        valueField: 'nro_tramite',
                        hiddenValue: 'nro_tramite',
                        displayField: 'nro_tramite',
                        gdisplayField:'nro_tramite',
                        queryParam:'query', //nombre de la consulta dentro el campo
                        listWidth:'280',
                        forceSelection:false,
                        autoSelect: false,
                        typeAhead: false, //completa el campo
                        typeAheadDelay: 75,
                        hideTrigger:true,
                        triggerAction: 'all',
                        lazyRender:false,
                        mode:'remote',
                        pageSize:5,
                        queryDelay:500,
                        anchor: '80%',
                        minChars:1, //las letras q se escriben para que se active la accion sea de busqueda o algo
                        
                    },
                    type:'ComboBox',
                    id_grupo: 0,
                    bottom_filter: false,
                    form: true
                },*/
               
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
			
			
			/////EGS-F-20/08/2018
		
		     {
                    config:{
                        name: 'nit',
                        fieldLabel: 'NIT',
                        qtip: 'Número de indentificación del proveedor',
                        allowBlank: true,
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
                            
                            //1A	EGS  24/08/2018	
                        tpl:'<tpl for=".">\
		                       <div class="x-combo-list-item"><p><b>Nit:</b>{nit}</p>\
		                       <p><b>Razon Social: </b>{razon_social}</p>\
		                     </div></tpl>', 
						//1A	EGS  24/08/2018	
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
                        anchor: '95%',//1A	EGS  24/08/2018	
                        minChars:1
                    },
                    type:'ComboBox',
                    id_grupo: 0,
                    form: true
                },
                /*		
				{
                    config:{
                        name: 'nro_documento',
                        fieldLabel: 'Nro Factura / Doc',
                        allowBlank: true,
                        anchor: '80%',
                        allowDecimals: false,
                        maxLength:100/*,  RAC  29/12/2017 comentado para recibos pueden tener numeros alfanumericos
                        maskRe: /[0-9/-]+/i,
                        regex: /[0-9/-]+/i*/

/*
                    },
                    type:'Field',
                    id_grupo:1,
                    form:true
                },*/
     
                {
				config:{
					name: 'nro_documento',
					fieldLabel: 'Nro Factura / Doc',
					allowBlank: true,
					resizable:true,
					emptyText: 'Nro Factura / Doc',
					store: new Ext.data.JsonStore({
	
		    					url: '../../sis_cobros/control/CobroRecibo/listarFacturaCombo',
		    					id: 'id',
		    					root: 'datos',
		    					sortInfo:{
		    						field: 'id',
		    						direction: 'ASC'
		    					},
		    					totalProperty: 'total',
		    					fields: ['id','nro_documento','razon_social','fecha','importe_doc','nro_cbte'],//1A	EGS  24/08/2018	
		    					// turn on remote sorting
		    					remoteSort: true,
		    					baseParams:{par_filtro:'nro_documento'}
		    				}),
		    				
		    		//1A	EGS  24/08/2018	
		    		tpl:'<tpl for=".">\
		                       <div class="x-combo-list-item"><p><b>Nro Doc/Fac:</b>{nro_documento}, <b>Nro Comprobante: </b>{nro_cbte}</p>\
		                       <p><b>Razon Social: </b>{razon_social},<b> Fecha:</b> {fecha}</p>\
		                     <p><b>Importe: </b>{importe_doc} </p></div></tpl>', 
		            //1A	EGS  24/08/2018	
	        	    valueField: 'nro_documento',
	        	    displayField: 'nro_documento',
	        	    gdisplayField: 'nro_documento',
	        	    hiddenName: 'nro_documento',
	        	    queryParam: 'query',///coloca pestana si se coloca numero de documento
	        	    hideTrigger:false,
	        	    triggerAction: 'all',
	        	    queryDelay:1000,
	        	    pageSize:5,
					forceSelection:false,
					typeAhead: false,
					anchor: '95%', //1A	EGS  24/08/2018	
					gwidth: 180,
					mode: 'remote',
					minChars:1, //las letras q se escriben para que se active la accion sea de busqueda o algo
				},
		           			
				type:'ComboBox',
				filters:{pfiltro:'dcv.numero_documento',type:'string'},
				bottom_filter: true,
				id_grupo:0,
				grid:false,
				form:true
			},
                /*
	        		 {
                    config:{
                        name: 'razon_social',
                        fieldLabel: 'Razón Social',
                        allowBlank: true,
                        maskRe: /[A-Za-z0-9 &-. ñ Ñ]/,
                        fieldStyle: 'text-transform:uppercase',
                        listeners:{
                            'change': function(field, newValue, oldValue){

                                field.suspendEvents(true);
                                field.setValue(newValue.toUpperCase());
                                field.resumeEvents(true);
                            }
                        },
                        anchor: '80%',
                        maxLength:180
                    },
                    type:'TextField',
                    id_grupo:0,
                    form:true
                },/**/
               
                 {
                    config:{
                        name: 'razon_social',
                        fieldLabel: 'Razon Social',
                        qtip: 'Razon Social',
                        allowBlank: true,
                        maskRe: /[A-Za-z0-9 &-. ñ Ñ]/,
                        //fieldStyle:'text-transform:uppercase',
                       listeners:{
                            'change': function(field, newValue, oldValue){

                                field.suspendEvents(true);
                                field.setValue(newValue.toUpperCase());
                                field.resumeEvents(true);
                            }
                        },
                        emptyText:'Razon ...',
                        store:new Ext.data.JsonStore(
                            {
                                url: '../../sis_cobros/control/CobroRecibo/listarRazonSocial',
                                id: 'razon_social',
                                root:'datos',
                                sortInfo:{
                                    field:'razon_social',
                                    direction:'ASC'
                                },
                                totalProperty:'total',
                                fields: ['razon_social','nit'],
                                remoteSort: true,
                               baseParams:{par_filtro:'razon_social'}
                            }),
                            
                            //1A	EGS  24/08/2018	
                      	tpl:'<tpl for=".">\
		                       <div class="x-combo-list-item"><p><b>Razon Social: </b>{razon_social}</p>\
		                      <p><b>Nit:</b>{nit}</p> \
		                     </div></tpl>',
		                 //1A	EGS  24/08/2018	
                        valueField: 'razon_social',
                        hiddenValue: 'razon_social',
                        displayField: 'razon_social',
                        gdisplayField:'razon_social',
                        queryParam: 'razon_social',
                        listWidth:'280',
                        forceSelection:false,
                        autoSelect: false,
                        typeAhead: false,
                        typeAheadDelay: 75,
                        hideTrigger:true,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode:'remote',
                        pageSize:20,
                        queryDelay:500,
                        anchor: '95%', //1A	EGS  24/08/2018	
                        minChars:1,
                        
                    },
                    type:'ComboBox',
                    id_grupo: 0,
                    form: true
                },
            {
            config:{
                name: 'desc_proveedor',
                origen: 'PROVEEDOR',
                allowBlank: true,
                fieldLabel: 'Proveedor',
                anchor: '100%',
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
		url: '../../../sis_cobros/vista/factura/Factura.php',
		title: 'Facturas', 
		width: '70%',
		height: '80%',
		cls: 'Factura'
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
			
			//var tipo_cobro= this.Cmp.id_tipo_cobro_simple.lastSelectionText;///EGS
			
			
			var tipo_cobro= this.Cmp.id_tipo_cobro_simple.getValue();////EGS/20/08/2018//// 
			var nit=this.Cmp.nit.lastSelectionText;
			var nro_doc=this.Cmp.nro_documento.lastSelectionText;
			
		
			
			var desc_pro=this.Cmp.desc_proveedor.lastSelectionText;
			//var nom_aux=this.Cmp.nombre_auxiliar.lastSelectionText;
			var razon_social=this.Cmp.razon_social.lastSelectionText;		
					
			this.onEnablePanel(this.idContenedor + '-east', 
				Ext.apply(parametros,{	'gest': gest,
										'dpto': dpto,
										'nro_tramite' : nro_tram,
										 'id_tipo_cobro_simple':tipo_cobro,////EGS/20/08/2018////
										 'nit' : nit ,
										 'nro_doc' : nro_doc,
										 'desc_proveedor':desc_pro,
										// 'nombre_auxiliar' : nom_aux,
										 'razon_social':razon_social
									 }));
       }
    },
	//
    iniciarEventos:function(){
    	
    	
    	// oculta los campos en el formulario al escoger una opcion
    	/*this.Cmp.tipo_filtro.on('change', function(cmp, check){
    		    
    		    if(check.getRawValue() !='gestion'){
    		    	this.Cmp.id_gestion.reset();
    		    	this.ocultarComponente(this.Cmp.id_gestion);
    		    	
    		    }
    		    else{
    		    	this.mostrarComponente(this.Cmp.id_gestion);
    		    	
    		    }
    		    	
    		    
    		
    	}, this);*/
    	
    		// oculta los campos en el formulario al escoger una opcion
    		/*
    	this.Cmp.tipo_filtro1.on('change', function(cmp, check){
    		    
    		    if(check.getRawValue() !='factura'){
    		    	this.Cmp.id_gestion.reset();
    		    	this.ocultarComponente(this.Cmp.nro_documento);
    		    	this.mostrarComponente(this.Cmp.nro_tramite);
    		    	
    		    }
    		    else{
    		    	this.mostrarComponente(this.Cmp.nro_documento);
    		    	this.ocultarComponente(this.Cmp.nro_tramite);
    		    	
    		    	
    		    }
    		    	
    		    
    		
    	}, this);*/
    	//carga los valores de razon si se pone un nit
    	 this.Cmp.nit.on('select', function(cmb,rec,i){
                this.Cmp.razon_social.setValue(rec.data.razon_social);
            } ,this);

            this.Cmp.nit.on('change', function(cmb,newval,oldval){
                var rec = cmb.getStore().getById(newval);
                if(!rec){
                    //si el combo no tiene resultado
                    if(cmb.lastQuery){
                        //y se tiene una consulta anterior( cuando editemos no abra cnsulta anterior)
                        this.Cmp.razon_social.reset();
                    }
                }

            } ,this);
          /*
          this.Cmp.razon_social.on('select', function(cmb,rec,i){
                this.Cmp.nit.setValue(rec.data.nit);
            } ,this);

            this.Cmp.razon_social.on('change', function(cmb,newval,oldval){
                var rec = cmb.getStore().getById(newval);
                if(!rec){
                    //si el combo no tiene resultado
                    if(cmb.lastQuery){
                        //y se tiene una consulta anterior( cuando editemos no abra cnsulta anterior)
                        this.Cmp.nit.reset();
                    }
                }

            } ,this);*/
    	
    },
    

    
    loadValoresIniciales: function(){
    	Phx.vista.FormFiltro.superclass.loadValoresIniciales.call(this);
    	
    	
    	
    	
    }
    
})    
</script>