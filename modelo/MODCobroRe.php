<?php
/**
*@package pXP
*@file gen-MODCobroSimple.php
*@author  (admin)
*@date 31-12-2017 12:33:30
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 * ISSUE			FECHA		AUTOR					DESCRIPCION
 *   *1A			21/08/2018		EGS					Se aumentaron campos en las funciones listarCobroSimple() ,insertarCobroSimple(),modificarCobroSimple 

 * 
*/

class MODCobroRe extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarCobroRe(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='cbr.ft_cobro_re_sel';
		$this->transaccion='CBR_CBRE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		
				
		//Definicion de la lista del resultado del query
		$this->captura('id','int4');
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
		
		
		
		$this->captura('tipo_cambio','numeric');
		$this->captura('tipo_cambio_mt','numeric');
		$this->captura('tipo_cambio_ma','numeric');
		$this->captura('id_config_cambiaria','integer');
		$this->captura('importe_mt','numeric');
		$this->captura('importe_mb','numeric');
		$this->captura('importe_ma','numeric');
		$this->captura('forma_cambio','varchar');
		$this->captura('id_int_comprobante','int4'); ////////////EGS-I-21/08/2018///    1A	
		$this->captura('nro_cbte','varchar');     ////////////EGS-I-21/08/2018///    1A	
		
        $this->captura('globalComun','varchar');
		$this->captura('globalRetgar','varchar');
		$this->captura('globalAnti','varchar');   
                            
		$this->captura('id_gestion','integer');
		$this->captura('id_periodo','integer');
		//$this->captura('nro_documento','varchar');
		
		
        
		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
	function listarDocCompraVentaRe(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='cbr.ft_cobro_re_sel ';
		$this->transaccion='CBR_CBRFA_SEL';
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
		$this->capturaCount('total_cobrado','numeric');
		$this->capturaCount('total_saldo_por_cobrar','numeric');
		
		
		
		$this->setParametro('nombre_vista','nombre_vista','varchar');
		
		//$this->setParametro('id_cobro_simple','id_cobro_simple','integer');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('id_tipo_cobro_simple','id_tipo_cobro_simple','varchar');////1A					24/08/2018			EGS 
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
		
		$this->captura('id_cobro_simple','int4');
		$this->captura('id_tipo_cobro_simple','int4');/////1A					24/08/2018			EGS 
		$this->captura('fecha_cobro','date');
		$this->captura('nro_tramite_cobro','varchar');
		
		$this->captura('importe_cobro','numeric');
		$this->captura('id_moneda_cobro','int4');
		$this->captura('desc_moneda_cobro','varchar');
		$this->captura('importe_cobro_factura','numeric');
		$this->captura('id_periodo','int4');
		$this->captura('id_gestion','int4');
		$this->captura('id_proveedor','int4');
		$this->captura('desc_proveedor','varchar');
		
		
		
		$this->captura('importe_cobrado_mb','numeric');
		$this->captura('importe_cobrado_mt','numeric');
		$this->captura('saldo_por_cobrar','numeric');
		

		

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump($this->respuesta); exit;
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function listarCobroReporte(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='cbr.ft_cobro_re_sel';
		$this->transaccion='CBR_CBREP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		
				
		//Definicion de la lista del resultado del query
		$this->captura('id','int4');
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
		
		
		
		$this->captura('tipo_cambio','numeric');
		$this->captura('tipo_cambio_mt','numeric');
		$this->captura('tipo_cambio_ma','numeric');
		$this->captura('id_config_cambiaria','integer');
		$this->captura('importe_mt','numeric');
		$this->captura('importe_mb','numeric');
		$this->captura('importe_ma','numeric');
		$this->captura('forma_cambio','varchar');
		$this->captura('id_int_comprobante','int4'); ////////////EGS-I-21/08/2018///    1A	
		$this->captura('nro_cbte','varchar');     ////////////EGS-I-21/08/2018///    1A	

                            
		$this->captura('id_gestion','integer');
		$this->captura('id_periodo','integer');
		
		$this->captura('importe_cobro_factura','numeric');
		$this->captura('id_doc_compra_venta','int8');
		$this->captura('nro_documento','varchar');
		$this->captura('razon_social','varchar');
		
		
        
		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>