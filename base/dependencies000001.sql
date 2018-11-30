/***********************************I-DEP-RAC-CBR-0-02/05/2018*****************************************/




CREATE OR REPLACE VIEW cbr.vcobro_simple_cbte(
    id_cobro_simple,
    id_moneda,
    id_depto_conta,
    id_depto_lb,
    id_cuenta_bancaria,
    importe,
    importe_mb,
    importe_ma,
    importe_mt,
    tipo_cambio,
    tipo_cambio_ma,
    tipo_cambio_mt,
    id_config_cambiaria,
    desc_proveedor,
    id_funcionario,
    id_proveedor,
    id_proceso_wf,
    id_estado_wf,
    nro_tramite,
    obs,
    id_periodo,
    id_gestion,
    id_int_comprobante,
    id_int_comprobante_pago,
    fecha)
AS
  SELECT ps.id_cobro_simple,
         ps.id_moneda,
         ps.id_depto_conta,
         ps.id_depto_lb,
         ps.id_cuenta_bancaria,
         ps.importe,
         ps.importe_mb,
         ps.importe_ma,
         ps.importe_mt,
         ps.tipo_cambio,
         ps.tipo_cambio_ma,
         ps.tipo_cambio_mt,
         ps.id_config_cambiaria,
         pro.desc_proveedor,
         ps.id_funcionario,
         ps.id_proveedor,
         ps.id_proceso_wf,
         ps.id_estado_wf,
         ps.nro_tramite,
         ps.obs,
         per.id_periodo,
         per.id_gestion,
         ps.id_int_comprobante,
         ps.id_int_comprobante_pago,
         ps.fecha
  FROM cbr.tcobro_simple ps
       JOIN param.vproveedor pro ON pro.id_proveedor = ps.id_proveedor
       JOIN param.tperiodo per ON ps.fecha >= per.fecha_ini AND ps.fecha <=
         per.fecha_fin;
         
         
         
         
 CREATE OR REPLACE VIEW cbr.vcobro_simple_det(
    id_cobro_simple,
    id_moneda,
    id_depto_conta,
    id_depto_lb,
    id_cuenta_bancaria,
    id_funcionario,
    id_proveedor,
    id_proceso_wf,
    id_estado_wf,
    nro_tramite,
    obs,
    id_cobro_simple_det,
    id_doc_compra_venta,
    importe,
    id_plantilla,
    desc_dcv)
AS
  SELECT psd.id_cobro_simple,
         ps.id_moneda,
         ps.id_depto_conta,
         ps.id_depto_lb,
         ps.id_cuenta_bancaria,
         ps.id_funcionario,
         ps.id_proveedor,
         ps.id_proceso_wf,
         ps.id_estado_wf,
         ps.nro_tramite,
         ps.obs,
         psd.id_cobro_simple_det,
         dcv.id_doc_compra_venta,
         psd.importe,
         dcv.id_plantilla,
         (('Doc: '::text || dcv.nro_documento::text) || ' del '::text) ||
           dcv.fecha::character varying::text AS desc_dcv
  FROM cbr.tcobro_simple ps
       JOIN cbr.tcobro_simple_det psd ON psd.id_cobro_simple =
         ps.id_cobro_simple
       JOIN conta.tdoc_compra_venta dcv ON dcv.id_doc_compra_venta =
         psd.id_doc_compra_venta;        

/***********************************F-DEP-RAC-CBR-0-02/05/2018*****************************************/

/***********************************I-DEP-EGS-CBR-0-24/10/2018*****************************************/
CREATE OR REPLACE VIEW cbr.vcobro_simple(
    id_cobro_simple,
    id_moneda,
    id_depto_conta,
    id_depto_lb,
    id_cuenta_bancaria,
    id_funcionario,
    id_proveedor,
    id_proceso_wf,
    id_estado_wf,
    nro_tramite,
    obs,
    importe,
    desc_dcv)
AS
  SELECT ps.id_cobro_simple,
         ps.id_moneda,
         ps.id_depto_conta,
         ps.id_depto_lb,
         ps.id_cuenta_bancaria,
         ps.id_funcionario,
         ps.id_proveedor,
         ps.id_proceso_wf,
         ps.id_estado_wf,
         ps.nro_tramite,
         ps.obs,
         ps.importe,
         'Doc: '::text || ps.nro_tramite::text AS desc_dcv
  FROM cbr.tcobro_simple ps;

/***********************************F-DEP-EGS-CBR-0-24/10/2018*****************************************/

