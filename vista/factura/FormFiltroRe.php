<?php
/**
*@package pXP
*@file    FormFiltroRe.php
*@author  manuel guerra
*@date    09-10-2017
*@description muestra un formulario que muestra la cuenta y el monto de la transferencia
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.FormFiltroRe=Ext.extend(Phx.frmInterfaz,{
		
	layout:'fit',
	maxCount:0,
	breset :false,
	constructor:function(config){
		this.maestro=config;
		
		console.log('maestro',this.maestro.id_proveedor);
		
		Phx.vista.FormFiltroRe.superclass.constructor.call(this,config);
		this.init(); 
		this.iniciarEventos();		
		this.loadValoresIniciales();		
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
					name: 'id_proveedor',
					
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name:'tipo',
				fieldLabel:'Tipo',
				allowBlank:false,
				emptyText:'Tipo de reporte...',
				typeAhead: true,
				triggerAction: 'all',
				lazyRender:true,
				mode: 'local',
				valueField: 'tipo',
				anchor: '100%',
				gwidth: 100,
				store:new Ext.data.ArrayStore({
					fields: ['variable', 'valor'],
					data : [ 
								['todo','TODO'],
								['proveedor','PROVEEDOR'],
								['cobro','COBRO']
							]
				}),
				valueField: 'variable',
				displayField: 'valor',
				
				listeners: {
					'afterrender': function(combo){			  
						combo.setValue('todo');
					}
				}
			},
			type:'ComboBox',
			form:true
		},
		
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
					anchor: '100%',//1A	EGS  24/08/2018	
					gwidth: 100,
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
				name:'tipo_formato',
				fieldLabel:'Tipo de Reporte',
				allowBlank:false,
				emptyText:'Tipo de reporte...',
				typeAhead: true,
				triggerAction: 'all',
				lazyRender:true,
				mode: 'local',
				valueField: 'tipo_reporte',
				anchor: '100%',
				gwidth: 100,
				store:new Ext.data.ArrayStore({
					fields: ['variable', 'valor'],
					data : [ 
								['pdf','PDF'],
								['xls','EXCEL']
							]
				}),
				valueField: 'variable',
				displayField: 'valor',
				
				listeners: {
					'afterrender': function(combo){			  
						combo.setValue('pdf');
					}
				}
			},
			type:'ComboBox',
			form:true
		}
	],
	

	title:'Filtro',
	
	/*
 successSave:function(resp)
        {
            Phx.CP.loadingHide();
            this.panel.close();
        },
       
        */
    	iniciarEventos: function(){
    		
			if(this.maestro.id_cobro_simple != null ){
				
					
						   this.Cmp.tipo.on('select',function(combo,record,index){
							console.log(record.data.variable);	
								if(record.data.variable == 'cobro' ){
	
									   this.Cmp.id_cobro_simple.setValue(this.maestro.id_cobro_simple);
									   this.Cmp.id_tipo_cobro_simple.reset();
									   this.Cmp.id_tipo_cobro_simple.disable(true);
	
								 }
								 else if(record.data.variable == 'proveedor' ){
	
									   this.Cmp.id_proveedor.setValue(this.maestro.id_proveedor);
									   this.Cmp.id_tipo_cobro_simple.enable(true);
	
								 }else
								 {
								 	this.Cmp.id_tipo_cobro_simple.enable(true);
								 }
		
				},this);			      

							
				
			}else{
			   this.Cmp.tipo.disable(true);
		
			}

    	},
    	
    	onSubmit:function(){
		//TODO passar los datos obtenidos del wizard y pasar  el evento save		
		if (this.form.getForm().isValid()) {
			this.fireEvent('beforesave',this,this.getValues());
			this.getValues();
			}
			this.panel.close();
		},
	
		
		getValues:function(){		
		var resp = {			
			id_cobro_simple:this.Cmp.id_cobro_simple.getValue(),
			id_proveedor:this.Cmp.id_proveedor.getValue(),
			id_tipo_cobro_simple:this.Cmp.id_tipo_cobro_simple.getValue(),
			tipo:this.Cmp.tipo.getValue(),
			tipo_formato:this.Cmp.tipo_formato.getValue(),
				

		}
		return resp;
	}
	

})
</script>
