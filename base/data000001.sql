/***********************************I-DAT-RAC-CBR-48-02/05/2018*****************************************

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

/***********************************F-DAT-RAC-CBR-48-02/05/2018*****************************************

/***********************************I-DAT-EGS-CBR-48-02/05/2018*****************************************

  
INSERT INTO cbr.ttipo_cobro_simple ("id_usuario_reg", "estado_reg", "id_tipo_cobro_simple", "codigo", "nombre", "plantilla_cbte", "plantilla_cbte_1", "flujo_wf")
VALUES 
  (1, E'activo', 2, E'CBRCMN', E'Cobro Comun', E'CBRSP', E'', E'CBR'),
  (1, E'activo', 3, E'CBRCMNRE', E'Cobro Regularizado', E'', E'', E'CBRRE'),
  (1, E'activo', 4, E'CBRCMNRG', E'Cobro Retención Garantía', E'CBRRG', E'', E'CBRRG'),
  (1, E'activo', 5, E'CBRCMNRGRE', E'Cobro Retención Garantía Regularizado', E'', E'', E'CBRRE'),
  (1, E'activo', 6, E'CBRCMNAT', E'Cobro Anticipo', E'ANTICIPOCBR', E'', E'CBRAT'),
  (1, E'activo', 7, E'CBRCMNATRE', E'Cobro Anticipos Regularizado', E'', E'', E'CBRRE');
 

/***********************************F-DAT-EGS-CBR-48-02/05/2018*****************************************