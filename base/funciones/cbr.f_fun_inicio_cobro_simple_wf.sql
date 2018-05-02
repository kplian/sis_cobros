--------------- SQL ---------------

CREATE OR REPLACE FUNCTION cbr.f_fun_inicio_cobro_simple_wf (
  p_id_usuario integer,
  p_id_usuario_ai integer,
  p_usuario_ai varchar,
  p_id_estado_wf integer,
  p_id_proceso_wf integer,
  p_codigo_estado varchar,
  p_id_depto_lb integer = NULL::integer,
  p_id_cuenta_bancaria integer = NULL::integer,
  p_estado_anterior varchar = 'no'::character varying
)
RETURNS boolean AS
$body$
/**************************************************************************
 SISTEMA:       Sistema de Cuenta Documentada
 FUNCION:       cbr.f_fun_inicio_cobro_simple_wf
                
 DESCRIPCION:   Actualiza los estados despues del registro de estado en pagos simples
 AUTOR:         RCM
 FECHA:         05/01/2018
 COMENTARIOS: 

 ***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:   
 AUTOR:         
 FECHA:         
***************************************************************************/
DECLARE

    v_nombre_funcion                text;
    v_resp                          varchar;     
    v_mensaje                       varchar;    
    v_registros                     record;
    v_rec                           record;
    v_monto_ejecutar_mo             numeric;
    v_id_uo                         integer;
    v_id_usuario_excepcion          integer;
    v_resp_doc                      boolean;
    v_sincronizar                   varchar;
    v_nombre_conexion               varchar;
    v_id_int_comprobante            integer;
    v_total_det_mb					numeric;
BEGIN
    
    --Identificación del nombre de la función
    v_nombre_funcion = 'cbr.f_fun_inicio_cobro_simple_wf';

    ----------------------------------------------
    --Obtención de datos de la cuenta documentada
    ----------------------------------------------
    select 
        c.id_cobro_simple,
        c.estado,
        c.id_estado_wf,
        c.id_funcionario,
        tc.codigo as codigo_tipo_pago_simple,
        tc.plantilla_cbte,
        tc.plantilla_cbte_1,
        c.importe_mb
        into v_rec
    from cbr.tcobro_simple c
    inner join cbr.ttipo_cobro_simple tc
    on tc.id_tipo_cobro_simple = c.id_tipo_cobro_simple
    where c.id_proceso_wf = p_id_proceso_wf;
    
    
      
    --Actualización del estado de la solicitud
    update cbr.tcobro_simple set 
    id_estado_wf    = p_id_estado_wf,
    estado          = p_codigo_estado,
    id_usuario_mod  = p_id_usuario,
    id_usuario_ai   = p_id_usuario_ai,
    usuario_ai      = p_usuario_ai,
    fecha_mod       = now()                     
    where id_proceso_wf = p_id_proceso_wf;

    ---------------------------------------------------
    -- ACTUALIZACION DE LIBO DE BANCOS YCUENTA BANCARIA
    ---------------------------------------------------
    if p_estado_anterior = 'vbtesoreria' then
          
        update cbr.tcobro_simple set 
        id_depto_lb             = p_id_depto_lb,
        id_cuenta_bancaria      = p_id_cuenta_bancaria
        where id_proceso_wf = p_id_proceso_wf;

   
    end if;
    
    -- la suam de prorrateo debe cuadrar con el m onto a apgar
    select   sum(csd.importe_mb) into v_total_det_mb
    from cbr.tcobro_simple_det csd where csd.id_cobro_simple =  v_rec.id_cobro_simple  ;
     
    if  COALESCE(v_total_det_mb,0) != v_rec.importe_mb   then
         raise exception 'El total prorrateado de facturas no cuadra con el total de cobro ' ;
    end if;

    ------------------------------------
    -- Generación de comprobante diario
    ------------------------------------
    
    --Si el estado es pendiente genera el comprobante
    if p_codigo_estado = 'pendiente' then
    
            IF v_rec.plantilla_cbte is null THEN
              raise exception 'revise la configuración para el tipo de cobro %, no se encontro plantilla de cbte',  v_rec.codigo_tipo_pago_simple; 
            END IF;
     
            --Generación del comprobante
            v_id_int_comprobante = conta.f_gen_comprobante(v_rec.id_cobro_simple, 
                                                            v_rec.plantilla_cbte,
                                                            p_id_estado_wf,                                                     
                                                            p_id_usuario,
                                                            p_id_usuario_ai, 
                                                            p_usuario_ai, 
                                                            v_nombre_conexion);

            --Actualización del Id del comprobante en la cuenta documentada
            update cbr.tcobro_simple set 
              id_int_comprobante = v_id_int_comprobante          
            where id_proceso_wf = p_id_proceso_wf;
            
            
    end if;
 
    --Respuesta
    return true;

EXCEPTION
                    
    WHEN OTHERS THEN

        v_resp = '';
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