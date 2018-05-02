<?php
/**
*@package pXP
*@file gen-ACTTipoCobroSimple.php
*@author  (admin)
*@date 02-12-2017 02:49:10
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTTipoCobroSimple extends ACTbase{    
			
	function listarTipoCobroSimple(){
		$this->objParam->defecto('ordenacion','id_tipo_cobro_simple');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODTipoCobroSimple','listarTipoCobroSimple');
		} else{
			$this->objFunc=$this->create('MODTipoCobroSimple');
			
			$this->res=$this->objFunc->listarTipoCobroSimple($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarTipoCobroSimple(){
		$this->objFunc=$this->create('MODTipoCobroSimple');	
		if($this->objParam->insertar('id_tipo_cobro_simple')){
			$this->res=$this->objFunc->insertarTipoCobroSimple($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarTipoCobroSimple($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarTipoCobroSimple(){
			$this->objFunc=$this->create('MODTipoCobroSimple');	
		$this->res=$this->objFunc->eliminarTipoCobroSimple($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>