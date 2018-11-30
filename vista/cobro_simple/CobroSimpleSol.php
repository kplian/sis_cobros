<?php
/**
*@package pXP
*@file CobroSimpleSol.php
*@author  RCM
*@date 20/01/2018
*@description Archivo con la interfaz de usuario
 * #1 		06/09/2018		EGS								aumento validacion de botones para anticipos
*
*
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.CobroSimpleSol = {
	require:'../../../sis_cobros/vista/cobro_simple/CobroSimple.php',
	requireclase:'Phx.vista.CobroSimple',
	title:'Pagos',
	nombreVista: 'CobroSimpleSol',
	
	constructor: function(config) {
        //Historico
        this.historico = 'no';
        this.tbarItems = ['-',{
            text: 'Histórico',
            enableToggle: true,
            pressed: false,
            toggleHandler: function(btn, pressed) {
               
                if(pressed){
                    this.historico = 'si';
                    this.getBoton('ant_estado').disable();
                    this.getBoton('sig_estado').disable();
                }
                else{
                   this.historico = 'no' 
                }
                
                this.store.baseParams.historico = this.historico;
                this.reload();
             },
            scope: this
           }];
           
        var me = this;
	   
        Phx.vista.CobroSimpleSol.superclass.constructor.call(this,config);
        this.init();
       
		this.store.baseParams = { tipo_interfaz: this.nombreVista };
		
		if(config.filtro_directo){
           this.store.baseParams.filtro_valor = config.filtro_directo.valor;
           this.store.baseParams.filtro_campo = config.filtro_directo.campo;
        }
		//primera carga
		this.store.baseParams.pes_estado = 'borrador';
    	this.load({params:{start:0, limit:this.tam_pag}});

		this.finCons = true;
   },
   
    preparaMenu: function(n) {
        var data = this.getSelectedData();
        var tb = this.tbar;
        Phx.vista.CobroSimple.superclass.preparaMenu.call(this, n);

        this.getBoton('ant_estado').disable();
        this.getBoton('sig_estado').disable();

        //Si está en modo histórico,no habilita ninguno de los botones que generan transacciones
        if(this.historico=='no'){
			
			/// #1 		06/09/2018		EGS	
		    if(data.estado == 'borrador') {			
				
				this.getBoton('sig_estado').enable();
			} else if(data.estado == 'finalizado'){
				this.getBoton('sig_estado').disable();
				this.getBoton('del').disable();
				this.getBoton('edit').disable();
			} else if(data.estado == 'pendiente'|| data.estado == 'pendiente_pago'){
				this.getBoton('sig_estado').disable();
				this.getBoton('ant_estado').disable();
				this.getBoton('del').disable();
				this.getBoton('edit').disable();
				
			//#1 		06/09/2018		EGS	
			} else if(data.estado == 'prorrateo'){
				this.getBoton('sig_estado').enable();
				this.getBoton('ant_estado').disable();
				this.getBoton('del').disable();
				this.getBoton('edit').disable();
			 //#1 		06/09/2018		EGS	
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
         this.getBoton('btnRecibo').enable();

       
        
        
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
            this.getBoton('btnRecibo').disable();
          
              
        }
        return tb
    }
        
};
</script>
