/***********************************I-SCP-RAC-CBR-1-02/05/2018****************************************/

  
  
  
  CREATE TABLE cbr.ttipo_cobro_simple (
  id_tipo_cobro_simple SERIAL,
  codigo VARCHAR(30),
  nombre VARCHAR(150),
  plantilla_cbte VARCHAR(50),
  plantilla_cbte_1 VARCHAR(50),
  flujo_wf VARCHAR(50),
  CONSTRAINT ttipo_cobro_simple_pkey PRIMARY KEY(id_tipo_cobro_simple)
) INHERITS (pxp.tbase)

WITH (oids = false);

COMMENT ON COLUMN cbr.ttipo_cobro_simple.flujo_wf
IS 'Código del workflow por tipo de pago simple';



CREATE TABLE cbr.tcobro_simple (
  id_cobro_simple SERIAL,
  id_depto_conta INTEGER,
  nro_tramite VARCHAR(100),
  fecha DATE,
  id_funcionario INTEGER,
  estado VARCHAR(30),
  id_estado_wf INTEGER,
  id_proceso_wf INTEGER,
  obs VARCHAR(500),
  id_cuenta_bancaria INTEGER,
  id_depto_lb INTEGER,
  id_proveedor INTEGER,
  id_moneda INTEGER,
  id_int_comprobante INTEGER,
  id_int_comprobante_pago INTEGER,
  id_tipo_cobro_simple INTEGER,
  id_funcionario_pago INTEGER,
  nro_tramite_asociado VARCHAR(150),
  importe NUMERIC(18,2),
  id_obligacion_pago INTEGER,
  id_caja INTEGER,
  id_solicitud_efectivo INTEGER,
  importe_mb NUMERIC(18,2),
  tipo_cambio NUMERIC DEFAULT 1 NOT NULL,
  importe_mt NUMERIC(18,2),
  tipo_cambio_mt NUMERIC,
  forma_cambio VARCHAR(30) DEFAULT 'oficial'::character varying NOT NULL,
  tipo_cambio_ma NUMERIC,
  importe_ma NUMERIC,
  id_config_cambiaria INTEGER,
  CONSTRAINT tcobro_simple_pkey PRIMARY KEY(id_cobro_simple),
  CONSTRAINT tcobro_simple_fk FOREIGN KEY (id_proveedor)
    REFERENCES param.tproveedor(id_proveedor)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tcobro_simple_fk1 FOREIGN KEY (id_moneda)
    REFERENCES param.tmoneda(id_moneda)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

COMMENT ON COLUMN cbr.tcobro_simple.nro_tramite_asociado
IS 'Número del trámite asociado al pago a realizar, puede ser del proceso de compra';

COMMENT ON COLUMN cbr.tcobro_simple.id_obligacion_pago
IS 'ID de la obligacion de pago para relizar el prorrateo por partida en el comprobante diario';

COMMENT ON COLUMN cbr.tcobro_simple.forma_cambio
IS 'oficial covenido compra venta, tipo de cambio';




CREATE TABLE cbr.tcobro_simple_det (
  id_cobro_simple_det SERIAL,
  id_cobro_simple INTEGER NOT NULL,
  id_doc_compra_venta INTEGER,
  importe NUMERIC(18,2) DEFAULT 0 NOT NULL,
  importe_mb NUMERIC(18,2) DEFAULT 0 NOT NULL,
  importe_mt NUMERIC(18,2) DEFAULT 0 NOT NULL,
  importe_ma NUMERIC,
  CONSTRAINT tpago_cobro_det_pkey PRIMARY KEY(id_cobro_simple_det),
  CONSTRAINT tpago_cobro_det_fk FOREIGN KEY (id_cobro_simple)
    REFERENCES cbr.tcobro_simple(id_cobro_simple)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tpago_cobro_det_fk1 FOREIGN KEY (id_doc_compra_venta)
    REFERENCES conta.tdoc_compra_venta(id_doc_compra_venta)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

/***********************************F-SCP-RAC-CBR-1-02/05/2018****************************************/

/***********************************I-DAT-EGS-CBR-07-03/05/2018*****************************************/

CREATE TABLE cbr.tcobro_excel (
  nro_documento VARCHAR(50),
  fecha DATE,
  importe_doc NUMERIC,
  importe_mb NUMERIC,
  concepto VARCHAR(500),
  ceco VARCHAR(10000),
  desc_proveedor VARCHAR(500)
) INHERITS (pxp.tbase)
WITH (oids = false);

CREATE TABLE cbr.tcobro_excel_2 (
  nro_documento VARCHAR(50),
  fecha DATE,
  importe_doc NUMERIC,
  importe_mb NUMERIC,
  concepto VARCHAR(500),
  ceco VARCHAR(10000),
  desc_proveedor VARCHAR(500)
) INHERITS (pxp.tbase)
WITH (oids = false);

/***********************************F-DAT-EGS-CBR-07-03/05/2018*****************************************/