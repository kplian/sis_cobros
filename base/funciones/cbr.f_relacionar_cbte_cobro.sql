--------------- SQL ---------------
CREATE OR REPLACE FUNCTION cbr.f_relacionar_cbte_cobro (
)
RETURNS boolean AS
$body$
/**************************************************************************
 SISTEMA:       Sistema de Cuenta Documentada
 FUNCION:       cbr.f_relacionar_cbte_cobro
                
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
   v_id_tipo_relacion_comprobante    integer;
   v_registros   record;
   va_id_int_comprobante_fks        integer[]; 
BEGIN

    -- recuperar tipo  relacion de cbte cobro del devengado
    
    select 
       trc.id_tipo_relacion_comprobante
     into
       v_id_tipo_relacion_comprobante 
    from conta.ttipo_relacion_comprobante trc
    where trc.codigo = 'INGDEV';
       
    --   listas  facturas relacionadas a cobro , recupera comrobante de cobro
    FOR v_registros in (
                       select 
                         csd.id_doc_compra_venta,
                         dcv.id_int_comprobante as id_cbte_dev,
                         cs.id_int_comprobante as id_cbte_pago
                      from cbr.tcobro_simple_det csd
                      inner join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                      inner join conta.tdoc_compra_venta dcv on dcv.id_doc_compra_venta = csd.id_doc_compra_venta
                      where dcv.estado_reg = 'activo' and  cs.id_int_comprobante is not null
       )LOOP
    
              raise notice '--> %,%',v_registros.id_cbte_pago ,v_registros.id_cbte_dev; 
    
               va_id_int_comprobante_fks = NULL;
               select 
                  cbt.id_int_comprobante_fks
                  into
                  va_id_int_comprobante_fks
               from conta.tint_comprobante cbt
               where cbt.id_int_comprobante = v_registros.id_cbte_pago;
               
               
              IF   v_registros.id_cbte_dev = ANY(va_id_int_comprobante_fks) THEN
              
                    raise notice 'no relaciona % ', v_registros.id_cbte_pago;
                    
              else
                    update  conta.tint_comprobante set
                    id_int_comprobante_fks = array_append(va_id_int_comprobante_fks, v_registros.id_cbte_dev),
                    id_tipo_relacion_comprobante = v_id_tipo_relacion_comprobante
                  where id_int_comprobante = v_registros.id_cbte_pago;
                
              END IF;
              
               
    
    
    END LOOP;   
  

        --  recupera cbte de devengado
     
            --modifica cbte de pago,  relaciona con cbte padre   
    
    
  return true;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
