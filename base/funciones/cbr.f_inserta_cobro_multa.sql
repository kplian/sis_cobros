CREATE OR REPLACE FUNCTION cbr.f_inserta_cobro_multa (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar
)
RETURNS varchar AS
$body$
/*
*

*  DESC:    funcion que inserta cobro en cobros
*  
*
*/

DECLARE

	v_nombre_funcion   	 	text;
    v_resp    			 	varchar;
    v_resp_2    			varchar;
    v_mensaje 			 	varchar;
	
    v_codigo_trans			varchar;
    v_codigo_trans_2		varchar;
    
    v_parametros           	record;
    v_id_tipo_compra_venta	integer;
    v_tabla					varchar;
    v_tabla_2				varchar;


    v_id_doc_compra_venta	INTEGER;
    
    v_id_estado_actual  	integer;
    
    va_id_tipo_estado 		integer[];
    va_codigo_estado 		varchar[];
    va_disparador    		varchar[];
    va_regla         		varchar[]; 
    va_prioridad     		integer[];
    	
     
    
    v_id_tipo_estado  		integer;
 
    
    item					record;
    v_tipo_cambio 			numeric;
    v_tipo_cambio_mt 	    numeric;
    v_tipo_cambio_ma		numeric;
    v_id_cobro_simple       varchar;
    
    
    p_id_usuario  			integer;
    p_id_usuario_ai 		integer;
    p_usuario_ai 			varchar;
   
	v_registros				record;
    
    v_id_auxiliar           integer;
    v_importe_debe_mb		numeric;
    v_importe_haber_mb		numeric;
    v_saldo					numeric;
    
BEGIN

	 v_nombre_funcion = 'cbr.f_inserta_cobro_multa';
	 --v_parametros = pxp.f_get_record(p_tabla);
	 v_resp	= 'exito';

	For item in (
    	
      Select 
  	   dcv.id_doc_compra_venta,       
       cbre.fecha,
       cbre.nro_documento,
       cbre.importe_doc,
       dcv.importe_pendiente,
       cbre.importe_mb,
       cbre.concepto,
       dcv.id_auxiliar,
       provee.id_proveedor,
       vprovee.desc_proveedor,
      (param.f_get_tipo_cambio(1,cbre.fecha::DATE,'O')):: numeric AS tipo_cambio,
      (param.f_get_tipo_cambio(2,cbre.fecha::DATE,'O')):: numeric AS tipo_cambio_mt,
      (param.f_get_tipo_cambio(3,cbre.fecha::DATE,'O')):: numeric AS tipo_cambio_ma
       from cbr.tcobro_excel_2 cbre
       left JOIN conta.tdoc_compra_venta dcv on dcv.nro_documento = cbre.nro_documento
       inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
       left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
       left join param.tproveedor provee on provee.id_auxiliar = aux.id_auxiliar
       left join param.vproveedor vprovee on vprovee.id_proveedor = provee.id_proveedor
                           
       WHERE
       dcv.nro_documento = cbre.nro_documento and  pla.tipo_plantilla = 'venta' and dcv.importe_doc=cbre.importe_doc and cbre.fecha=dcv.fecha and cbre.nro_documento not in ('214','218')
	
    )LOOP
  		select
	
              intra.id_auxiliar,
              sum (COALESCE(intra.importe_debe_mb,0)) as importe_debe_mb,
              sum (COALESCE(intra.importe_haber_mb,0))as importe_haber_mb,
              (sum(COALESCE(intra.importe_debe_mb,0))- sum(COALESCE(intra.importe_haber_mb,0))) as saldo
              into 
              v_id_auxiliar,
              v_importe_debe_mb,
              v_importe_haber_mb,
              v_saldo
          from conta.tint_transaccion intra

          left join param.tproveedor pro on pro.id_auxiliar = intra.id_auxiliar
          inner join param.vproveedor vpro on vpro.id_proveedor = pro.id_proveedor
          left join conta.tcuenta		cue	on cue.id_cuenta = intra.id_cuenta
          left join conta.tint_comprobante incbt on incbt.id_int_comprobante = intra.id_int_comprobante

          where incbt.estado_reg = 'validado' and cue.nro_cuenta ='1.1.3.10.001.001' and date_part('year', intra.fecha_reg) = '2018' and intra.id_auxiliar = item.id_auxiliar

          group by intra.id_auxiliar  ;
    
         if v_saldo = 0 and item.importe_doc < 50000 THEN
    
    
                  
                           --raise Exception' %',v_id_doc_compra_venta;

                         --verificar si existe el documento
                         select 
                            csd.id_doc_compra_venta 
                            into 
                            v_id_doc_compra_venta
                         from cbr.tcobro_simple_det csd
                         where 
                            csd.id_doc_compra_venta = item.id_doc_compra_venta;
                      
                      --si no existe el documento se inserta
                        if (v_id_doc_compra_venta is null) then
                        
                        	p_id_usuario = 1;
                          	p_id_usuario_ai = 1;
                          	p_usuario_ai = null;


                           v_codigo_trans = 'CBR_PAGSIM_INS';

                              --crear tabla 
                          v_tabla = pxp.f_crear_parametro(ARRAY[		'_nombre_usuario_ai',
                                                                      '_id_usuario_ai',
                                                                    'estado_reg',
                                                                    'id_depto_conta',
                                                                    'nro_tramite',
                                                                    'fecha',
                                                                    'id_funcionario',
                                                                    'estado',
                                                                    'id_estado_wf',
                                                                    'id_proceso_wf',
                                                                    'obs',
                                                                    'id_cuenta_bancaria',
                                                                    'id_depto_lb',
                                                                    'id_moneda',
                                                                    'id_proveedor',
                                                                    'id_funcionario_pago',
                                                                    'id_tipo_cobro_simple',
                                                                    'importe',
                                                                    'id_obligacion_pago',
                                                                    'id_caja',
                                                                    'id_int_comprobante',
                                                                    'tipo_cambio',
                                                                    'tipo_cambio_ma',
                                                                    'tipo_cambio_mt',
                                                                    'id_config_cambiaria',
                                                                    'forma_cambio'],
                                          ARRAY[	
                                              '', ---'_nombre_usuario_ai',
                                              '',  -----'_id_usuario_ai',
                                              'activo',	--'estado_reg',
                                              '3',	--'id_depto_conta',
                                               '',	--'nro_tramite',
                                             coalesce(item.fecha::varchar,''),	--'fecha',
                                              -- now()::varchar,	--'fecha',
                                              '292',	--'id_funcionario',
                                              '',	--'estado',
                                             '',	--'id_estado_wf',
                                              '',	--'id_proceso_wf',
                                              coalesce(item.concepto::varchar,''),	--'obs',
                                             '', 	--'id_cuenta_bancaria',
                                              '',	--'id_depto_lb',
                                              '1'::VARCHAR,	--'id_moneda',
                                              coalesce(item.id_proveedor::varchar,''),	--'id_proveedor',
                                              '',	--'id_funcionario_pago',
                                              '3'::varchar,	--'id_tipo_cobro_simple',
                                              coalesce(item.importe_doc::varchar,''),	--'importe',
                                              '',	--'id_obligacion_pago',
                                              '',	--'id_caja',
                                              '',	--'id_int_comprobante',
                                              coalesce(item.tipo_cambio::varchar,''),	--'tipo_cambio',
                                              coalesce(item.tipo_cambio_ma::varchar,''),	--'tipo_cambio_ma',
                                              coalesce(item.tipo_cambio_mt ::varchar,''),	--'tipo_cambio_mt',
                                              '1'::varchar,	--'id_config_cambiaria',
                                              'oficial' --'forma_cambio'
                                              ],
                                          ARRAY[
                                              'varchar',
                                              'integer',	
                                              'varchar',
                                              'int4',	
                                              'varchar',
                                              'date',
                                              'int4',
                                              'varchar',
                                              'int4',
                                              'int4',
                                              'varchar',
                                              'int4',
                                              'int4',
                                              'int4',
                                              'int4',
                                              'int4',
                                              'int4',
                                              'numeric',
                                              'int4',
                                              'int4',
                                              'int4',
                                              'numeric',
                                              'numeric',
                                              'numeric',
                                              'integer',
                                              'varchar'
                                             ]
                                          );
                         
                          
                          v_resp = cbr.ft_cobro_simple_ime(p_administrador,p_id_usuario,v_tabla,v_codigo_trans);
                          v_id_cobro_simple = pxp.f_recupera_clave(v_resp,'id_cobro_simple');
                          v_id_cobro_simple	=  split_part(v_id_cobro_simple, '{', 2);
                          v_id_cobro_simple	=  split_part(v_id_cobro_simple, '}', 1);
                          --raise exception '%',v_id_cobro_simple;
                          
                          v_codigo_trans_2 = 'CBR_RELFAC_IME';

                              --crear tabla 
                          v_tabla_2 = pxp.f_crear_parametro(ARRAY['_nombre_usuario_ai',
                                                                   '_id_usuario_ai',
                                                                    'id_cobro_simple',
                                                                    'id_doc_compra_venta',
                                                                    'monto_prorrateo'
                                                                   ],
                                          ARRAY[	
                                              '', ---'_nombre_usuario_ai',
                                              '',  -----'_id_usuario_ai',
                                              coalesce(v_id_cobro_simple::varchar,''),	--'id_cobro_simple',  
                                              coalesce(item.id_doc_compra_venta::varchar,''),	--'id_doc_compra_venta',
                                              coalesce(item.importe_doc::varchar,'')	--'monto_prorrateo',
                                                      
                                              ],
                                          ARRAY[
                                              'varchar',
                                              'integer',	
                                              'int4',	
                                              'int4',
                                              'numeric'
                                             ]
                                          );
                         
                          
                          v_resp_2 = cbr.ft_cobro_simple_det_ime(p_administrador,p_id_usuario,v_tabla_2,v_codigo_trans_2);
                          
                         
                        
                         select 
                              cs.id_cobro_simple,
                              cs.id_proceso_wf,
                              cs.id_estado_wf,
                              cs.id_depto_conta
                          INTO
                          v_registros
                          	
                          from cbr.tcobro_simple cs
                          where cs.id_cobro_simple = v_id_cobro_simple::INTEGER  ;        
                         
                          
                          
                          
                                SELECT 
                                 *
                              into
                                va_id_tipo_estado,
                                va_codigo_estado,
                                va_disparador,
                                va_regla,
                                va_prioridad
                            
                            FROM wf.f_obtener_estado_wf(v_registros.id_proceso_wf, v_registros.id_estado_wf,NULL,'siguiente');
                            
                            
                            --raise exception '--  % ,  % ,% ',v_id_proceso_wf,v_id_estado_wf,va_codigo_estado;
                            
                            
                            IF va_codigo_estado[2] is not null THEN
                            
                             raise exception 'El proceso de WF esta mal parametrizado,  solo admite un estado siguiente para el estado: %', v_registros.estado;
                            
                            END IF;
                            
                             IF va_codigo_estado[1] is  null THEN
                            
                             raise exception 'El proceso de WF esta mal parametrizado, no se encuentra el estado siguiente,  para el estado: %', v_registros.estado;           
                            END IF;
                            
                            
                          p_id_usuario=1;
                          p_id_usuario_ai = 1;
                          p_usuario_ai = null;
                            
                            -- estado siguiente
                         v_id_estado_actual =  wf.f_registra_estado_wf(va_id_tipo_estado[1], 
                                                                           NULL, 
                                                                           v_registros.id_estado_wf, 
                                                                           v_registros.id_proceso_wf,
                                                                           p_id_usuario,
                                                                           p_id_usuario_ai, -- id_usuario_ai
                                                                           p_usuario_ai, -- usuario_ai
                                                                           v_registros.id_depto_conta,
                                                                           'Cobro Automatico');
                            
                            
                            
                           
                        
                        
                            -- actualiza estado en la solicitud
                          
                            
                             update cbr.tcobro_simple pp  set 
                                         id_estado_wf =  v_id_estado_actual,
                                         estado = va_codigo_estado[1],
                                         id_usuario_mod=p_id_usuario,
                                         fecha_mod=now(),
                                         id_usuario_ai = p_id_usuario_ai,
                                         usuario_ai = p_usuario_ai
                                       where id_cobro_simple  = v_registros.id_cobro_simple; 
                  
                          
                          
                          
                          
                          
                        else 
                           raise NOTICE 'ya existe un cobro con esta factura %',v_id_doc_compra_venta;
                        
                        end if;
			else 
             raise NOTICE 'El saldo de %  no es 0',item.id_doc_compra_venta;
                        
            end if;
          
           
          
    	end loop;	
   

	RETURN   v_resp;

EXCEPTION
					
	WHEN OTHERS THEN
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION cbr.f_inserta_cobro_multa (p_administrador integer, p_id_usuario integer, p_tabla varchar)
  OWNER TO postgres;