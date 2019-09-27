<?php
/**
*@package pXP
*@file ACTCobroRe.php
*@author  (admin)
*@date 31-12-2017 12:33:30
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 ****************************************************************************************
 ISSUE 	FECHA		AUTOR 		DESCRIPCION
 #1		14/8/2019   EGS 		Corrección por actualización PHP 7, cambio de nombre a método
****************************************************************************************
*/

require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDFFormulario.php';



require_once(dirname(__FILE__).'/../reportes/RCobroReporte.php');
require_once(dirname(__FILE__).'/../reportes/RCobroReporteXls.php');


class ACTCobroRe extends ACTbase{
	
	   
			
	function listarCobroRe(){
		$this->objParam->defecto('ordenacion','id_cobro_simple');
		
	    $this->objParam->defecto('dir_ordenacion','asc');
			
		if($this->objParam->getParametro('id_gestion')!=''){
			$this->objParam->addFiltro("gest.id_gestion = ".$this->objParam->getParametro('id_gestion'));	
		}
		
		if($this->objParam->getParametro('id_depto')!=''){
			$this->objParam->addFiltro("pagsim.id_depto_conta = ".$this->objParam->getParametro('id_depto'));	
		}
		
		
		if($this->objParam->getParametro('id_periodo')!=''){
            $this->objParam->addFiltro("gest.id_periodo =".$this->objParam->getParametro('id_periodo'));    
        }
		
	
		
		if($this->objParam->getParametro('nombre_auxiliar')!=''){
            $this->objParam->addFiltro("aux.nombre_auxiliar = ''".$this->objParam->getParametro('nombre_auxiliar')."''");    
        }
		
		if($this->objParam->getParametro('desc_proveedor')!=''){
            $this->objParam->addFiltro("pro.desc_proveedor = ''".$this->objParam->getParametro('desc_proveedor')."''");    
        }
		
	
		if($this->objParam->getParametro('nro_tramite')!=''){
            $this->objParam->addFiltro(" pagsim.nro_tramite= ''".$this->objParam->getParametro('nro_tramite')."''");    
        }
		
		
        if($this->objParam->getParametro('desde')!='' && $this->objParam->getParametro('hasta')!=''){
			$this->objParam->addFiltro("( pagsim.fecha::date  BETWEEN ''%".$this->objParam->getParametro('desde')."%''::date  and ''%".$this->objParam->getParametro('hasta')."%''::date)");	
		}
		
		if($this->objParam->getParametro('desde')!='' && $this->objParam->getParametro('hasta')==''){
			$this->objParam->addFiltro("( pagsim.fecha::date  >= ''%".$this->objParam->getParametro('desde')."%''::date)");	
		}
		
		if($this->objParam->getParametro('desde')=='' && $this->objParam->getParametro('hasta')!=''){
			$this->objParam->addFiltro("( pagsim.fecha::date  <= ''%".$this->objParam->getParametro('hasta')."%''::date)");	
		}
		
			///EGS-I-17/08/2018// 1C
		if($this->objParam->getParametro('id_tipo_cobro_simple')!=''){
            $this->objParam->addFiltro("pagsim.id_tipo_cobro_simple = ".$this->objParam->getParametro('id_tipo_cobro_simple'));    
        }
		

		$this->objParam->defecto('dir_ordenacion','asc');
		$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]); 
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODCobroRe','listarCobroRe');
		} else{
			$this->objFunc=$this->create('MODCobroRe');
			
			$this->res=$this->objFunc->listarCobroRe($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function listarDocCompraVentaRe(){
		$this->objParam->defecto('ordenacion','id_doc_compra_venta');

		$this->objParam->defecto('dir_ordenacion','asc');
		
			
	
		
		if($this->objParam->getParametro('id_cobro_simple')!=''){
            $this->objParam->addFiltro("pagsim.id_cobro_simple = ".$this->objParam->getParametro('id_cobro_simple'));    
        }
		
				
		
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODCobroRe','listarDocCompraVentaRe');
		} else{
			
			
			$this->objFunc=$this->create('MODCobroRe');			
			$this->res=$this->objFunc->listarDocCompraVentaRe($this->objParam);
		
		}
		
		//habilita los totales en la interfaz
		
			//if($this->objParam->getParametro('razon_social')!=''){
				
			$temp = Array();
			$temp['importe_ice'] = $this->res->extraData['total_importe_ice'];
			$temp['importe_excento'] = $this->res->extraData['total_importe_excento'];
			$temp['importe_it'] = $this->res->extraData['total_importe_it'];
			$temp['importe_iva'] = $this->res->extraData['total_importe_iva'];
			$temp['importe_descuento'] = $this->res->extraData['total_importe_descuento'];
			//$temp['importe_doc'] = $this->res->extraData['total_importe_doc'];
			
			$temp['importe_doc'] = $this->res->extraData['total_importe'];
						
			$temp['importe_retgar'] = $this->res->extraData['total_importe_retgar'];
			$temp['importe_anticipo'] = $this->res->extraData['total_importe_anticipo'];
			$temp['importe_pendiente'] = $this->res->extraData['tota_importe_pendiente'];
			$temp['importe_neto'] = $this->res->extraData['total_importe_neto'];
			$temp['importe_descuento_ley'] = $this->res->extraData['total_importe_descuento_ley'];
			$temp['importe_pago_liquido'] = $this->res->extraData['total_importe_pago_liquido'];	
			//$temp['importe_cobrado_mb'] = $this->res->extraData['total_importe_cobro_factura'];
			
			$temp['importe_cobrado_mb'] = $this->res->extraData['total_cobrado'];
			$temp['saldo_por_cobrar'] = $this->res->extraData['total_saldo_por_cobrar'];
     		$temp['tipo_reg'] = 'summary';
			$temp['id_doc_compra_venta'] = 0;
			
			
			$this->res->total++;
			
			$this->res->addLastRecDatos($temp);	
				
			//}
		
			
		
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function recuperarDatosCobro(){
		$this->objParam->addParametroConsulta('ordenacion','desc_proveedor');
        $this->objParam->addParametroConsulta('dir_ordenacion','ASC');
        $this->objParam->addParametroConsulta('cantidad',1000000000);
        $this->objParam->addParametroConsulta('puntero',0);
		
		
		if($this->objParam->getParametro('id_cobro_simple')!=''){
            $this->objParam->addFiltro("pagsim.id_cobro_simple = ".$this->objParam->getParametro('id_cobro_simple'));    
        }
		
		$dataSource = new DataSource();	
    	$this->objFunc = $this->create('MODCobroRe');
	
	;
		//var_dump($this->objParam);
	    $cbteHeader = $this->objFunc->listarCobroReporte($this->objParam);
		//var_dump($cbteHeader);
		
		if($cbteHeader->getTipo() == 'EXITO') { 	
				$dataSource->putParameter('cobro',$cbteHeader->getDatos());			
		return $dataSource;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
		    
		} 
		
			
    }
    
	function recuperarDatosTipoCobro(){
		$this->objParam->addParametroConsulta('ordenacion','id_tipo_cobro_simple');
        $this->objParam->addParametroConsulta('dir_ordenacion','ASC');
        $this->objParam->addParametroConsulta('cantidad',1000);
        $this->objParam->addParametroConsulta('puntero',0);
		
		
		//var_dump($this->objParamDefecto);
		
		if($this->objParam->getParametro('id_tipo_cobro_simple')!=''){
            $this->objParam->addFiltro("tipasi.id_tipo_cobro_simple = ".$this->objParam->getParametro('id_tipo_cobro_simple'));    
        }
		
		$dataSource = new DataSource();	
		$this->objFunc = $this->create('sis_cobros/MODTipoCobroSimple');
		
		$cbteHeader = $this->objFunc->listarTipoCobroSimple($this->objParam);
		
		if($cbteHeader->getTipo() == 'EXITO'){
			$dataSource->putParameter('tipo_cobro',$cbteHeader->getDatos());						
			return $dataSource;			
		}
		else{
			$cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}
	}
  /////////recupera el proveedor ya existente el sistema de cobros
	function recuperarDatosProveedor(){
		$this->objParam->addParametroConsulta('ordenacion','id_proveedor');
        $this->objParam->addParametroConsulta('dir_ordenacion','ASC');
        $this->objParam->addParametroConsulta('cantidad',1);
        $this->objParam->addParametroConsulta('puntero',0);

		if($this->objParam->getParametro('id_proveedor')!=''){
            $this->objParam->addFiltro("pagsim.id_proveedor = ".$this->objParam->getParametro('id_proveedor'));    
        }
		
		$dataSource = new DataSource();	
    	$this->objFunc = $this->create('MODCobroRe');
	
	;
		//var_dump($this->objParam);
	    $cbteHeader = $this->objFunc->listarCobroReporte($this->objParam);
		//var_dump($cbteHeader);
		
		if($cbteHeader->getTipo() == 'EXITO') { 	
				$dataSource->putParameter('proveedor',$cbteHeader->getDatos());			
		return $dataSource;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
		    
		} 
		
			
    }

	function cobroReporte(){
		
		$dataTipo = '';
		$dataProveedor = '';
		
		
		if($this->objParam->getParametro('tipo_formato')=='pdf'){

		$nombreArchivo = uniqid(md5(session_id()).'-Cbte') . '.pdf'; 
		
		/////Recupera tipo de cobro y proveedor
		if($this->objParam->getParametro('id_tipo_cobro_simple')!=''){

		    $dataTipo = $this->recuperarDatosTipoCobro();
		
        }
		
		///reescribiendo los filtros para q se reinicien 
		$this->objParam->addParametroConsulta('filtro','0=0');
			
	
		 
		if($this->objParam->getParametro('id_proveedor')!=''){

		    $dataProveedor = $this->recuperarDatosProveedor();
			//var_dump($dataProveedor);
        }
		
		///reescribiendo los filtros para q se reinicien 
		$this->objParam->addParametroConsulta('filtro','0=0');
		
		/////////// añadir filtros para busqueda
		
		if($this->objParam->getParametro('id_proveedor')!=''){
            $this->objParam->addFiltro("pagsim.id_proveedor = ".$this->objParam->getParametro('id_proveedor'));    
        }
		
		if($this->objParam->getParametro('id_tipo_cobro_simple')!=''){
            $this->objParam->addFiltro("tipasi.id_tipo_cobro_simple = ".$this->objParam->getParametro('id_tipo_cobro_simple'));    
        }
		
		
		$dataSource =$this->recuperarDatosCobro();
		//var_dump($dataSource);
		$tamano = 'LETTER';
		$orientacion = 'p';
		$this->objParam->addParametro('orientacion',$orientacion);
		$this->objParam->addParametro('tamano',$tamano);
		$this->objParam->addParametro('titulo_archivo',$titulo);
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
		//Instancia la clase de pdf
		
		$reporte = new RCobroReporte($this->objParam);
		
		//var_dump('cobro',$this->objParam);
		
		
		$reporte->datosHeader($dataSource);
	
		$reporte->generarReporte1($dataSource,$dataTipo,$dataProveedor);//#1
		$reporte->output($reporte->url_archivo,'F');
		
		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
		}
		
		if($this->objParam->getParametro('tipo_formato')=='xls'){
			 	
				$this->objParamDefecto=$this->objParam;
				
		
				if($this->objParam->getParametro('id_tipo_cobro_simple')!=''){
			
					 $dataTipo = $this->recuperarDatosTipoCobro();
					 $dataTipo = $dataTipo->getParameter('tipo_cobro');
					 $this->objParam->addParametro('tipo_cobro',$dataTipo);	
					//var_dump($dataTipo);
        		
				}
				///reescribiendo los filtros para uq se reinicien 
				$this->objParam->addParametroConsulta('filtro','0=0');
				
				if($this->objParam->getParametro('id_proveedor')!=''){
			
					 $dataProveedor = $this->recuperarDatosProveedor();
					 $dataProveedor = $dataProveedor->getParameter('proveedor');
					 $this->objParam->addParametro('proveedor',$dataProveedor);	
					//var_dump($dataTipo);
        		
				}
				///reescribiendo los filtros para q se reinicien 
				$this->objParam->addParametroConsulta('filtro','0=0');
				
				
			///añadiendo filtros para la busqueda	
			if($this->objParam->getParametro('id_cobro_simple')!=''){
            	$this->objParam->addFiltro("pagsim.id_cobro_simple = ".$this->objParam->getParametro('id_cobro_simple'));    
        	}
			if($this->objParam->getParametro('id_proveedor')!=''){
            	$this->objParam->addFiltro("pagsim.id_proveedor = ".$this->objParam->getParametro('id_proveedor'));    
        	}
			if($this->objParam->getParametro('id_tipo_cobro_simple')!=''){
            $this->objParam->addFiltro("tipasi.id_tipo_cobro_simple = ".$this->objParam->getParametro('id_tipo_cobro_simple'));    
       		 }
			
			
			$this->objParam->addParametroConsulta('ordenacion','desc_proveedor');
	        $this->objParam->addParametroConsulta('dir_ordenacion','ASC');
	        $this->objParam->addParametroConsulta('cantidad',1000000000);
	        $this->objParam->addParametroConsulta('puntero',0);
			$this->objFun=$this->create('MODCobroRe');
			
			$this->res = $this->objFun->listarCobroReporte($this->objParam);
			
			//var_dump($this->res);
			
			if($this->res->getTipo()=='ERROR'){
				$this->res->imprimirRespuesta($this->res->generarJson());
				exit;
			}
			
		
			
			$titulo ='Cobro';
			$nombreArchivo=uniqid(md5(session_id()).$titulo);
			$nombreArchivo.='.xls';
			
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			
			$this->objParam->addParametro('data',$this->res->datos);

			//var_dump($this->objParam);
			
			$this->objReporteFormato=new RCobroReporteXls($this->objParam);
			$this->objReporteFormato->generarDatos();
			$this->objReporteFormato->generarReporte();
			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se genero con éxito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
			
		}
		
	}
		

}

?>