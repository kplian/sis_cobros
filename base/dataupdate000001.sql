/********************************************I-DAUP-AUTOR-SCHEMA-0-31/02/2019********************************************/
--SHEMA : Esquema (CONTA) contabilidad         AUTHOR:Siglas del autor de los scripts' dataupdate000001.txt
/********************************************F-DAUP-AUTOR-SCHEMA-0-31/02/2019********************************************/


/********************************************I-DAUP-MGM-CBR-ETR-2-05/01/2020********************************************/
--rollback
--UPDATE cbr.tcobro_simple_det SET id_doc_compra_venta=189335 WHERE id_cobro_simple_det=6284;
--commit
UPDATE cbr.tcobro_simple_det SET id_doc_compra_venta=NULL WHERE id_cobro_simple_det=6284;
/********************************************F-DAUP-MGM-CBR-ETR-2-05/01/2020********************************************/

