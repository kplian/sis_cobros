/***********************************I-DAT-RAC-CBR-48-02/05/2018*****************************************/

/*
*	Author: Gonzalo Sarmiento Sejas GSS
*	Date: 20/02/2013
*	Description: Build the menu definition and the composition
*/
/*

Para  definir la la metadata, menus, roles, etc

1) sincronize ls funciones y procedimientos del sistema
2)  verifique que la primera linea de los datos sea la insercion del sistema correspondiente
3)  exporte los datos a archivo SQL (desde la interface de sistema en sis_seguridad), 
    verifique que la codificacion  se mantenga en UTF8 para no distorcionar los caracteres especiales
4)  remplaze los sectores correspondientes en este archivo en su totalidad:  (el orden es importante)  
                             menu, 
                             funciones, 
                             procedimietnos
*/


INSERT INTO segu.tsubsistema ( codigo, nombre, prefijo, estado_reg, nombre_carpeta, id_subsis_orig)
VALUES ('CBR', 'Sistema de Cobranza', 'CBR', 'activo', 'contabilidad', NULL);

-------------------------------------
--DEFINICION DE INTERFACES
-----------------------------------

select pxp.f_insert_tgui ('COBROS', '', 'CBR', 'si', 1, '', 1, '', '', 'CBR');
select pxp.f_insert_tgui ('Tipos de Cobros', 'Tipos de Cobros', 'TIPCOBRO', 'si', 1, 'sis_cobros/vista/tipo_cobro_simple/TipoCobroSimple.php', 2, '', 'TipoCobroSimple', 'CBR');
select pxp.f_insert_tgui ('Solicitar Cobros', 'Solicitar cobros', 'SOLCOB', 'si', 2, 'sis_cobros/vista/cobro_simple/CobroSimpleSol.php', 2, '', 'CobroSimpleSol', 'CBR');

select pxp.f_insert_testructura_gui ('CBR', 'SISTEMA');
select pxp.f_insert_testructura_gui ('TIPCOBRO', 'CBR');
select pxp.f_insert_testructura_gui ('SOLCOB', 'CBR');

select pxp.f_insert_tgui ('Reporte de Cobros', '', 'RDC', 'si', 3, 'sis_cobros/vista/factura/FormFiltro.php', 2, '', 'FormFiltro', 'CBR');
select pxp.f_insert_testructura_gui ('RDC', 'CBR');

/***********************************F-DAT-RAC-CBR-48-02/05/2018*****************************************/

/***********************************I-DAT-EGS-CBR-48-02/05/2018*****************************************/

  
INSERT INTO cbr.ttipo_cobro_simple ("id_usuario_reg", "estado_reg", "id_tipo_cobro_simple", "codigo", "nombre", "plantilla_cbte", "plantilla_cbte_1", "flujo_wf")
VALUES 
  (1, E'activo', 2, E'CBRCMN', E'Cobro Comun', E'CBRSP', E'', E'CBR'),
  (1, E'activo', 3, E'CBRCMNRE', E'Cobro Regularizado', E'', E'', E'CBRRE'),
  (1, E'activo', 4, E'CBRCMNRG', E'Cobro Retención Garantía', E'CBRRG', E'', E'CBRRG'),
  (1, E'activo', 5, E'CBRCMNRGRE', E'Cobro Retención Garantía Regularizado', E'', E'', E'CBRRE'),
  (1, E'activo', 6, E'CBRCMNAT', E'Cobro Anticipo', E'ANTICIPOCBR', E'', E'CBRAT'),
  (1, E'activo', 7, E'CBRCMNATRE', E'Cobro Anticipos Regularizado', E'', E'', E'CBRRE');
 

/***********************************F-DAT-EGS-CBR-48-02/05/2018*****************************************/


/***********************************I-DAT-EGS-CBR-0-30/08/2018*****************************************/

----cobros regularizados---
select wf.f_import_tproceso_macro ('insert','CBRRE', 'CBR', 'Cobros Facturas Regularizadas','si');
select wf.f_import_tcategoria_documento ('insert','legales', 'Legales');
select wf.f_import_tcategoria_documento ('insert','proceso', 'Proceso');
select wf.f_import_ttipo_proceso ('insert','CBRRE',NULL,NULL,'CBRRE','','cd.vpago_simple','cd.vpago_simple','si','','','','CBRRE',NULL);
select wf.f_import_ttipo_estado ('insert','borrador','CBRRE','Borrador','si','no','no','ninguno','','ninguno','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','','cbr.f_fun_regreso_cobro_simple_wf','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','finalizado','CBRRE','Finalizado','no','no','si','ninguno','','ninguno','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','cbr.f_fun_inicio_cobro_simple_wf','cbr.f_fun_regreso_cobro_simple_wf','','','','','',NULL);
select wf.f_import_testructura_estado ('insert','borrador','finalizado','CBRRE',1,'');

----cobros retencion garantias---
select wf.f_import_tproceso_macro ('insert','CBRRG', 'CBR', 'Cobros Facturas Retencion con Garantias','si');
select wf.f_import_tcategoria_documento ('insert','legales', 'Legales');
select wf.f_import_tcategoria_documento ('insert','proceso', 'Proceso');
select wf.f_import_ttipo_proceso ('insert','CBRRG',NULL,NULL,'CBRRG','Devengar y Pagar (Simple)','cd.vpago_simple','cd.vpago_simple','si','','','','CBRRG',NULL);
select wf.f_import_ttipo_estado ('insert','borrador','CBRRG','Borrador','si','no','no','ninguno','','ninguno','','','si','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','','cbr.f_fun_regreso_cobro_simple_wf','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','pendiente','CBRRG','Pendiente','no','si','no','segun_depto','','depto_func_list','cbr.f_lista_depto_lb_cobro_simple_wf','','si','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','cbr.f_fun_inicio_cobro_simple_wf','cbr.f_fun_regreso_cobro_simple_wf','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','vbtesoreria','CBRRG','VoBo Tesoreria','no','no','no','segun_depto','','depto_func_list','cbr.f_lista_depto_lb_cobro_simple_wf','','si','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','cbr.f_fun_inicio_cobro_simple_wf','cbr.f_fun_regreso_cobro_simple_wf','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','finalizado','CBRRG','Finalizado','no','no','si','anterior','','ninguno','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','cbr.f_fun_inicio_cobro_simple_wf','cbr.f_fun_regreso_cobro_simple_wf','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','pendiente_pago','CBRRG','Pendiente Pago','no','si','no','anterior','','ninguno','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','cbr.f_fun_inicio_cobro_simple_wf','cbr.f_fun_regreso_cobro_simple_wf','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','rendicion','CBRRG','Rendición','no','no','no','funcion_listado','cd.f_lista_funcionario_sol_pago_simple_wf_sel','ninguno','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','cbr.f_fun_inicio_cobro_simple_wf','cbr.f_fun_regreso_cobro_simple_wf','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','tesoreria','CBRRG','Revisión Tesorería','no','no','no','segun_depto','','depto_func_list','cbr.f_lista_depto_lb_cobro_simple_wf','','si','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','cbr.f_fun_inicio_cobro_simple_wf','cbr.f_fun_regreso_cobro_simple_wf','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','vbconta','CBRRG','VoBo Contador','no','no','no','segun_depto','','depto_func_list','cd.f_lista_depto_conta_pago_simple_wf','','si','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','','','','','','','',NULL);
select wf.f_import_ttipo_documento ('insert','DOCRES','CBRRG','Documento de Respaldo (Factura)','Documento de Respaldo (Factura)','','escaneado',1.00,'{}');
select wf.f_import_testructura_estado ('insert','pendiente_pago','finalizado','CBRRG',1,'');
select wf.f_import_testructura_estado ('insert','vbconta','pendiente','CBRRG',1,'');
select wf.f_import_testructura_estado ('insert','tesoreria','pendiente','CBRRG',1,'');
select wf.f_import_testructura_estado ('insert','pendiente','finalizado','CBRRG',1,'');
select wf.f_import_testructura_estado ('insert','borrador','vbtesoreria','CBRRG',1,'');
select wf.f_import_testructura_estado ('insert','vbtesoreria','pendiente','CBRRG',1,'');

/***********************************F-DAT-EGS-CBR-0-30/08/2018*****************************************/


/****************************I-DAT-EGS-CBR-0-30/08/2018******************/
INSERT INTO pxp.variable_global ("variable", "valor", "descripcion")
VALUES
 (E'v_cobro_comun', E'CBRCMN,CBRCMNRE', E'codigo cobro comun'),
 (E'v_cobro_retencion_garantia', E'CBRCMNRG,CBRCMNRGRE', E'codigo cobro con retencion de garantia'),
 (E'v_cobro_anticipo', E'CBRCMNAT,CBRCMNATRE', E'codigo cobro anticipo');

/****************************F-DAT-EGS-CBR-0-30/09/2018******************/
 
 