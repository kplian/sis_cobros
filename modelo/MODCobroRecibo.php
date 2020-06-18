<?php
/**
*@package pXP
*@file gen-MODCobroSimple.php
*@author  (admin)
*@date 31-12-2017 12:33:30
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 * 
 *  ISSUE				FECHA			AUTHOR		  DESCRIPCION
 *  1A					24/08/2018			EGS  	para tipo de cobro y mejoras en filtro
 *	1B					20/09/2018		EGS			se aumento el campo id_contrato
 */

class MODCobroRecibo extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
	function listarCobroRecibo(){
			
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='cbr.ft_cobro_recibo_sel';
		$this->transaccion='CBR_COBREC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);
		
		$this->setParametro('id_proceso_wf','id_proceso_wf','int4');
				
		//Definicion de la lista del resultado del query
		$this->captura('id_cobro_simple','int4');
		$this->captura('codigo','varchar');
		$this->captura('id_cuenta_bancaria','int4');
		$this->captura('importe','numeric');
		$this->captura('importe_literal','varchar');
		$this->captura('desc_proveedor','varchar');
		$this->captura('id_funcionario','int4');
		$this->captura('id_proveedor','int4');
		$this->captura('nro_tramite','varchar');
		$this->captura('obs','varchar');
		$this->captura('fecha','date');
		
		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
	
function listarDocCompraVentaCobro(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='cbr.ft_cobro_recibo_sel ';
		$this->transaccion='CBR_COBCBR_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		
		//captura parametros adicionales para el count
		/*
		$this->capturaCount('total_importe_ice','numeric');
		$this->capturaCount('total_importe_excento','numeric');
		$this->capturaCount('total_importe_it','numeric');
		$this->capturaCount('total_importe_iva','numeric');
		$this->capturaCount('total_importe_descuento','numeric');
		$this->capturaCount('total_importe_doc','numeric');
		
		$this->capturaCount('total_importe_retgar','numeric');
		$this->capturaCount('total_importe_anticipo','numeric');
		$this->capturaCount('tota_importe_pendiente','numeric');
		$this->capturaCount('total_importe_neto','numeric');
		$this->capturaCount('total_importe_descuento_ley','numeric');
		$this->capturaCount('total_importe_pago_liquido','numeric');
		$this->capturaCount('total_importe_cobro_factura','numeric');
		$this->capturaCount('total_importe_aux_neto','numeric');
		/*$this->capturaCount('importe_cobro','numeric');*/
		/*$this->capturaCount('total_saldo_por_cobrar','numeric');
		$this->capturaCount('total_saldo_por_cobrar_mt','numeric');*/
		
		$this->capturaCount('total_importe','numeric');
		$this->capturaCount('total_importe_cobrado','numeric');
		$this->capturaCount('total_saldo_por_cobrar','numeric');
		
		
		/*
		$this->setParametro('nombre_vista','nombre_vista','varchar');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('id_tipo_cobro_simple','id_tipo_cobro_simple','varchar');////1A					24/08/2018			EGS 
		$this->setParametro('nro_documento','nro_documento','varchar');*/
		//$this->setParametro('id_proveedor','id_proveedor','int4');
		
		
				
		//Definicion de la lista del resultado del query
		$this->captura('id_doc_compra_venta','int8');
		$this->captura('revisado','varchar');
		$this->captura('movil','varchar');
		$this->captura('tipo','varchar');
		$this->captura('importe_excento','numeric');
		$this->captura('id_plantilla','int4');
		$this->captura('fecha','date');
		$this->captura('nro_documento','varchar');
		$this->captura('nit','varchar');
		$this->captura('importe_ice','numeric');
		$this->captura('nro_autorizacion','varchar');
		$this->captura('importe_iva','numeric');
		$this->captura('importe_descuento','numeric');
		$this->captura('importe_doc','numeric');
		$this->captura('sw_contabilizar','varchar');
		$this->captura('tabla_origen','varchar');
		$this->captura('estado','varchar');
		$this->captura('id_depto_conta','int4');
		$this->captura('id_origen','int4');
		$this->captura('obs','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('codigo_control','varchar');
		$this->captura('importe_it','numeric');
		$this->captura('razon_social','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		$this->captura('desc_depto','varchar');
		$this->captura('desc_plantilla','varchar');
		$this->captura('importe_descuento_ley','numeric');
		$this->captura('importe_pago_liquido','numeric');
		$this->captura('nro_dui','varchar');
		$this->captura('id_moneda','int4');
		$this->captura('desc_moneda','varchar');
		$this->captura('id_int_comprobante','int4');
		$this->captura('nro_tramite','varchar');
		$this->captura('desc_comprobante','varchar');
		
		
		$this->captura('importe_pendiente','numeric');
		$this->captura('importe_anticipo','numeric');
		$this->captura('importe_retgar','numeric');
		$this->captura('importe_neto','numeric');		
		$this->captura('id_auxiliar','integer');
		$this->captura('codigo_auxiliar','varchar');
		$this->captura('nombre_auxiliar','varchar');		
		$this->captura('id_tipo_doc_compra_venta','integer');
		$this->captura('desc_tipo_doc_compra_venta','varchar');		
		$this->captura('importe_aux_neto','numeric');
		$this->captura('id_funcionario','integer');		
		$this->captura('desc_funcionario2','varchar');
		$this->captura('fecha_cbte','date');
		$this->captura('estado_cbte','varchar');
		
		
		$this->captura('importe_cobrado_mb','numeric');
		$this->captura('importe_cobrado_mt','numeric');
		$this->captura('importe_cobrado_retgar_mb','numeric');///EGS//20/08/2018  ///2000-I
		$this->captura('importe_cobrado_retgar_mt','numeric');////EGS///20/08/2018////2000-F
		$this->captura('importe_cobrado_ant_mb','numeric');///EGS//20/08/2018  ///2000-I
		$this->captura('importe_cobrado_ant_mt','numeric');////EGS///20/08/2018////2000-F
		
		$this->captura('importe_total_cobrado_mb','numeric');///EGS//20/08/2018  ///2000-I
		$this->captura('importe_total_cobrado_mt','numeric');
		
		$this->captura('saldo_por_cobrar_pendiente','numeric');//#2001 ETR        12/09/2018        EGS 
		$this->captura('saldo_por_cobrar_retgar','numeric');//#2001 ETR        12/09/2018        EGS 
		$this->captura('saldo_por_cobrar_anticipo','numeric');//#2001 ETR        12/09/2018        EGS 
		
		$this->captura('saldo_por_cobrar','numeric');
		

		$this->captura('id_contrato','int4');  //1B				20/09/2018     EGS
		$this->captura('nro_contrato','varchar'); //1B				20/09/2018     EGS
		//$this->captura('nro_tramite','varchar');
		//$this->captura('id_tipo_cobro_simple','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump($this->respuesta); exit;
		//Devuelve la respuesta
		return $this->respuesta;
	}
	   function listarRazonSocial(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='cbr.ft_cobro_recibo_sel';
		$this->transaccion='CBR_COBRS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);
		
		$this->setParametro('razon_social','razon_social','varchar');
		
		//Definicion de la lista del resultado del query
		$this->captura('razon_social','varchar');
		$this->captura('nit','varchar');   // 1A					24/08/2018			EGS 
		

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
	function listarCobro(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='cbr.ft_cobro_recibo_sel';
		$this->transaccion='CBR_COBRO_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		
		

		$this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
		$this->setParametro('tipo_interfaz','tipo_interfaz','varchar');		
		$this->setParametro('historico','historico','varchar');
		$this->setParametro('estado','estado','varchar');
				
				
		$this->capturaCount('total_importe_cobro_factura','numeric');			
		//Definicion de la lista del resultado del query
		$this->captura('id_cobro_simple','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_depto_conta','int4');
		$this->captura('nro_tramite','varchar');
		$this->captura('fecha','date');
		$this->captura('id_funcionario','int4');
		$this->captura('estado','varchar');
		$this->captura('id_estado_wf','int4');
		$this->captura('id_proceso_wf','int4');
		$this->captura('obs','varchar');
		$this->captura('id_cuenta_bancaria','int4');
		$this->captura('id_depto_lb','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_depto_conta','varchar');
		$this->captura('desc_funcionario','text');
		$this->captura('desc_cuenta_bancaria','varchar');
		$this->captura('desc_depto_lb','varchar');
		$this->captura('id_moneda','integer');
		$this->captura('id_proveedor','integer');
		$this->captura('desc_moneda','varchar');
		$this->captura('desc_proveedor','varchar');
		$this->captura('id_tipo_cobro_simple','integer');
		$this->captura('id_funcionario_pago','integer');
		$this->captura('desc_funcionario_pago','text');
		$this->captura('desc_tipo_cobro_simple','text');
		$this->captura('codigo_tipo_cobro_simple','varchar');
		$this->captura('nro_tramite_asociado','varchar');
		$this->captura('importe','numeric');
		$this->captura('id_obligacion_pago','integer');
		$this->captura('desc_obligacion_pago','varchar');
		$this->captura('id_caja','integer');
		$this->captura('desc_caja','varchar');
		$this->captura('id_tipo_doc_compra_venta','integer');
		
		$this->captura('razon_social','varchar');
		$this->captura('nit','varchar');
		$this->captura('importe_cobro_factura','numeric');
		
		$this->captura('id_gestion','integer');
		$this->captura('id_periodo','integer');
		
		
		$this->captura('tipo_cambio','numeric');
		$this->captura('tipo_cambio_mt','numeric');
		$this->captura('tipo_cambio_ma','numeric');
		$this->captura('id_config_cambiaria','integer');
		$this->captura('importe_mt','numeric');
		$this->captura('importe_mb','numeric');
		$this->captura('importe_ma','numeric');
		$this->captura('forma_cambio','varchar');
		
        
		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}

function listarCobroCombo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='cbr.ft_cobro_recibo_sel';
		$this->transaccion='CBR_CBRCOMBO_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion	
		
				
		//Definicion de la lista del resultado del query
		$this->captura('id_cobro_simple','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_depto_conta','int4');
		$this->captura('nro_tramite','varchar');
		$this->captura('fecha','date');
		$this->captura('id_funcionario','int4');
		$this->captura('estado','varchar');
		$this->captura('id_estado_wf','int4');
		$this->captura('id_proceso_wf','int4');
		$this->captura('obs','varchar');
		$this->captura('id_cuenta_bancaria','int4');
		$this->captura('id_depto_lb','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('nombre','varchar');	     ///EGS  24/08/2018	1A	
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
function listarCobroReporteFactura(){
		//Definicion de variables para ejecucion del procedimientp
		
		$this->procedimiento='cbr.ft_cobro_recibo_sel ';
		
		$this->transaccion='CBR_COBREFA_SEL';
		
		
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);


		$this->setParametro('id_doc_compra_venta','id_doc_compra_venta','int8');
	
		
				
		//Definicion de la lista del resultado del query
		$this->captura('id','int8');
		$this->captura('id_doc_compra_venta','int8');
		$this->captura('revisado','varchar');
		$this->captura('movil','varchar');
		$this->captura('tipo','varchar');
		$this->captura('importe_excento','numeric');
		$this->captura('id_plantilla','int4');
		$this->captura('fecha','date');
		$this->captura('nro_documento','varchar');
		$this->captura('nit','varchar');
		$this->captura('importe_ice','numeric');
		$this->captura('nro_autorizacion','varchar');
		$this->captura('importe_iva','numeric');
		$this->captura('importe_descuento','numeric');
		$this->captura('importe_doc','numeric');
		$this->captura('sw_contabilizar','varchar');
		$this->captura('tabla_origen','varchar');
		$this->captura('estado','varchar');
		$this->captura('id_depto_conta','int4');
		$this->captura('id_origen','int4');
		$this->captura('obs','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('codigo_control','varchar');
		$this->captura('importe_it','numeric');
		$this->captura('razon_social','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		$this->captura('desc_depto','varchar');
		$this->captura('desc_plantilla','varchar');
		$this->captura('importe_descuento_ley','numeric');
		$this->captura('importe_pago_liquido','numeric');
		$this->captura('nro_dui','varchar');
		$this->captura('id_moneda','int4');
		$this->captura('desc_moneda','varchar');
		$this->captura('id_int_comprobante','int4');
		$this->captura('nro_tramite','varchar');
		$this->captura('desc_comprobante','varchar');
		
		
		$this->captura('importe_pendiente','numeric');
		$this->captura('importe_anticipo','numeric');
		$this->captura('importe_retgar','numeric');
		$this->captura('importe_neto','numeric');		
		$this->captura('id_auxiliar','integer');
		$this->captura('codigo_auxiliar','varchar');
		$this->captura('nombre_auxiliar','varchar');		
		$this->captura('id_tipo_doc_compra_venta','integer');
		$this->captura('desc_tipo_doc_compra_venta','varchar');		
		$this->captura('importe_aux_neto','numeric');
		$this->captura('id_funcionario','integer');		
		$this->captura('desc_funcionario2','varchar');
		$this->captura('fecha_cbte','date');
		$this->captura('estado_cbte','varchar');
		
		$this->captura('id_cobro_simple','int4');
		$this->captura('fecha_cobro','date');
		$this->captura('nro_tramite_cobro','varchar');
		
		$this->captura('importe_cobro','numeric');
		$this->captura('id_moneda_cobro','int4');
		$this->captura('desc_moneda_cobro','varchar');
		$this->captura('importe_cobro_factura','numeric');
		$this->captura('id_periodo','int4');
		$this->captura('id_gestion','int4');
		
		
		
		$this->captura('importe_cobrado_mb','numeric');
		$this->captura('importe_cobrado_mt','numeric');
		$this->captura('saldo_por_cobrar','numeric');
		
		
		$this->captura('id_contrato','int4');//1B					20/09/2018     EGS
		$this->captura('nro_contrato','varchar');  //1B					20/09/2018     EGS
		
		
		

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		
		return $this->respuesta;
	}
	
	
	function listarFactura(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='cbr.ft_cobro_recibo_sel ';
		$this->transaccion='CBR_LISFAC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		
		//captura parametros adicionales para el count
		
		$this->capturaCount('total_importe_ice','numeric');
		$this->capturaCount('total_importe_excento','numeric');
		$this->capturaCount('total_importe_it','numeric');
		$this->capturaCount('total_importe_iva','numeric');
		$this->capturaCount('total_importe_descuento','numeric');
		$this->capturaCount('total_importe_doc','numeric');
		
		$this->capturaCount('total_importe_retgar','numeric');
		$this->capturaCount('total_importe_anticipo','numeric');
		$this->capturaCount('tota_importe_pendiente','numeric');
		$this->capturaCount('total_importe_neto','numeric');
		$this->capturaCount('total_importe_descuento_ley','numeric');
		$this->capturaCount('total_importe_pago_liquido','numeric');
		//$this->capturaCount('total_importe_cobro_factura','numeric');
		$this->capturaCount('total_importe_aux_neto','numeric');
		/*$this->capturaCount('importe_cobro','numeric');*/
		
		
		
		//$this->setParametro('nombre_vista','nombre_vista','varchar');
		//$this->setParametro('id_proveedor','id_proveedor','int4');
		
		
				
		//Definicion de la lista del resultado del query
		$this->captura('id','int8');
		$this->captura('id_doc_compra_venta','int8');
		$this->captura('revisado','varchar');
		$this->captura('movil','varchar');
		$this->captura('tipo','varchar');
		$this->captura('importe_excento','numeric');
		$this->captura('id_plantilla','int4');
		$this->captura('fecha','date');
		$this->captura('nro_documento','varchar');
		$this->captura('nit','varchar');
		$this->captura('importe_ice','numeric');
		$this->captura('nro_autorizacion','varchar');
		$this->captura('importe_iva','numeric');
		$this->captura('importe_descuento','numeric');
		$this->captura('importe_doc','numeric');
		$this->captura('sw_contabilizar','varchar');
		$this->captura('tabla_origen','varchar');
		$this->captura('estado','varchar');
		$this->captura('id_depto_conta','int4');
		$this->captura('id_origen','int4');
		$this->captura('obs','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('codigo_control','varchar');
		$this->captura('importe_it','numeric');
		$this->captura('razon_social','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		$this->captura('desc_depto','varchar');
		$this->captura('desc_plantilla','varchar');
		$this->captura('importe_descuento_ley','numeric');
		$this->captura('importe_pago_liquido','numeric');
		$this->captura('nro_dui','varchar');
		$this->captura('id_moneda','int4');
		$this->captura('desc_moneda','varchar');
		$this->captura('id_int_comprobante','int4');
		$this->captura('nro_tramite','varchar');
		$this->captura('desc_comprobante','varchar');
		
		
		$this->captura('importe_pendiente','numeric');
		$this->captura('importe_anticipo','numeric');
		$this->captura('importe_retgar','numeric');
		$this->captura('importe_neto','numeric');		
		$this->captura('id_auxiliar','integer');
		$this->captura('codigo_auxiliar','varchar');
		$this->captura('nombre_auxiliar','varchar');		
		$this->captura('id_tipo_doc_compra_venta','integer');
		$this->captura('desc_tipo_doc_compra_venta','varchar');		
		$this->captura('importe_aux_neto','numeric');
		$this->captura('id_funcionario','integer');		
		$this->captura('desc_funcionario2','varchar');
		$this->captura('fecha_cbte','date');
		$this->captura('estado_cbte','varchar');
		$this->captura('nro_cbte','varchar');	///EGS  24/08/2018	1A	
		$this->captura('importe_cobrado_mb','numeric');
		$this->captura('importe_cobrado_mt','numeric');
		$this->captura('fecha_ultimo_pago','date');
		$this->captura('saldo_por_cobrar','numeric');
		

		$this->captura('id_contrato','int4');//1B					20/09/2018     EGS
		$this->captura('nro_contrato','varchar');  //1B			20/09/2018     EGS
        $this->captura('codigo_aplicacion','varchar');// #7
		

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump($this->respuesta); exit;
		//Devuelve la respuesta
		return $this->respuesta;
	}

	  
	

	
			
	

			
}
?>