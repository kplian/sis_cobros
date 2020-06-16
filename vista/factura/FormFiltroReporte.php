<?php
/**
*@package pXP
*@file    FormFiltroReporte.php
*@author
*@date    09-10-2017
*@description muestra un formulario que muestra la cuenta y el monto de la transferencia
 *  ISSUE			FECHA			AUTOR		DESCRIPCION
    #7             16/06/2020      EGS          creacion
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.FormFiltroReporte=Ext.extend(Phx.frmInterfaz,{
		
	layout:'fit',
	maxCount:0,
	breset :false,
	constructor:function(config){
		this.maestro=config;
		
		console.log('maestro',this.maestro.id_proveedor);
		
		Phx.vista.FormFiltroReporte.superclass.constructor.call(this,config);
		this.init(); 
		this.iniciarEventos();		
		this.loadValoresIniciales();		
	},

	Atributos:[


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
		},

        {
            config: {
                name:'codigo_aplicacion',
                qtip:'Aplicación para filtro prioritario, primero busca uan relación contable especifica para la aplicación definida si no la encuentra busca un relación contable sin aplicación',
                fieldLabel : 'Aplicación:',
                resizable:true,
                allowBlank:true,
                emptyText:'Seleccione un catálogo...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                    id: 'id_catalogo',
                    root: 'datos',
                    sortInfo:{
                        field: 'orden',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_catalogo','codigo','descripcion'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams: {par_filtro:'descripcion',catalogo_tipo:'tipo_venta'}
                }),
                enableMultiSelect:false,
                valueField: 'codigo',
                displayField: 'descripcion',
                gdisplayField: 'codigo_aplicacion',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:10,
                queryDelay:1000,
                anchor: '80%',
                minChars:2
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro: 'movtip.nombre',type: 'string'},
            grid: true,
            form: true
        },
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
            codigo_aplicacion:this.Cmp.codigo_aplicacion.getValue(),
			tipo_formato:this.Cmp.tipo_formato.getValue(),
        }
		return resp;
	}
	

})
</script>
