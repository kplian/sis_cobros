<?php
/**
*@package pXP
*@file gen-ACTCobroSimpleDet.php
*@author  (admin)
*@date 01-01-2018 06:21:25
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTCobroSimpleDet extends ACTbase{    
			
	function listarCobroSimpleDet(){
		$this->objParam->defecto('ordenacion','id_cobro_simple_det');
		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('id_cobro_simple')!=''){
			$this->objParam->addFiltro("paside.id_cobro_simple = ".$this->objParam->getParametro('id_cobro_simple'));	
		}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODCobroSimpleDet','listarCobroSimpleDet');
		} else{
			$this->objFunc=$this->create('MODCobroSimpleDet');
			
			$this->res=$this->objFunc->listarCobroSimpleDet($this->objParam);
		}

		$temp = Array();
		$temp['importe_ice'] = $this->res->extraData['total_importe_ice'];
		$temp['importe_excento'] = $this->res->extraData['total_importe_excento'];
		$temp['importe_it'] = $this->res->extraData['total_importe_it'];
		$temp['importe_iva'] = $this->res->extraData['total_importe_iva'];
		$temp['importe_descuento'] = $this->res->extraData['total_importe_descuento'];
		$temp['importe_doc'] = $this->res->extraData['total_importe_doc'];			
		$temp['importe_retgar'] = $this->res->extraData['total_importe_retgar'];
		$temp['importe_anticipo'] = $this->res->extraData['total_importe_anticipo'];
		$temp['importe_pendiente'] = $this->res->extraData['tota_importe_pendiente'];
		$temp['importe_neto'] = $this->res->extraData['total_importe_neto'];
		$temp['importe_descuento_ley'] = $this->res->extraData['total_importe_descuento_ley'];
		$temp['importe_pago_liquido'] = $this->res->extraData['total_importe_pago_liquido'];	
		$temp['importe_aux_neto'] = $this->res->extraData['total_importe_aux_neto'];	
		
		
		$temp['importe_mb'] = $this->res->extraData['total_importe_det_mb'];	
		$temp['importe_mt'] = $this->res->extraData['total_importe_det_mt'];	
				
		$temp['tipo_reg'] = 'summary';
		$temp['id_doc_compra_venta'] = 0;

		$this->res->total++;
		$this->res->addLastRecDatos($temp);

		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarCobroSimpleDet(){
		$this->objFunc=$this->create('MODCobroSimpleDet');	
		if($this->objParam->insertar('id_cobro_simple_det')){
			$this->res=$this->objFunc->insertarCobroSimpleDet($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarCobroSimpleDet($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarCobroSimpleDet(){
		$this->objFunc=$this->create('MODCobroSimpleDet');	
		$this->res=$this->objFunc->eliminarCobroSimpleDet($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function relacionarFacturaCobro(){
		$this->objFunc=$this->create('MODCobroSimpleDet');	
		$this->res=$this->objFunc->relacionarFacturaCobro($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}


			
}

?>