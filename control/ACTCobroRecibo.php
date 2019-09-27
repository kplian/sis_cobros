<?php
/**
*@package pXP
*@file gen-ACTCobroSimple.php
*@author  (admin)
*@date 31-12-2017 12:33:30
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo

****************************************************************************************
 ISSUE			FECHA			AUTOR		DESCRIPCION
 1C				17/08/2018		EGS			Se añadió filtros y listas para el tipo de cobro y reporte de todas las facturas
 #1				09/08/2019		RCM 		Corrección por actualización PHP 7, cambio de nombre a método
****************************************************************************************
*/
//require_once(dirname(__FILE__).'/../reportes/RDetallePago.php');

//agregado
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDFFormulario.php';
require_once(dirname(__FILE__).'/../reportes/RCobroRecibo.php');
require_once(dirname(__FILE__).'/../reportes/RCobroReporteFactura.php');
//require_once(dirname(__FILE__).'/../reportes/RCobroReporteFacturaCliente.php');
require_once(dirname(__FILE__).'/../reportes/RCobroReporteFacturaCliente2.php');
//require_once(dirname(__FILE__).'/../reportes/RCobroReporteFacturaClienteTodo.php');
require_once(dirname(__FILE__).'/../reportes/RCobroReporteFacturaClienteTodo2.php');
require_once(dirname(__FILE__).'/../reportes/RCobroReporteFacturaXls.php');
require_once(dirname(__FILE__).'/../reportes/RCobroReporteFacturaClienteXls.php');
require_once(dirname(__FILE__).'/../reportes/RCobroReporteFacturaClienteTodoXls.php');

require_once(dirname(__FILE__).'/../reportes/RCobroReporteFacturaTodo.php');//  1C	EGS	17/08/2018/
require_once(dirname(__FILE__).'/../reportes/RCobroReporteFacturaTodoXls.php');//  1C	EGS	17/08/2018/

class ACTCobroRecibo extends ACTbase{

	function recuperarDatosCobroRecibo(){
    	$dataSource = new DataSource();
		$this->objFunc = $this->create('MODCobroRecibo');
		$cbteHeader = $this->objFunc->listarCobroRecibo($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){
				$dataSource->putParameter('cabecera',$cbteHeader->getDatos());
		return $dataSource;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
		}

    }

   function cobroRecibo(){

		$nombreArchivo = uniqid(md5(session_id()).'-Cbte') . '.pdf';
		//var_dump($dataSource = $this->recuperarDatosCobroRecibo());
		$dataSource = $this->recuperarDatosCobroRecibo();


		//parametros basicos
		$tamano = 'LETTER';
		$orientacion = 'p';
		$this->objParam->addParametro('orientacion',$orientacion);
		$this->objParam->addParametro('tamano',$tamano);
		$this->objParam->addParametro('titulo_archivo',$titulo);
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
		//Instancia la clase de pdf

		$reporte = new RCobroRecibo($this->objParam);

		$reporte->datosHeader($dataSource);
		$reporte->generarReporte();
		$reporte->output($reporte->url_archivo,'F');

		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

	}
	function listarRazonSocial(){
		$this->objParam->defecto('ordenacion','razon_social');
        $this->objParam->defecto('dir_ordenacion','asc');
		$this->objFunc=$this->create('MODCobroRecibo');
		$this->res=$this->objFunc->listarRazonSocial($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

		function listarDocCompraVentaCobro(){
		$this->objParam->defecto('ordenacion','dcv.id_doc_compra_venta');

		$this->objParam->defecto('dir_ordenacion','asc');


		if($this->objParam->getParametro('id_doc_compra_venta')!=''){
			$this->objParam->addFiltro("dcv.id_doc_compra_venta = ".$this->objParam->getParametro('id_doc_compra_venta'));
		}



		if($this->objParam->getParametro('id_gestion')!=''){
			$this->objParam->addFiltro("per.id_gestion = ".$this->objParam->getParametro('id_gestion'));
		}

		if($this->objParam->getParametro('id_depto')!=''){
			$this->objParam->addFiltro(" dcv.id_depto_conta = ".$this->objParam->getParametro('id_depto'));
		}


		if($this->objParam->getParametro('id_periodo')!=''){
            $this->objParam->addFiltro("dcv.id_periodo =".$this->objParam->getParametro('id_periodo'));
        }

		if($this->objParam->getParametro('nro_documento')!=''){
            $this->objParam->addFiltro("  dcv.nro_documento = ''".$this->objParam->getParametro('nro_documento')."''");

	   	    }

		if($this->objParam->getParametro('nit')!=''){
            $this->objParam->addFiltro(" dcv.nit = ''".$this->objParam->getParametro('nit')."''");
        }

		if($this->objParam->getParametro('nombre_auxiliar')!=''){
            $this->objParam->addFiltro("aux.nombre_auxiliar = ''".$this->objParam->getParametro('nombre_auxiliar')."''");
        }

		if($this->objParam->getParametro('desc_proveedor')!=''){
            $this->objParam->addFiltro("vprovee.desc_proveedor = ''".$this->objParam->getParametro('desc_proveedor')."''");
        }


		if($this->objParam->getParametro('razon_social')!=''){
            $this->objParam->addFiltro(" dcv.razon_social = ''".$this->objParam->getParametro('razon_social')."''");
        }




        if($this->objParam->getParametro('desde')!='' && $this->objParam->getParametro('hasta')!=''){
			$this->objParam->addFiltro("( dcv.fecha::date  BETWEEN ''%".$this->objParam->getParametro('desde')."%''::date  and ''%".$this->objParam->getParametro('hasta')."%''::date)");
		}

		if($this->objParam->getParametro('desde')!='' && $this->objParam->getParametro('hasta')==''){
			$this->objParam->addFiltro("( dcv.fecha::date  >= ''%".$this->objParam->getParametro('desde')."%''::date)");
		}

		if($this->objParam->getParametro('desde')=='' && $this->objParam->getParametro('hasta')!=''){
			$this->objParam->addFiltro("( dcv.fecha::date  <= ''%".$this->objParam->getParametro('hasta')."%''::date)");
		}

			///EGS-I-17/08/2018// 1C
	if($this->objParam->getParametro('nro_tramite')!=''){
            $this->objParam->addFiltro(" nuco.nro_tramite::varchar like ''%".$this->objParam->getParametro('nro_tramite')."%''");
        }

		if($this->objParam->getParametro('id_tipo_cobro_simple')!=''){

			$var=strval($this->objParam->getParametro('id_tipo_cobro_simple'));
			//var_dump($var);
            $this->objParam->addFiltro("nuco.id_tipo_cobro_simple::varchar like ''%".$this->objParam->getParametro('id_tipo_cobro_simple')."%''");
        }
			///EGS-F-17/08/2018// 1C




		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODCobroRecibo','listarDocCompraVentaCobro');
		} else{


			$this->objFunc=$this->create('MODCobroRecibo');
			$this->res=$this->objFunc->listarDocCompraVentaCobro($this->objParam);

			//$objFuncion=$this->create('MODCobroRecibo');
			//$resp=$objFuncion->listarFactura($this->objParam);
		}
			//var_dump($res);
			//var_dump($resp);
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

			$temp['importe_total_cobrado_mb'] = $this->res->extraData['total_importe_cobrado'];
			$temp['saldo_por_cobrar'] = $this->res->extraData['total_saldo_por_cobrar'];
     		$temp['tipo_reg'] = 'summary';
			$temp['id_doc_compra_venta'] = 0;


			$this->res->total++;

			$this->res->addLastRecDatos($temp);

			//}



		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarCobro(){
		$this->objParam->defecto('ordenacion','id_cobro_simple');
		$this->objParam->defecto('dir_ordenacion','asc');
		$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);

		if($this->objParam->getParametro('id_doc_compra_venta')!=''){
            $this->objParam->addFiltro(" paside.id_doc_compra_venta = ''".$this->objParam->getParametro('id_doc_compra_venta')."''");
        }

		//var_dump($this->objParam);
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODCobroRecibo','listarCobro');
		} else{
			$this->objFunc=$this->create('MODCobroRecibo');

			$this->res=$this->objFunc->listarCobro($this->objParam);
		}



			$temp = Array();

			$temp['importe_cobro_factura'] = $this->res->extraData['total_importe_cobro_factura'];



			$temp['tipo_reg'] = 'summary';
			$temp['id_cobro_simple'] = 0;


			$this->res->total++;

			$this->res->addLastRecDatos($temp);


		$this->res->imprimirRespuesta($this->res->generarJson());
	}


function listarCobroCombo(){
		$this->objParam->defecto('ordenacion','id_cobro_simple');
		$this->objParam->defecto('dir_ordenacion','asc');
		$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODCobroRecibo','listarCobro');
		} else{
			$this->objFunc=$this->create('MODCobroRecibo');

			$this->res=$this->objFunc->listarCobroCombo($this->objParam);
		}

		$this->res->imprimirRespuesta($this->res->generarJson());
	}
function listarFacturaCombo(){
		$this->objParam->defecto('ordenacion','id_cobro_simple');
		$this->objParam->defecto('dir_ordenacion','asc');
		$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODCobroRecibo','listarCobro');
		} else{
			$this->objFunc=$this->create('MODCobroRecibo');

			$this->res=$this->objFunc->listarFactura($this->objParam);
		}

		$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function recuperarDatosCobroReporteFactura(){


		if($this->objParam->getParametro('id_doc_compra_venta')!=''){
			$this->objParam->addFiltro("dcv.id_doc_compra_venta = ".$this->objParam->getParametro('id_doc_compra_venta'));
		}
		if($this->objParam->getParametro('razon_social')!=''){
            $this->objParam->addFiltro(" dcv.razon_social = ''".$this->objParam->getParametro('razon_social')."''");

		}
    	$dataSource = new DataSource();
		$this->objFunc = $this->create('MODCobroRecibo');
	    $cbteHeader = $this->objFunc->listarCobroReporteFactura($this->objParam);

		if($cbteHeader->getTipo() == 'EXITO'){
				$dataSource->putParameter('cabecera',$cbteHeader->getDatos());

		return $dataSource;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());

		}

    }

	function recuperarDatosFactura(){

		$dataSource = new DataSource();
    	$this->objFunc = $this->create('MODCobroRecibo');
		//var_dump(  $cbteheader = $this->objFunc->listarFactura($this->objParam));
		//var_dump($this->objParam);
	    $cbteHeader = $this->objFunc->listarFactura($this->objParam);

		if($cbteHeader->getTipo() == 'EXITO'){
				$dataSource->putParameter('factura',$cbteHeader->getDatos());
		return $dataSource;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());

		}

    }
   function cobroReporteFactura(){

	if(($this->objParam->getParametro('id_doc_compra_venta'))!=''&& $this->objParam->getParametro('formato')=='pdf')
	{
		$nombreArchivo = uniqid(md5(session_id()).'-Cbte') . '.pdf';

		//var_dump($dataSource = $this->recuperarDatosCobroRecibo());
		$dataSource = $this->recuperarDatosCobroReporteFactura();


		//parametros basicos
		$tamano = 'LETTER';
		$orientacion = 'p';
		$this->objParam->addParametro('orientacion',$orientacion);
		$this->objParam->addParametro('tamano',$tamano);
		$this->objParam->addParametro('titulo_archivo',$titulo);
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
		//Instancia la clase de pdf

		$reporte = new RCobroReporteFactura($this->objParam);

		$reporte->datosHeader($dataSource);
		$reporte->generarReporte();
		$reporte->output($reporte->url_archivo,'F');

		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

		}

	if($this->objParam->getParametro('razon_social')!='' && $this->objParam->getParametro('formato')=='pdf')
	{

		$nombreArchivo = uniqid(md5(session_id()).'-Cbte') . '.pdf';

		//var_dump($dataSource = $this->recuperarDatosCobroRecibo());
		$dataSource = $this->recuperarDatosCobroReporteFactura();

		//var_dump($dataSourceCobro = $this->recuperarDatosCobro());
		//$dataSource = $this->recuperarDatosCobro();


		//parametros basicos
		$tamano = 'LETTER';
		$orientacion = 'p';
		$this->objParam->addParametro('orientacion',$orientacion);
		$this->objParam->addParametro('tamano',$tamano);
		$this->objParam->addParametro('titulo_archivo',$titulo);
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
		//Instancia la clase de pdf

		//$reporte = new RCobroReporteFacturaCliente($this->objParam);//pdf en html
		$reporte = new RCobroReporteFacturaCliente2($this->objParam);

		$reporte->datosHeader($dataSource);
		//$reporte->generarReporte();//pdf en html
		$reporte->generarReporte1($dataSource); //#1
		$reporte->output($reporte->url_archivo,'F');

		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

		}


		if(($this->objParam->getParametro('id_doc_compra_venta')!='')&& ($this->objParam->getParametro('formato') == 'xls'))
		{

			if($this->objParam->getParametro('id_doc_compra_venta')!=''){
				$this->objParam->addFiltro("dcv.id_doc_compra_venta = ".$this->objParam->getParametro('id_doc_compra_venta'));
			}

			$this->objFun=$this->create('MODCobroRecibo');
			//var_dump($this->res = $this->objFun->listarCobroReporteFactura($this->objParam));
			$this->res = $this->objFun->listarCobroReporteFactura($this->objParam);
			//
			if($this->res->getTipo()=='ERROR'){
				$this->res->imprimirRespuesta($this->res->generarJson());
				exit;
			}
			$titulo ='Ret';
			$nombreArchivo=uniqid(md5(session_id()).$titulo);
			$nombreArchivo.='.xls';
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			$this->objParam->addParametro('datos',$this->res->datos);
			$this->objReporteFormato=new RCobroReporteFacturaXls($this->objParam);
			$this->objReporteFormato->generarDatos();
			$this->objReporteFormato->generarReporte();
			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se genero con éxito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
		}

		if(($this->objParam->getParametro('razon_social')!='')&& ($this->objParam->getParametro('formato') == 'xls'))
		{

			if($this->objParam->getParametro('razon_social')!=''){
            $this->objParam->addFiltro(" dcv.razon_social = ''".$this->objParam->getParametro('razon_social')."''");

			}
			$this->objFun=$this->create('MODCobroRecibo');
			//var_dump($this->res = $this->objFun->listarCobroReporteFactura($this->objParam));
			$this->res = $this->objFun->listarCobroReporteFactura($this->objParam);
			//
			if($this->res->getTipo()=='ERROR'){
				$this->res->imprimirRespuesta($this->res->generarJson());
				exit;
			}
			$titulo ='Ret';
			$nombreArchivo=uniqid(md5(session_id()).$titulo);
			$nombreArchivo.='.xls';
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			$this->objParam->addParametro('datos',$this->res->datos);
			$this->objReporteFormato=new RCobroReporteFacturaClienteXls($this->objParam);
			$this->objReporteFormato->generarDatos();
			$this->objReporteFormato->generarReporte();
			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se genero con éxito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
		}

		if(($this->objParam->getParametro('todo'))=='cliente'&& $this->objParam->getParametro('formato')=='pdf')
		{
				$nombreArchivo = uniqid(md5(session_id()).'-Cbte') . '.pdf';

				//var_dump($dataSource = $this->recuperarDatosCobroRecibo());
				$dataSource = $this->recuperarDatosFactura();



				//parametros basicos
				$tamano = 'LETTER';
				$orientacion = 'p';
				$this->objParam->addParametro('orientacion',$orientacion);
				$this->objParam->addParametro('tamano',$tamano);
				$this->objParam->addParametro('titulo_archivo',$titulo);
				$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
				//Instancia la clase de pdf

				$reporte = new RCobroReporteFacturaClienteTodo2($this->objParam);

				$reporte->datosHeader($dataSource);
				//$reporte->generarReporte();//en html
				$reporte->generarReporte1($dataSource); //#1
				$reporte->output($reporte->url_archivo,'F');

				$this->mensajeExito=new Mensaje();
				$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

		}
		if(($this->objParam->getParametro('todo'))=='cliente'&& $this->objParam->getParametro('formato')=='xls')
		{

			$this->objFun=$this->create('MODCobroRecibo');
			//var_dump($this->res = $this->objFun->listarCobroReporteFactura($this->objParam));
			$this->res = $this->objFun->listarFactura($this->objParam);
			//
			if($this->res->getTipo()=='ERROR'){
				$this->res->imprimirRespuesta($this->res->generarJson());
				exit;
			}
			$titulo ='Ret';
			$nombreArchivo=uniqid(md5(session_id()).$titulo);
			$nombreArchivo.='.xls';
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			$this->objParam->addParametro('datos',$this->res->datos);
			$this->objReporteFormato=new RCobroReporteFacturaClienteTodoXls($this->objParam);
			$this->objReporteFormato->generarDatos();
			$this->objReporteFormato->generarReporte();
			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se genero con éxito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
		}

		///EGS-I-17/08/2018// 1C

		if(($this->objParam->getParametro('todo')) == 'factura'&& $this->objParam->getParametro('formato')=='xls')
		{

			$this->objFun=$this->create('MODCobroRecibo');
			//var_dump($this->res = $this->objFun->listarCobroReporteFactura($this->objParam));
			$this->res = $this->objFun->listarFactura($this->objParam);
			//
			if($this->res->getTipo()=='ERROR'){
				$this->res->imprimirRespuesta($this->res->generarJson());
				exit;
			}
			$titulo ='Ret';
			$nombreArchivo=uniqid(md5(session_id()).$titulo);
			$nombreArchivo.='.xls';
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			$this->objParam->addParametro('datos',$this->res->datos);
			$this->objReporteFormato=new RCobroReporteFacturaTodoXls($this->objParam);
			$this->objReporteFormato->generarDatos();
			$this->objReporteFormato->generarReporte();
			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se genero con éxito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
		}
		if(($this->objParam->getParametro('todo')) == 'factura'&& $this->objParam->getParametro('formato')=='pdf')
		{

			$nombreArchivo = uniqid(md5(session_id()).'-Cbte') . '.pdf';

				//var_dump($dataSource = $this->recuperarDatosCobroRecibo());
				$dataSource = $this->recuperarDatosFactura();


				//parametros basicos
				$tamano = 'LETTER';
				$orientacion = 'L';
				$this->objParam->addParametro('orientacion',$orientacion);
				$this->objParam->addParametro('tamano',$tamano);
				$this->objParam->addParametro('titulo_archivo',$titulo);
				$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
				//Instancia la clase de pdf

				$reporte = new RCobroReporteFacturaTodo($this->objParam);

				$reporte->datosHeader($dataSource);
				//$reporte->generarReporte();//en html
				$reporte->generarReporte1($dataSource); //#1
				$reporte->output($reporte->url_archivo,'F');

				$this->mensajeExito=new Mensaje();
				$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

		}




	}


	function listarTipoCobro(){


		$this->objParam->defecto('ordenacion','id_tipo_cobro_simple');
        $this->objParam->defecto('dir_ordenacion','asc');
		$this->objFunc=$this->create('sis_cobros/MODTipoCobroSimple');
		$this->res=$this->objFunc->listarTipoCobroSimple($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());

	}

	///EGS-F-17/08/2018// 1C


}

?>