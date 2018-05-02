/***********************************I-DAT-GSS-CONTA-48-20/02/2013*****************************************/

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

;


/***********************************F-DAT-GSS-CONTA-48-20/02/2013*****************************************/


