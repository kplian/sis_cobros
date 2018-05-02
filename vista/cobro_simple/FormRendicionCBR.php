<?php
/**
*@package pXP
*@file FormRendicionCBR.php
*@author  Rensi Arteaga 
*@date 16-02-2016
*@description Archivo con la interfaz de usuario que permite 
*ingresar el documento a rendir
*
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.FormRendicionCBR = {
	require: '../../../sis_contabilidad/vista/doc_compra_venta/FormCompraVenta.php',
	ActSave: '../../sis_cobros/control/RendicionDet/insertarPSDocCompleto',
	requireclase: 'Phx.vista.FormCompraVenta',
	mostrarFormaPago : false,
	mostrarFuncionario: true,
	heightHeader: 245,
	autorizacion: 'fondo_avance',
	autorizacion_nulos: 'no',
	tipo_pres_gasto: 'gasto,administrativo',
		
	constructor: function(config) {	
		Phx.vista.FormRendicionCBR.superclass.constructor.call(this,config);	   
    },
    
      extraAtributos:[
        {
				//configuracion del componente
				config:{
						labelSeparator:'',
						inputType:'hidden',
						name: 'sw_pgs'
				},
				type:'Field',
				form:true 
		}
     ],
    
    
	onNew: function(){    	
    	Phx.vista.FormRendicionCBR.superclass.onNew.call(this);
    	this.Cmp.sw_pgs.setValue('reg');
       
	},
	
	onEdit: function(){    	
    	Phx.vista.FormRendicionCBR.superclass.onEdit.call(this);	
    	this.Cmp.sw_pgs.setValue('reg');	
       		
	},
	
	
};
</script>
