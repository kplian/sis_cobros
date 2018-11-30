--------------- SQL ---------------

CREATE OR REPLACE FUNCTION cbr.ft_cobro_simple_det_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Cuenta Documenta
 FUNCION: 		cbr.ft_cobro_simple_det_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'cbr.tcobro_simple_det'
 AUTOR: 		 (admin)
 FECHA:	        01-01-2018 06:21:25
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				01-01-2018 06:21:25								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'cbr.tcobro_simple_det'	
 #1             20/08/2018              EGS                  en la trnassaccion CBR_RELFAC_IME se aumento la logica para el cobro de retencion de garantias y regularizados
 #2				10/09/2018				EGS					se aumento las validaciones y variables para cobro de anticipos

 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_registros 			record;
	v_id_cobro_simple_det 	integer;
    v_total 				numeric;
    v_registros_cbr			record;
    v_id_moneda_base		integer;
    
    v_id_moneda_tri		    integer;
    v_reg_dcv		        record;
    v_total_importe_cobro_mb			numeric;
    v_total_importe_cobro_mt			numeric;
    v_monto_prorrateo_ma			    numeric;
    v_monto_prorrateo_mt			    numeric;
    v_monto_prorrateo_mb		        numeric;
    v_total_regitro_previos_facturas_mb	    numeric;
    va_montos		                        numeric[];
    v_id_moneda_act	                        integer;
    
    v_bandera						varchar;
    v_bandera_rg 					varchar;
    v_bandera_ant					VARCHAR;  ----- #2  EGS 10/09/2018
    
    
    v_bandera_regularizacion		varchar;
    v_bandera_regularizacion_comun		varchar;
    v_bandera_regularizacion_retgar		varchar;
    v_bandera_regularizacion_ant		varchar;    --#2  EGS 10/09/2018
    
    v_bandera_consulta						varchar;
              
			    
BEGIN

    v_nombre_funcion = 'cbr.ft_cobro_simple_det_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	
	/*********************************    
 	#TRANSACCION:  'CBR_PASIDE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		01-01-2018 06:21:25
	***********************************/

	if(p_transaccion='CBR_PASIDE_ELI')then

		begin

			--Obtiene datos del pago simple
			select
              ps.id_cobro_simple,
              ps.id_tipo_cobro_simple,
              ps.estado,
              tps.codigo as codigo_tipo_cobro_simple,
              ps.estado
         
			into
					v_registros
			from cbr.tcobro_simple_det psd
			inner join cbr.tcobro_simple ps
			on ps.id_cobro_simple = psd.id_cobro_simple
			inner join cbr.ttipo_cobro_simple tps
			on tps.id_tipo_cobro_simple= ps.id_tipo_cobro_simple
			where psd.id_cobro_simple_det = v_parametros.id_cobro_simple_det;

			--Elimina el documento solo si el pago esta en estado borrador
			if  v_registros.estado not  in ('borrador') then				
                raise exception 'No puede quitarse el documento porque el Pago no esta en Borrador';
			end if;

			--Sentencia de la eliminacion
			delete from cbr.tcobro_simple_det
            where id_cobro_simple_det=v_parametros.id_cobro_simple_det;

			
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Facturas/Recibos retirados del cobro(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_cobro_simple_det',v_parametros.id_cobro_simple_det::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
        
      /*********************************    
      #TRANSACCION:  'CBR_RELFAC_IME'
      #DESCRIPCION:	Relaciona facturas al cobro
      #AUTOR:		    Rensi
      #FECHA:		    04-04-2018 06:21:25
      ***********************************/
	 elsif(p_transaccion='CBR_RELFAC_IME')then

		begin
             
           --recueprar el tipo de cobro y su estado
             select 
               cb.id_cobro_simple,
               cb.estado,
               tcs.codigo as  codigo_tipo,
               cb.id_moneda,
               cb.importe,
               cb.importe_mb,
               cb.importe_mt,
               cb.fecha,
               cb.id_int_comprobante,
               cb.nro_tramite,
               cb.tipo_cambio,
               cb.tipo_cambio_mt,
               cb.tipo_cambio_ma,
               cb.id_config_cambiaria,
               tcs.id_tipo_cobro_simple,
               tcs.nombre
              into
               v_registros_cbr
             from cbr.tcobro_simple cb
             inner join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cb.id_tipo_cobro_simple
             where cb.id_cobro_simple = v_parametros.id_cobro_simple;
             
             --recupera datos de la factura
             select 
                dcv.id_doc_compra_venta,
                dcv.id_moneda,
                dcv.importe_doc,
                dcv.fecha,
                dcv.importe_pago_liquido,
                dcv.importe_pendiente,
                dcv.codigo_aplicacion,
                dcv.importe_retgar,
                dcv.importe_anticipo
             into
                 v_reg_dcv
             from conta.tdoc_compra_venta dcv
             where dcv.id_doc_compra_venta = v_parametros.id_doc_compra_venta; 
             
             --raise exception 'id %',v_reg_dcv.id_doc_compra_venta;
             
             IF v_reg_dcv.codigo_aplicacion is null or v_reg_dcv.codigo_aplicacion = '' THEN
               raise exception 'La factura no tiene una aplicación definida.';
             END IF;
             
             
             IF   v_reg_dcv is null THEN
                 raise exception 'No se encontro la factura ID %', v_parametros.id_doc_compra_venta;
             END IF;
            
        
             IF v_registros_cbr is null THEN
                 raise exception 'No se encontro el id de cobro %',v_parametros.id_cobro_simple;
             END IF;
             
           
             
             
             -- validar el estado del cobro (Segun el tipo de cobro) --> CBRREG cobr de regularizacion , no tiene comprobante
             IF v_registros_cbr.estado != 'borrador' and v_registros_cbr.codigo_tipo  in ('CBRANT')   THEN
                raise exception 'solo puede relacionar factura en cobro en estado borrador,  y en cobros que no sean anticipos)' ;
             END IF;
             
        
             -- revisar que la factura no este relaciona dos veces al mismo cobro
             
             IF exists(select 1 from cbr.tcobro_simple_det csd 
                       where      csd.id_doc_compra_venta = v_parametros.id_doc_compra_venta 
                             and  csd.id_cobro_simple = v_parametros.id_cobro_simple ) THEN
                raise exception 'esta factura ya se encuentra relacionada a este cobro';
             END IF;
             
             ----------------------------------------
             --  sumar todos los cobros de la factura             
             --  considerar anticipos
             --  devoluciones de garantia
             --  pagos comunes
             --  Convetir los  cobros a la moneda de la factura al tipo de cambio que fueron registrados
             --------------------------------------------
             
             --recupera moneda base 
             
             v_id_moneda_base = param.f_get_moneda_base();
             v_id_moneda_tri = param.f_get_moneda_triangulacion();
             

             v_bandera = split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 1);
             v_bandera_rg = split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 1);
             v_bandera_ant = split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 1);
             
             
             v_bandera_regularizacion_comun = split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 2);
             v_bandera_regularizacion_retgar= split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 2);
             v_bandera_regularizacion_ant= split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 2);
           -- raise exception 'bandera% %',v_bandera,v_registros_cbr.codigo_tipo;
            --raise exception 'bandera%',v_registros_cbr.codigo_tipo;
             
           
              
             if  v_bandera = v_registros_cbr.codigo_tipo or v_bandera_regularizacion_comun = v_registros_cbr.codigo_tipo then
             	
             v_bandera_consulta	= split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 1);
             v_bandera_regularizacion = split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 2);
             
               --raise exception 'bandera %',  v_bandera_consulta;
              -- raise exception 'bandera 2 %', v_bandera_regularizacion;
             elseif v_bandera_rg = v_registros_cbr.codigo_tipo or v_bandera_regularizacion_retgar = v_registros_cbr.codigo_tipo then
             	v_bandera_consulta	=  split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 1);
             	v_bandera_regularizacion = split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 2);
             elseif v_bandera_ant = v_registros_cbr.codigo_tipo or v_bandera_regularizacion_ant = v_registros_cbr.codigo_tipo then
             	v_bandera_consulta	=  split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 1);
             	v_bandera_regularizacion = split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 2);
              	 
             end if;
             --v_bandera_consulta	= 'CBRCMN';
            -- v_bandera_regularizacion 	= 'CBRCMNRE' ; 
             --raise exception 'bandera consulta % ',v_bandera_consulta;
             --raise exception 'bandera regularizacion% ', v_bandera_regularizacion;
             --raise exception 'registro% ',''''|| v_registros_cbr.codigo_tipo||'''';
             
                         
      SELECT( (select COALESCE(sum(csd.importe_mb),0)
                from cbr.tcobro_simple_det csd
                left join cbr.tcobro_simple cbs on cbs.id_cobro_simple = csd.id_cobro_simple
                left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cbs.id_tipo_cobro_simple 
                 where csd.id_doc_compra_venta = v_parametros.id_doc_compra_venta and tcs.codigo = v_bandera_consulta )
         			  +
                  (select COALESCE(sum(csd.importe_mb),0)
                from cbr.tcobro_simple_det csd
                left join cbr.tcobro_simple cbs on cbs.id_cobro_simple = csd.id_cobro_simple
                left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cbs.id_tipo_cobro_simple 
                 where csd.id_doc_compra_venta = v_parametros.id_doc_compra_venta and tcs.codigo = v_bandera_regularizacion    
                ) )as totalmb,
                
                ( select COALESCE(sum(csd.importe_mt),0)
                from cbr.tcobro_simple_det csd
                left join cbr.tcobro_simple cbs on cbs.id_cobro_simple = csd.id_cobro_simple
                left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cbs.id_tipo_cobro_simple 
                 where csd.id_doc_compra_venta = v_parametros.id_doc_compra_venta and tcs.codigo in (v_bandera_consulta,v_bandera_regularizacion))as totalmt
                 into 
               v_total_importe_cobro_mb,
               v_total_importe_cobro_mt;
             --raise exception 'total%', execute(v_total_importe_cobro_mb);
            --raise exception 'total %', v_total_importe_cobro_mt;
             /*
             select 
               sum(csd.importe_mb),
               sum(csd.importe_mt) 
             into 
               v_total_importe_cobro_mb,
               v_total_importe_cobro_mt
                   
             from cbr.tcobro_simple_det csd
              left join cbr.tcobro_simple cbs on cbs.id_cobro_simple = csd.id_cobro_simple
              left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cbs.id_tipo_cobro_simple 
              where   csd.id_doc_compra_venta = v_parametros.id_doc_compra_venta
                   and csd.estado_reg = 'activo'and tcs.codigo =  v_registros_cbr.codigo_tipo ;		*/	
            
           
            v_total_importe_cobro_mb = COALESCE(v_total_importe_cobro_mb,0);
            v_total_importe_cobro_mt = COALESCE(v_total_importe_cobro_mt,0);
             
             
             
             
             
             IF  v_reg_dcv.id_moneda not in (v_id_moneda_base, v_id_moneda_tri)   THEN             
                raise exception 'moneda no soportada para docuementos de venta';             
             END IF;
             
             --------------------------------------------------------------------------
            --calcula los valor  en moneda base y triangulacion segun configuracion
            ------------------------------------------------------------------------
            v_id_moneda_base = param.f_get_moneda_base();
            v_id_moneda_tri = param.f_get_moneda_triangulacion();
            v_id_moneda_act = param.f_get_moneda_intercambio();
            
            
            
            IF v_parametros.monto_prorrateo  <=  0 THEN
               raise exception 'el monto asignado a la factura debe ser mayor a cero';
            END IF;
            
            va_montos  = conta.f_calcular_monedas_segun_config(v_registros_cbr.id_moneda, v_id_moneda_base, v_id_moneda_tri, v_id_moneda_act, v_registros_cbr.tipo_cambio, v_registros_cbr.tipo_cambio_mt,v_registros_cbr.tipo_cambio_ma, v_parametros.monto_prorrateo, v_registros_cbr.id_config_cambiaria, v_registros_cbr.fecha);
            v_monto_prorrateo_mb = va_montos[1];
            v_monto_prorrateo_mt = va_montos[2];
            v_monto_prorrateo_ma = va_montos[3];
              
            
  
             
             
          -- raise exception 'entraaaaa%',v_monto_prorrateo_mb; 
           
 -------------------------------------------------------------------------
  --Cobro Comun codigo = CBRCMN usado en el registro de tipo de cobro
  --en regularizado codigo= CBRCMNRE
 --------------------------------------------------------------------------
          
           IF  v_bandera = v_registros_cbr.codigo_tipo or v_bandera_regularizacion_comun=v_registros_cbr.codigo_tipo then
                   
                  
                   -------------------------------------------------------------------------
                   --el monto asignado  al cobro no puede sobrepasar el saldo de la factura
                   --------------------------------------------------------------------------
                   
                   IF v_id_moneda_base = v_reg_dcv.id_moneda  THEN
                     --raise exception 'entra..  1)  %, 2) %, 3) %', v_reg_dcv.importe_doc , v_total_importe_cobro_mb ,  v_monto_prorrateo_mb;
               		--raise exception 'monto pendiente %',v_reg_dcv.importe_pendiente;
                        IF v_reg_dcv.importe_pendiente < v_monto_prorrateo_mb THEN
                            raise exception 'el monto a cobrar no puede ser mayor que al pendiente por cobrar de la factura';
                         END IF;
                           
                         --Todas las moendas estan en bolivianos             
                         IF    v_reg_dcv.importe_pendiente <  (v_total_importe_cobro_mb +  v_monto_prorrateo_mb) THEN  
                           raise exception 'BOB , el monto a pagar prorrateado (%) mas los pagos previos (%) sobrepasa el monto del documento (%)', v_monto_prorrateo_mb , v_total_importe_cobro_mb, v_reg_dcv.importe_pendiente; 
                         END IF;
                     
                   ELSEIF v_id_moneda_tri = v_reg_dcv.id_moneda  THEN
                   
                          IF v_reg_dcv.importe_pendiente < v_monto_prorrateo_mt THEN
                            raise exception 'el monto a cobrar no pueder  ser mayor que el total de la factura';
                          END IF;
                         
                           -- Todas las moendas estan en dolares
                            IF  v_reg_dcv.importe_pendiente <=  (v_total_importe_cobro_mt +  v_monto_prorrateo_mt) THEN  
                              raise exception 'USD , el monto a pagar prorrateado (%) mas los pagos previos (%) sobrepasa el monto del documento (%)', v_monto_prorrateo_mt , v_total_importe_cobro_mt, v_reg_dcv.importe_pendiente; 
                            END IF;
                   ELSE
                     raise exception 'moenda no soportada';
                   END IF;
  -------------------------------------------------------------------------
  --Cobro Retencion con garantias codigo = CBRCMNRG usado en el registro de tipo de cobro
  --en regularizado codigo= CBRCMNRGRE
 --------------------------------------------------------------------------
             
           ELSIF    v_bandera_rg = v_registros_cbr.codigo_tipo or v_bandera_regularizacion_retgar = v_registros_cbr.codigo_tipo THEN
           
                IF v_id_moneda_base = v_reg_dcv.id_moneda  THEN
                         --raise exception 'entra..  1)  %, 2) %, 3) %', v_reg_dcv.importe_doc , v_total_importe_cobro_mb ,  v_monto_prorrateo_mb;
                   				
                         --raise EXCEPTION 'total cobrado %',(v_total_importe_cobro_mb +  v_monto_prorrateo_mb);
                             IF v_reg_dcv.importe_retgar < v_monto_prorrateo_mb THEN
                                raise exception 'el monto a cobrar no puede ser mayor que el total por cobrar de la retencion de garantias de la factura';
                             END IF;
                             
                             --Todas las moendas estan en bolivianos             
                             IF    v_reg_dcv.importe_retgar <  (v_total_importe_cobro_mb +  v_monto_prorrateo_mb) THEN  
                               raise exception 'BOB , el monto a pagar prorrateado (%) mas los pagos previos (%) sobrepasa el monto de la retencion de garantias del documento(%)', v_monto_prorrateo_mb , v_total_importe_cobro_mb, v_reg_dcv.importe_retgar; 
                             END IF;
                         
                 ELSEIF v_id_moneda_tri = v_reg_dcv.id_moneda  THEN
                       
                                  IF v_reg_dcv.importe_retgar < v_monto_prorrateo_mt THEN
                                    raise exception 'el monto a cobrar no pueder  ser mayor que el total de retencion de garantias de la factura ';
                                  END IF;
                         
                                 -- Todas las moendas estan en dolares
                                  IF  v_reg_dcv.importe_retgar <  (v_total_importe_cobro_mt +  v_monto_prorrateo_mt) THEN  
                                    raise exception 'USD , el monto a pagar prorrateado (%) mas los pagos previos (%)sobrepasa el monto de la retencion de garantias del documento(%)', v_monto_prorrateo_mt , v_total_importe_cobro_mt, v_reg_dcv.importe_retgar; 
                                  END IF;
                   ELSE
                          raise exception 'moneda no soportada';
                   END IF;
          
           
           
           
                -------------------------------#2  I-EGS 10/09/2018------------------------------------------
                  --Cobro Retencion con garantias codigo = CBRCMNAT usado en el registro de tipo de cobro
                  --en regularizado codigo= CBRCMNATRE
                --------------------------------------------------------------------------
             
           ELSIF   v_bandera_ant = v_registros_cbr.codigo_tipo or v_bandera_regularizacion_ant = v_registros_cbr.codigo_tipo THEN
           
                IF v_id_moneda_base = v_reg_dcv.id_moneda  THEN
                         --raise exception 'entra..  1)  %, 2) %, 3) %', v_reg_dcv.importe_doc , v_total_importe_cobro_mb ,  v_monto_prorrateo_mb;
                   				
                         --raise EXCEPTION 'total cobrado %',(v_total_importe_cobro_mb +  v_monto_prorrateo_mb);
                             IF v_reg_dcv.importe_anticipo < v_monto_prorrateo_mb THEN
                                raise exception 'el monto a cobrar no puede ser mayor que el total por cobrar de anticipos de la factura';
                             END IF;
                             
                             --Todas las moendas estan en bolivianos             
                             IF    v_reg_dcv.importe_anticipo <  (v_total_importe_cobro_mb +  v_monto_prorrateo_mb) THEN  
                               raise exception 'BOB , el monto a pagar prorrateado (%) mas los pagos previos (%) sobrepasa el monto de los  anticipos del documento(%)', v_monto_prorrateo_mb , v_total_importe_cobro_mb, v_reg_dcv.importe_retgar; 
                             END IF;
                         
                 ELSEIF v_id_moneda_tri = v_reg_dcv.id_moneda  THEN
                       
                                  IF v_reg_dcv.importe_anticipo < v_monto_prorrateo_mt THEN
                                    raise exception 'el monto a cobrar no pueder  ser mayor que el total de anticipos de la factura ';
                                  END IF;
                         
                                 -- Todas las moendas estan en dolares
                                  IF  v_reg_dcv.importe_anticipo <  (v_total_importe_cobro_mt +  v_monto_prorrateo_mt) THEN  
                                    raise exception 'USD , el monto a pagar prorrateado (%) mas los pagos previos (%)sobrepasa el monto de los anticipos del documento(%)', v_monto_prorrateo_mt , v_total_importe_cobro_mt, v_reg_dcv.importe_retgar; 
                                  END IF;
                 ELSE
                          raise exception 'moneda no soportada';
            	 END IF;
           
           
           END IF;
                        
  -------------------------------#2  F-EGS 10/09/2018------------------------------------------
           
           
           
      
           
           
             
             
             -------------------------------------------------------------------------------
             -- el monto total de las factursa no peude sobrepsar el monto totla a cobrar
             ------------------------------------------------------------------------------
             
             -- sumamos totas las facturas regitrdas para este pago en moenda base
             
             select
                  sum(csd.importe_mb)
                into
                 v_total_regitro_previos_facturas_mb
             from cbr.tcobro_simple_det csd
             where   csd.id_cobro_simple = v_parametros.id_cobro_simple 
                   and csd.estado_reg = 'activo';
                   
             v_total_regitro_previos_facturas_mb = COALESCE(v_total_regitro_previos_facturas_mb,0);      
                   
             --    raise exception '...   1) % , 2) % ,  3)  %', v_total_regitro_previos_facturas_mb , v_monto_prorrateo_mb ,  v_registros_cbr.importe_mb;
             
             IF  (v_total_regitro_previos_facturas_mb + v_monto_prorrateo_mb)  >  v_registros_cbr.importe_mb  THEN
                   raise  'BOB el total de facturas previas  (%)y la que intenta insertar (%) no puede superar el total del cobro previsto (%)',v_total_regitro_previos_facturas_mb ,v_monto_prorrateo_mb,  v_registros_cbr.importe_mb ;
             END IF;
             
             
             --insertar registro en el detalle (en la moneda  del COBRO, covertir a  moneda  base)
             INSERT INTO   cbr.tcobro_simple_det
                        (
                          id_usuario_reg,                       
                          fecha_reg,                        
                          estado_reg,
                          id_usuario_ai,
                          usuario_ai,                        
                          id_cobro_simple,
                          id_doc_compra_venta,
                          importe,
                          importe_mb,
                          importe_mt,
                          importe_ma
                        )
                        VALUES (
                          p_id_usuario,                         
                          now(),                        
                          'activo',
                          v_parametros._id_usuario_ai,
			              v_parametros._nombre_usuario_ai,                          
                          v_parametros.id_cobro_simple,
                          v_parametros.id_doc_compra_venta,
                          v_parametros.monto_prorrateo,
                          v_monto_prorrateo_mb,
                          v_monto_prorrateo_mt,
                          v_monto_prorrateo_ma
                        );
             
             -- NOTA, no permitir la edicion de los tipos de cambio en cbte de COBRO, para  evitar inconsistencias
              
             --  los equivalentes en moneda base y moneda de triangulacion segun el tipo de cambio del comprobante
			 --  el tipo de cambio de la cabecera cambia a convenido....
             
             
           -- raise exception 'fin'; 
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Facturas/Recibos modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_cobro_simple',v_parametros.id_cobro_simple::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;
         
	else
     
    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

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
