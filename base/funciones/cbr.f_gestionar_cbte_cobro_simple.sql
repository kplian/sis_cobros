--------------- SQL ---------------

CREATE OR REPLACE FUNCTION cbr.f_gestionar_cbte_cobro_simple (
  p_id_usuario integer,
  p_id_usuario_ai integer,
  p_usuario_ai varchar,
  p_id_int_comprobante integer,
  p_conexion varchar = NULL::character varying
)
RETURNS boolean AS
$body$
/*
Autor: RCM
Fecha:   06/01/2018
Descripcion  Esta funcion gestiona los cbtes de pagosimple cuando son validados          
*/


DECLARE

     v_nombre_funcion               text;
     v_resp                         varchar;
     v_registros                    record;
     v_registros_tmp                record;
     v_id_estado_actual             integer;
     va_id_tipo_estado              integer[];
     va_codigo_estado               varchar[];
     va_disparador                  varchar[];
     va_regla                       varchar[]; 
     va_prioridad                   integer[];    
     v_tipo_sol                     varchar;    
     v_nro_cuota                    numeric;    
     v_id_proceso_wf                integer;
     v_id_estado_wf                 integer;
     v_codigo_estado                varchar;
     v_id_plan_pago                 integer;
     v_verficacion                  boolean;
     v_verficacion2                 varchar[];     
     v_id_tipo_estado               integer;
     v_codigo_proceso_llave_wf      varchar;
     --gonzalo
     v_id_finalidad                 integer;
     v_respuesta_libro_bancos       varchar;
     v_registros_tpc                record;
     v_codigo_tpc                   varchar;
     v_id_proceso_caja              integer;
     v_id_int_comprobante           integer;
     v_sw_disparo                   boolean;
     v_hstore_registros             hstore;
     v_tmp_tipo                     varchar;
     v_id_solicitud_efectivo        integer;
     v_saldo_caja                   numeric;
     v_monto                        numeric;
     v_registros_cv                 record;
     v_total_rendido                numeric;
     v_importe_solicitado           numeric;
     v_rec_saldo                    record;
     v_num_estados                  integer;
     v_num_funcionarios             integer;
     v_id_funcionario_estado_sig    integer;
     v_reg_cbte                     record;
     
     v_id_tipo_relacion_comprobante    integer;
     v_registros_relacion   record;
     va_id_int_comprobante_fks        integer[]; 
    
BEGIN

    v_nombre_funcion = 'cbr.f_gestionar_cbte_cobro_simple';
 
    -- 1) con el id_comprobante identificar el plan de pago
    select 
    cc.id_cobro_simple,
    cc.id_estado_wf,
    cc.id_proceso_wf,
    cc.estado,
    cc.id_int_comprobante
    into
    v_registros
    from cbr.tcobro_simple cc
    where cc.id_int_comprobante = p_id_int_comprobante; 
      
    
    
    --2) Validar que tenga una cuenta documentada
    if v_registros.id_cobro_simple is null then

        select 
        cc.id_cobro_simple,
        cc.id_estado_wf,
        cc.id_proceso_wf,
        cc.estado,
        cc.id_int_comprobante_pago as id_int_comprobante
        into
        v_registros
        from cbr.tcobro_simple cc
        where cc.id_int_comprobante_pago = p_id_int_comprobante; 

        if  v_registros.id_cobro_simple is null then
            raise exception 'El comprobante no esta relacionado a ningun pago';
        end if;

    end if;

    --Obtiene los datos del pago
    select 
    pc.id_cobro_simple,
    pc.id_estado_wf,
    pc.id_proceso_wf,
    pc.estado,
    pc.id_depto_conta,
    pc.nro_tramite,
    ew.id_funcionario,
    ew.id_depto,
    pc.fecha,
    pc.tipo_cambio,
    pc.tipo_cambio_ma,
    pc.tipo_cambio_mt
    into
    v_registros_cv
    from cbr.tcobro_simple pc
    inner join wf.testado_wf ew on ew.id_estado_wf = pc.id_estado_wf
    where pc.id_cobro_simple = v_registros.id_cobro_simple;
    
    
    ------------------------------------------------------
    --revisa que los tipos de cambio no esten modificados
    -- con respecto al cobro registrado
    --------------------------------------------------------
    
    select 
      c.tipo_cambio,
      c.tipo_cambio_2,
      c.tipo_cambio_3
     into
      v_reg_cbte
    from conta.tint_comprobante c
    where c.id_int_comprobante = p_id_int_comprobante;
    
    
    IF v_reg_cbte .tipo_cambio !=  v_registros_cv.tipo_cambio  or 
       v_reg_cbte.tipo_cambio_2  != v_registros_cv.tipo_cambio_mt or 
       v_reg_cbte.tipo_cambio_3 != v_registros_cv.tipo_cambio_ma  THEN
    
       raise exception 'Los tipo de cambios debe ser iguales que lo registrados para el cobro (No es permitido editar el tipo de cambio en el cbte)';
    
    END IF;
    
    
    
    
    
     

    --------------------------------------------------------
    ---  cambiar el estado de la cuenta dicumentada    -----
    --------------------------------------------------------
        
        
    -- obtiene el siguiente estado del flujo 
    SELECT 
    *
    into
    va_id_tipo_estado,
    va_codigo_estado,
    va_disparador,
    va_regla,
    va_prioridad
    FROM wf.f_obtener_estado_wf(v_registros_cv.id_proceso_wf, v_registros_cv.id_estado_wf,NULL,'siguiente');

    IF va_codigo_estado[2] is not null THEN              
        raise exception 'El proceso de WF esta mal parametrizado,  solo admite un estado siguiente para el estado: %', v_registros.estado;
    END IF;

    IF va_codigo_estado[1] is  null THEN
        raise exception 'El proceso de WF esta mal parametrizado, no se encuentra el estado siguiente,  para el estado: %', v_registros.estado;           
    END IF;
    
    
    v_num_estados= array_length(va_id_tipo_estado, 1);
        
        
                       
        IF v_num_estados = 1 then
                  -- si solo hay un estado,  verificamos si tiene mas de un funcionario por este estado
                 SELECT 
                 *
                  into
                  v_num_funcionarios
                  
                 FROM wf.f_funcionario_wf_sel(
                     p_id_usuario, 
                     va_id_tipo_estado[1], 
                     v_registros_cv.fecha,
                     v_registros_cv.id_estado_wf,
                     TRUE) AS (total bigint);
                     
                     
             
                                   
                      IF v_num_funcionarios = 1 THEN
                      -- si solo es un funcionario, recuperamos el funcionario correspondiente
                           SELECT 
                               id_funcionario
                                 into
                               v_id_funcionario_estado_sig
                           FROM wf.f_funcionario_wf_sel(
                               p_id_usuario, 
                               va_id_tipo_estado[1], 
                               v_registros_cv.fecha,
                               v_registros_cv.id_estado_wf,
                               FALSE) 
                               AS (id_funcionario integer,
                                 desc_funcionario text,
                                 desc_funcionario_cargo text,
                                 prioridad integer);
                      END IF;  
        
        ELSE
        
            raise exception 'El flujo se encuentra mal parametrizados, mas de un estado destino';
        
        END IF;
    
    

    -- estado siguiente
    v_id_estado_actual =  wf.f_registra_estado_wf(va_id_tipo_estado[1], 
                             v_id_funcionario_estado_sig,     --obtiene funcionario segun configuracion del estado, 
                             v_registros_cv.id_estado_wf, 
                             v_registros_cv.id_proceso_wf,
                             p_id_usuario,
                             p_id_usuario_ai, -- id_usuario_ai
                             p_usuario_ai, -- usuario_ai
                             v_registros_cv.id_depto_conta,
                             'Comprobante validado');
    --Actualiza estado del proceso
    update cbr.tcobro_simple pc  set 
    id_estado_wf    =  v_id_estado_actual,
    estado          = va_codigo_estado[1],
    id_usuario_mod  = p_id_usuario,
    fecha_mod       = now(),
    id_usuario_ai   = p_id_usuario_ai,
    usuario_ai      = p_usuario_ai
    where pc.id_cobro_simple  = v_registros_cv.id_cobro_simple; 
      
    --relacionar el comprobante de pago con los comprbantes de devengado
    
    select 
       trc.id_tipo_relacion_comprobante
     into
       v_id_tipo_relacion_comprobante 
    from conta.ttipo_relacion_comprobante trc
    where trc.codigo = 'INGDEV';
       
    --   listas  facturas relacionadas a cobro , recupera comrobante de cobro
    FOR v_registros_relacion in (
                       select 
                         csd.id_doc_compra_venta,
                         dcv.id_int_comprobante as id_cbte_dev,
                         cs.id_int_comprobante as id_cbte_pago
                      from cbr.tcobro_simple_det csd
                      inner join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                      inner join conta.tdoc_compra_venta dcv on dcv.id_doc_compra_venta = csd.id_doc_compra_venta
                      where      dcv.estado_reg = 'activo' and  cs.id_int_comprobante is not null
                            and   cs.id_cobro_simple  = v_registros_cv.id_cobro_simple
       )LOOP
    
            
    
               va_id_int_comprobante_fks = NULL;
               select 
                  cbt.id_int_comprobante_fks
                  into
                  va_id_int_comprobante_fks
               from conta.tint_comprobante cbt
               where cbt.id_int_comprobante = v_registros_relacion.id_cbte_pago;
               
               
              IF   v_registros_relacion.id_cbte_dev = ANY(va_id_int_comprobante_fks) THEN
              
                    raise notice 'no relaciona % ', v_registros_relacion.id_cbte_pago;
                    
              else
                    update  conta.tint_comprobante set
                    id_int_comprobante_fks = array_append(va_id_int_comprobante_fks, v_registros_relacion.id_cbte_dev),
                    id_tipo_relacion_comprobante = v_id_tipo_relacion_comprobante
                    where id_int_comprobante = v_registros_relacion.id_cbte_pago;
                
              END IF;
              
               
    END LOOP;         
            
   RETURN  TRUE;

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
