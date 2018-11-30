--------------- SQL ---------------
CREATE OR REPLACE FUNCTION cbr.f_procesar_cobros (
)
RETURNS boolean AS
$body$
DECLARE
   v_registros record;
   v_registros_trans record;
   va_id_int_comprobante_matriz   INTEGER[];
   
   
BEGIN
 
   FOR v_registros in (
                     select cs.id_int_comprobante from cbr.tcobro_simple cs
                     where cs.estado = 'finalizado'
                           and  cs.id_int_comprobante  is not NULL
    
          )LOOP
          
          
          --recuperar la matrix de  comprobantes a revisar    
           with mytable as
              (
              select 
                  ids 
                  from conta.vcbte_raiz cr1 
                  where cr1.id_int_comprobante in(  
                  select 
                    c.id_int_comprobante
                  from conta.vcbte_raiz c
                  where v_registros.id_int_comprobante = ANY(c.ids))
              )

              select
                array_agg(elements order by elements) into va_id_int_comprobante_matriz
              from mytable, unnest(ids) as elements;
              
              --listar todas las trasaccion dentro de la matriz que esten cerradas
              FOR v_registros_trans in (
                                           with presuma as (
                                          select
                                                   t.id_cuenta,
                                                   t.id_auxiliar,
                                                   pxp.aggarray(t.id_int_transaccion ) as ids_transaccion,
                                                   sum(t.importe_debe_mb) as debe,
                                                   sum(t.importe_haber_mb) as haber
                                                 from conta.tint_transaccion t
                                                 inner join conta.tint_comprobante c on c.id_int_comprobante = t.id_int_comprobante 
                                                 where  t.estado_reg = 'activo' and
                                                        c.id_int_comprobante =ANY(va_id_int_comprobante_matriz)
                                                 group by 
                                                      t.id_cuenta,
                                                      t.id_auxiliar 
                                          )
                                            select
                                              id_cuenta,
                                              id_auxiliar,
                                              ids_transaccion,
                                              debe - haber as saldo
                                            from presuma 
                                            where (debe - haber) = 0 
                                          
                                          )LOOP
                                          
                                          
                                     update  conta.tint_transaccion tr set
                                          cerrado = 'si',
                                          fecha_cerrado =  now()
                                      where tr.id_int_transaccion =ANY(v_registros_trans.ids_transaccion);            
                           
              
              
              END LOOP;
              
              
              
              
              
          
          
   
   END LOOP;

    
   return true;
            
           
            


END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
