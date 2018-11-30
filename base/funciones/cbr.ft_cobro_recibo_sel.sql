CREATE OR REPLACE FUNCTION cbr.ft_cobro_recibo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Cuenta Documenta
 FUNCION: 		cbr.ft_cobro_recibo_sel 
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'cbr.ft_cobro_recibo_sel '
 AUTOR: 		 (admin)
 FECHA:	        01-01-2018 06:21:25
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				01-01-2018 06:21:25								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'cbr.tcobro_simple_det'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    
    	
    v_id_entidad		integer;
    v_id_deptos			varchar;
    v_registros 		record;
    v_reg_entidad		record;
    v_tabla_origen    	varchar;
    v_filtro     		varchar;
    v_tipo   			varchar;
    v_sincronizar		varchar;
    v_gestion			integer;
    v_filtro_ext		varchar;    
    V_filtroLCV         varchar;
    v_id_auxiliar  		integer;
    v_id_auxiliar_2  	integer;
 
    v_estado            varchar;
    v_historico         varchar;
    v_inner             varchar;
    v_strg_cd           varchar;
    v_strg_obs          varchar;
    
    v_parametroCambiante  	integer;
    
      
    v_bandera						varchar;
   v_bandera_rg 					varchar;
   v_bandera_ant					varchar;
   v_bandera_regularizacion			varchar;
   v_bandera_regularizacion_rg			varchar;
   v_bandera_regularizacion_ant		varchar;
    
			    
BEGIN

	v_nombre_funcion = 'cbr.ft_cobro_recibo_sel ';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CBR_COBREC_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		01-01-2018 06:21:25
	***********************************/

	if(p_transaccion='CBR_COBREC_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:=' SELECT 
                         ps.id_cobro_simple,
                         moneda.codigo,     
                         ps.id_cuenta_bancaria,
                         ps.importe,
                         pxp.f_convertir_num_a_letra(importe) as importe_literal,
                         pro.desc_proveedor,
                         ps.id_funcionario,
                         ps.id_proveedor,   
                         ps.nro_tramite,
                         ps.obs,    
                         ps.fecha
                  FROM cbr.tcobro_simple ps
                       JOIN param.tmoneda moneda ON moneda.id_moneda = ps.id_moneda
                       JOIN param.vproveedor pro ON pro.id_proveedor = ps.id_proveedor
                       JOIN param.tperiodo per ON ps.fecha >= per.fecha_ini AND ps.fecha <=
                         per.fecha_fin
                  where ps.id_proceso_wf ='||v_parametros.id_proceso_wf||'';
			
			--Devuelve la respuesta
            
            --RAISE NOTICE '%',v_consulta; 
           --RAISE EXCEPTION '%', v_consulta;
			return v_consulta;
						
		end;
        
       
     /*********************************
 	#TRANSACCION:  'CBR_COBCBR_SEL'
 	#DESCRIPCION:	Consulta de datos para la grilla
 	#AUTOR:		Rensi Arteaga Copari
 	#FECHA:		04-04-2018 15:57:09
	***********************************/

	elseif(p_transaccion='CBR_COBCBR_SEL')then

    	begin
            
             v_bandera = split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 1);
             v_bandera_rg = split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 1);
			 v_bandera_regularizacion = split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 2);
             v_bandera_regularizacion_rg = split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 2);
             v_bandera_ant = split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 1);
             v_bandera_regularizacion_ant = split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 2);
                      
    		--Sentencia de la consulta
			v_consulta:='
             WITH  doc_cobrado(
                                    id_doc_compra_venta,                                  
                                    importe_mb,
                                    importe_mt,
                                    nro_tramite,
                                    id_cobro_simple,
                                    id_tipo_cobro_simple
                                    
                                            ) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,                                      
                                       sum(COALESCE(csd.importe_mb,0)) as importe_mb,
                                       sum(COALESCE(csd.importe_mt,0)) as importe_mt,
                                       pxp.aggarray(cs.nro_tramite),
                                       pxp.aggarray(csd.id_cobro_simple),
                                       pxp.aggarray(tcs.id_tipo_cobro_simple)
                                                                              
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                    inner join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                                    inner join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cs.id_tipo_cobro_simple
                                    WHERE tcs.codigo in ('''||v_bandera||''','''|| v_bandera_regularizacion||''')
                                    group by dcv.id_doc_compra_venta
                           ),
 doc_cobrado_retgar(
                                    id_doc_compra_venta,                                  
                                    importe_mb,
                                    importe_mt
                                            ) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,                                      
                                       sum(csd.importe_mb) as importe_mb,
                                       sum(csd.importe_mt) as importe_mt
                                                                              
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                    left join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                                    left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cs.id_tipo_cobro_simple
                                    WHERE tcs.codigo in ('''||v_bandera_rg||''','''||v_bandera_regularizacion_rg||''')
                                    group by dcv.id_doc_compra_venta
                           )
                           ,
 doc_cobrado_anticipo(
                                    id_doc_compra_venta,                                  
                                    importe_mb,
                                    importe_mt
                                            ) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,                                      
                                       sum(csd.importe_mb) as importe_mb,
                                       sum(csd.importe_mt) as importe_mt
                                                                              
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                    left join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                                    left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cs.id_tipo_cobro_simple
                                    WHERE tcs.codigo in ('''||v_bandera_ant||''','''||v_bandera_regularizacion_ant||''')
                                    group by dcv.id_doc_compra_venta
                           ),
   numero_cobro(
                                    id_doc_compra_venta,                                  
                                    nro_tramite,
                                    id_cobro_simple,
                                    id_tipo_cobro_simple
                                    
                                            ) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,
                                       pxp.aggarray(cs.nro_tramite),
                                       pxp.aggarray(csd.id_cobro_simple),
                                       pxp.aggarray(tcs.id_tipo_cobro_simple)
                                                                              
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                    left join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                                    left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cs.id_tipo_cobro_simple
                                    
                                    group by dcv.id_doc_compra_venta
                        )
            
            select
                            dcv.id_doc_compra_venta,
                            dcv.revisado,
                            dcv.movil,
                            dcv.tipo,
                            COALESCE(dcv.importe_excento,0)::numeric as importe_excento,
                            dcv.id_plantilla,
                            dcv.fecha,
                            dcv.nro_documento,
                            dcv.nit,
                            COALESCE(dcv.importe_ice,0)::numeric as importe_ice,
                            dcv.nro_autorizacion,
                            COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                            COALESCE(dcv.importe_descuento,0)::numeric as importe_descuento,
                            COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                            dcv.sw_contabilizar,
                            COALESCE(dcv.tabla_origen,''ninguno'') as tabla_origen,
                            dcv.estado,
                            dcv.id_depto_conta,
                            dcv.id_origen,
                            dcv.obs,
                            dcv.estado_reg,
                            dcv.codigo_control,
                            COALESCE(dcv.importe_it,0)::numeric as importe_it,
                            dcv.razon_social,
                            dcv.id_usuario_ai,
                            dcv.id_usuario_reg,
                            dcv.fecha_reg,
                            dcv.usuario_ai,
                            dcv.id_usuario_mod,
                            dcv.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dep.nombre as desc_depto,
                            pla.desc_plantilla,
                            COALESCE(dcv.importe_descuento_ley,0)::numeric as importe_descuento_ley,
                            COALESCE(dcv.importe_pago_liquido,0)::numeric as importe_pago_liquido,
                            dcv.nro_dui,
                            dcv.id_moneda,
                            mon.codigo as desc_moneda,
                            dcv.id_int_comprobante,
                            COALESCE(dcv.nro_tramite,''''),
                            COALESCE(ic.nro_cbte,dcv.id_int_comprobante::varchar)::varchar  as desc_comprobante,
                            COALESCE(dcv.importe_pendiente,0)::numeric as importe_pendiente,
                            COALESCE(dcv.importe_anticipo,0)::numeric as importe_anticipo,
                            COALESCE(dcv.importe_retgar,0)::numeric as importe_retgar,
                            COALESCE(dcv.importe_neto,0)::numeric as importe_neto,
                            aux.id_auxiliar,
                            aux.codigo_auxiliar,
                            aux.nombre_auxiliar,
                            dcv.id_tipo_doc_compra_venta,
                            (tdcv.codigo||'' - ''||tdcv.nombre)::Varchar as desc_tipo_doc_compra_venta,
                            (dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0))     as importe_aux_neto,
                            fun.id_funcionario,
                            fun.desc_funcionario2::varchar,
                            ic.fecha as fecha_cbte,
                            ic.estado_reg as estado_cbte,
                            COALESCE(doc.importe_mb,0) as importe_cobrado_mb,
                            COALESCE(doc.importe_mt,0) as importe_cobrado_mt,
                            COALESCE(docrg.importe_mb,0) as importe_cobrado_retgar_mb,
                            COALESCE(docrg.importe_mt,0) as importe_cobrado_retgar_mt,
 							COALESCE(docanti.importe_mb,0) as importe_cobrado_ant_mb,
                            COALESCE(docanti.importe_mt,0) as importe_cobrado_ant_mt,
                           (COALESCE(dcv.importe_pago_liquido,0)+COALESCE(doc.importe_mb,0)+ COALESCE(docrg.importe_mb,0)+ COALESCE(docanti.importe_mb,0)) as importe_total_cobrado_mb,
                           (COALESCE(dcv.importe_pago_liquido,0)+COALESCE(doc.importe_mt,0)+ COALESCE(docrg.importe_mt,0)+ COALESCE(docanti.importe_mt,0)) as importe_total_cobrado_mt,
                            case
                              when dcv.id_moneda  = 1  then
                                 COALESCE(dcv.importe_pendiente,0) - COALESCE(doc.importe_mb,0)
                              when  dcv.id_moneda  = 2 then
                                 COALESCE(dcv.importe_pendiente,0)  - COALESCE(doc.importe_mt,0)
                              else  
                                 0 
                            end as saldo_por_cobrar_pendiente, 
                            case
                              when dcv.id_moneda  = 1  then
                                COALESCE(dcv.importe_retgar,0) - COALESCE(docrg.importe_mb,0)
                              when  dcv.id_moneda  = 2 then
                                  COALESCE(dcv.importe_retgar,0)  - COALESCE(docrg.importe_mt,0)
                              else  
                                 0 
                            end as saldo_por_cobrar_retgar, 
                            case
                              when dcv.id_moneda  = 1  then
                                COALESCE(dcv.importe_anticipo,0) - COALESCE(docanti.importe_mb,0)
                              when  dcv.id_moneda  = 2 then
                                  COALESCE(dcv.importe_anticipo,0)  - COALESCE(docanti.importe_mt,0)
                              else  
                                 0 
                            end as saldo_por_cobrar_anticipo,
                             
                            case
                              when dcv.id_moneda  = 1  then
                                COALESCE(dcv.importe_pendiente,0) + COALESCE(dcv.importe_retgar,0)+ COALESCE(dcv.importe_anticipo,0) -  (COALESCE(doc.importe_mb,0)+ COALESCE(docrg.importe_mb,0)+ COALESCE(docanti.importe_mb,0))
                              when  dcv.id_moneda  = 2 then
                                COALESCE(dcv.importe_pendiente,0) + COALESCE(dcv.importe_retgar,0)+ COALESCE(dcv.importe_anticipo,0) - (COALESCE(doc.importe_mt,0)+ COALESCE(docrg.importe_mt,0)+ COALESCE(docanti.importe_mt,0))
                              else  
                                 0 
                            end as saldo_por_cobrar,
                            
                            dcv.id_contrato,
                            con.numero as nro_contrato
                          
						from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join doc_cobrado doc on doc.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join doc_cobrado_retgar docrg on docrg.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join doc_cobrado_anticipo docanti on docanti.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join numero_cobro nuco on nuco.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante                         
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
                          left join orga.vfuncionario fun on fun.id_funcionario = dcv.id_funcionario
                          left join param.tproveedor provee on provee.id_auxiliar = aux.id_auxiliar
                          left join param.vproveedor vprovee on vprovee.id_proveedor = provee.id_proveedor
                          left join param.tperiodo per on per.id_periodo =  dcv.id_periodo
                          left join leg.tcontrato con on con.id_contrato = dcv.id_contrato
				        where  pla.tipo_plantilla = ''venta''  and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			--RAISE NOTICE '%',v_consulta; 
           --RAISE EXCEPTION '%', v_consulta;
      
			--Devuelve la respuesta
			return v_consulta;

		end;
        

    /*********************************
 	#TRANSACCION:  'CBR_COBCBR_CONT'
 	#DESCRIPCION:	Conteo de registros para la grilla
 	#AUTOR:		rensi
 	#FECHA:		04-04-2018 15:57:09
	***********************************/

	elsif(p_transaccion='CBR_COBCBR_CONT')then
             
             v_bandera = split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 1);
             v_bandera_rg = split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 1);
			 v_bandera_regularizacion = split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 2);
             v_bandera_regularizacion_rg = split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 2);
             v_bandera_ant = split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 1);
             v_bandera_regularizacion_ant = split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 2);
     
          begin
            
			--Sentencia de la consulta de conteo de registros
			v_consulta:=' WITH  doc_cobrado(
                                    id_doc_compra_venta,                                  
                                    importe_mb,
                                    importe_mt,
                                    nro_tramite,
                                    id_cobro_simple,
                                    id_tipo_cobro_simple
                                    
                                            ) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,                                      
                                       sum(COALESCE(csd.importe_mb,0)) as importe_mb,
                                       sum(COALESCE(csd.importe_mt,0)) as importe_mt,
                                       pxp.aggarray(cs.nro_tramite),
                                       pxp.aggarray(csd.id_cobro_simple),
                                       pxp.aggarray(tcs.id_tipo_cobro_simple)
                                                                              
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                    inner join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                                    inner join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cs.id_tipo_cobro_simple
                                    WHERE tcs.codigo in ('''||v_bandera||''','''|| v_bandera_regularizacion||''')
                                    group by dcv.id_doc_compra_venta
                           ),
 doc_cobrado_retgar(
                                    id_doc_compra_venta,                                  
                                    importe_mb,
                                    importe_mt
                                            ) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,                                      
                                       sum(csd.importe_mb) as importe_mb,
                                       sum(csd.importe_mt) as importe_mt
                                                                              
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                    left join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                                    left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cs.id_tipo_cobro_simple
                                    WHERE tcs.codigo in ('''||v_bandera_rg||''','''||v_bandera_regularizacion_rg||''')
                                    group by dcv.id_doc_compra_venta
                           )
                           ,
 doc_cobrado_anticipo(
                                    id_doc_compra_venta,                                  
                                    importe_mb,
                                    importe_mt
                                            ) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,                                      
                                       sum(csd.importe_mb) as importe_mb,
                                       sum(csd.importe_mt) as importe_mt
                                                                              
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                    left join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                                    left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cs.id_tipo_cobro_simple
                                    WHERE tcs.codigo in ('''||v_bandera_ant||''','''||v_bandera_regularizacion_ant||''')
                                    group by dcv.id_doc_compra_venta
                           ) ,
     numero_cobro(
                                    id_doc_compra_venta,                                  
                                    nro_tramite,
                                    id_cobro_simple,
                                    id_tipo_cobro_simple
                                    
                                            ) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,
                                       pxp.aggarray(cs.nro_tramite),
                                       pxp.aggarray(csd.id_cobro_simple),
                                       pxp.aggarray(tcs.id_tipo_cobro_simple)
                                                                              
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                    left join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple
                                    left join cbr.ttipo_cobro_simple tcs on tcs.id_tipo_cobro_simple = cs.id_tipo_cobro_simple
                                    
                                    group by dcv.id_doc_compra_venta
                        )
            
                           
            
            select		 
            			 count(dcv.id_doc_compra_venta), 
            			 sum(COALESCE(dcv.importe_doc,0))::numeric as total_importe,
                         sum((COALESCE(dcv.importe_pago_liquido,0)+COALESCE(doc.importe_mb,0)+ COALESCE(docrg.importe_mb,0)+ COALESCE(docanti.importe_mb,0)))::numeric  as total_importe_cobrado,
                         sum((COALESCE(dcv.importe_pendiente,0)-COALESCE(doc.importe_mb,0))+(COALESCE(dcv.importe_retgar,0)-COALESCE(docrg.importe_mb,0))+(COALESCE(dcv.importe_anticipo,0)-COALESCE(docanti.importe_mb,0)))as total_saldo_por_cobrar
						
                          from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join doc_cobrado doc on doc.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join doc_cobrado_retgar docrg on docrg.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join doc_cobrado_anticipo docanti on docanti.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join numero_cobro nuco on nuco.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante                         
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
                          left join orga.vfuncionario fun on fun.id_funcionario = dcv.id_funcionario
                          left join param.tproveedor provee on provee.id_auxiliar = aux.id_auxiliar
                          left join param.vproveedor vprovee on vprovee.id_proveedor = provee.id_proveedor
                          left join param.tperiodo per on per.id_periodo =  dcv.id_periodo
                          left join leg.tcontrato con on con.id_contrato = dcv.id_contrato
				        where  pla.tipo_plantilla = ''venta'' and ';

                      --Definicion de la respuesta
                      v_consulta:=v_consulta||v_parametros.filtro;
					 --- v_consulta:=v_consulta||' group by  total_importe,total_importe_cobrado,total_saldo_por_cobrar' ;
                   --  RAISE NOTICE '%',v_consulta; 
                   -- RAISE EXCEPTION '%', v_consulta;
                      --Devuelve la respuesta
                      return v_consulta;
            end; 		
         /*********************************
          #TRANSACCION:  'CBR_COBRS_SEL'
          #DESCRIPCION:	consulta  razon social 
       	
          ***********************************/

          elsif(p_transaccion='CBR_COBRS_SEL')then

              begin
                  --Sentencia de la consulta
                  v_consulta:='select DISTINCT dcv.razon_social,
                  				dcv.nit
                  from conta.tdoc_compra_venta dcv
                  inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                  where dcv.nit !='' '' and pla.tipo_informe = ''lcv'' and dcv.razon_social like UPPER(''%'||COALESCE(v_parametros.razon_social,'-')||'%'')';


                  v_consulta:=v_consulta||'  limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;



                  --Devuelve la respuesta
                  return v_consulta;

              end;
             /*********************************    
    #TRANSACCION:  'CBR_COBRS_CONT'
    #DESCRIPCION:   Conteo de registros de razon social
    #AUTOR:     admin   
    #FECHA:     31-12-2017 12:33:30
    ***********************************/

    elsif(p_transaccion='CBR_COBRS_CONT')then

        begin

            v_filtro='';
          
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count (DISTINCT dcv.razon_social)   
			from conta.tdoc_compra_venta dcv
            inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
             where dcv.nit !='' '' and pla.tipo_informe = ''lcv'' and dcv.razon_social like UPPER(''%'||COALESCE(v_parametros.razon_social,'-')||'%'')';

            --v_consulta = v_consulta || v_filtro;
            
            --Definicion de la respuesta            
           -- v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;
    
    /*********************************    
    #TRANSACCION:  'CBR_COBRO_SEL'
    #DESCRIPCION: listar cobro
    #AUTOR:     admin   
    #FECHA:     31-12-2017 12:33:30
    ***********************************/

   elsif(p_transaccion='CBR_COBRO_SEL')then
                    
        begin

           

            --Filtros
            v_filtro='';
			--RAISE NOTICE '%',v_parametros.razon_social; 
           --RAISE EXCEPTION '%',v_parametros.razon_social;

            --Sentencia de la consulta
            v_consulta:='select
                            pagsim.id_cobro_simple,
                            pagsim.estado_reg,
                            pagsim.id_depto_conta,
                            pagsim.nro_tramite,
                            pagsim.fecha,
                            pagsim.id_funcionario,
                            pagsim.estado,
                            pagsim.id_estado_wf,
                            pagsim.id_proceso_wf,
                            pagsim.obs,
                            pagsim.id_cuenta_bancaria,
                            pagsim.id_depto_lb,
                            pagsim.id_usuario_reg,
                            pagsim.fecha_reg,
                            pagsim.id_usuario_ai,
                            pagsim.usuario_ai,
                            pagsim.id_usuario_mod,
                            pagsim.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dep.codigo as desc_depto_conta,
                            fun.desc_funcionario1 as desc_funcionario,
                            (ins.nombre||'' - ''||cban.nro_cuenta)::varchar as desc_cuenta_bancaria,
                            deplb.codigo as desc_depto_lb,
                            pagsim.id_moneda,
                            pagsim.id_proveedor,
                            mon.codigo as desc_moneda,
                            pro.desc_proveedor,
                            pagsim.id_tipo_cobro_simple,
                            pagsim.id_funcionario_pago,
                            fun1.desc_funcionario1 as desc_funcionario_pago,
                            tps.codigo || '' - '' || tps.nombre as desc_tipo_cobro_simple,
                            tps.codigo as codigo_tipo_cobro_simple,
                            pagsim.nro_tramite_asociado,
                            pagsim.importe,
                            pagsim.id_obligacion_pago,
                            op.num_tramite as desc_obligacion_pago,
                            pagsim.id_caja,
                            caj.codigo as desc_caja,
                            paside.id_doc_compra_venta,
                            dcv.razon_social,
                            dcv.nit,
                            paside.importe_mb importe_cobro_factura,
                            (select 
                            ges.id_gestion
                            from param.tgestion ges
                            where ges.gestion = (date_part(''year'', pagsim.fecha))::integer
                            limit 1 offset 0) as id_gestion,
                            (select
                            id_periodo
                            from param.tperiodo
                            where pagsim.fecha between fecha_ini and fecha_fin
                            limit 1 offset 0) as id_periodo,
                            
                            pagsim.tipo_cambio,
                            pagsim.tipo_cambio_mt,
                            pagsim.tipo_cambio_ma,
                            pagsim.id_config_cambiaria,
                            pagsim.importe_mt,
                            pagsim.importe_mb,
                            pagsim.importe_ma,
                            forma_cambio
                            
                            
                        from cbr.tcobro_simple pagsim
                        inner join wf.testado_wf ew on ew.id_estado_wf = pagsim.id_estado_wf
                        inner join segu.tusuario usu1 on usu1.id_usuario = pagsim.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = pagsim.id_usuario_mod
                        inner join param.tdepto dep on dep.id_depto = pagsim.id_depto_conta
                        inner join orga.vfuncionario fun on fun.id_funcionario = pagsim.id_funcionario
                        left join tes.tcuenta_bancaria cban on cban.id_cuenta_bancaria = pagsim.id_cuenta_bancaria
                        left join param.tdepto deplb on deplb.id_depto = pagsim.id_depto_lb
                        left join param.tinstitucion ins on ins.id_institucion = cban.id_institucion
                        inner join param.tmoneda mon on mon.id_moneda = pagsim.id_moneda
                        left join param.vproveedor pro on pro.id_proveedor = pagsim.id_proveedor
                        inner join cbr.ttipo_cobro_simple tps on tps.id_tipo_cobro_simple = pagsim.id_tipo_cobro_simple
                        left join orga.vfuncionario fun1 on fun1.id_funcionario = pagsim.id_funcionario_pago
                        left join tes.tobligacion_pago op on op.id_obligacion_pago = pagsim.id_obligacion_pago
                        left join tes.tcaja caj on caj.id_caja = pagsim.id_caja
                        left join cbr.tcobro_simple_det paside on paside.id_cobro_simple = pagsim.id_cobro_simple
                        left join conta.tdoc_compra_venta dcv on dcv.id_doc_compra_venta = paside.id_doc_compra_venta
                        where  ';
			
            
		--RAISE NOTICE '%',v_consulta; 
        --RAISE EXCEPTION '%', v_consulta;
           -- v_consulta = v_consulta || v_filtro;
            
            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;
                        
        end;
        
         /*********************************    
    #TRANSACCION:  'CBR_COBRO_CONT'
    #DESCRIPCION:   Conteo de registros de cobros
    #AUTOR:     admin   
    #FECHA:     31-12-2017 12:33:30
    ***********************************/

    elsif(p_transaccion='CBR_COBRO_CONT')then

        begin

            v_filtro='';
          
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count(pagsim.id_cobro_simple),
                        COALESCE(sum(paside.importe_mb), 0)::numeric as total_importe_cobro_factura
                        from cbr.tcobro_simple pagsim
                        inner join wf.testado_wf ew on ew.id_estado_wf = pagsim.id_estado_wf
                        inner join segu.tusuario usu1 on usu1.id_usuario = pagsim.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = pagsim.id_usuario_mod
                        inner join param.tdepto dep on dep.id_depto = pagsim.id_depto_conta
                        inner join orga.vfuncionario fun on fun.id_funcionario = pagsim.id_funcionario
                        left join tes.tcuenta_bancaria cban on cban.id_cuenta_bancaria = pagsim.id_cuenta_bancaria
                        left join param.tdepto deplb on deplb.id_depto = pagsim.id_depto_lb
                        left join param.tinstitucion ins on ins.id_institucion = cban.id_institucion
                        inner join param.tmoneda mon on mon.id_moneda = pagsim.id_moneda
                        left join param.vproveedor pro on pro.id_proveedor = pagsim.id_proveedor
                        inner join cbr.ttipo_cobro_simple tps on tps.id_tipo_cobro_simple = pagsim.id_tipo_cobro_simple
                        left join orga.vfuncionario fun1 on fun1.id_funcionario = pagsim.id_funcionario_pago
                        left join tes.tobligacion_pago op on op.id_obligacion_pago = pagsim.id_obligacion_pago
                        left join tes.tcaja caj on caj.id_caja = pagsim.id_caja
                        left join cbr.tcobro_simple_det paside on paside.id_cobro_simple = pagsim.id_cobro_simple
                        left join conta.tdoc_compra_venta dcv on dcv.id_doc_compra_venta = paside.id_cobro_simple
                        where ';

            v_consulta = v_consulta || v_filtro;
            
            --Definicion de la respuesta            
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;
         
      /*********************************    
        #TRANSACCION:  'CBR_CBRCOMBO_SEL'
        #DESCRIPCION: listar cobro Combo
        #AUTOR:     admin   
        #FECHA:     31-12-2017 12:33:30
        ***********************************/

       elsif(p_transaccion='CBR_CBRCOMBO_SEL')then
                        
            begin
                --Sentencia de la consulta
                v_consulta:='select
                                pagsim.id_cobro_simple,
                                pagsim.estado_reg,
                                pagsim.id_depto_conta,
                                pagsim.nro_tramite,
                                pagsim.fecha,
                                pagsim.id_funcionario,
                                pagsim.estado,
                                pagsim.id_estado_wf,
                                pagsim.id_proceso_wf,
                                pagsim.obs,
                                pagsim.id_cuenta_bancaria,
                                pagsim.id_depto_lb,
                                pagsim.id_usuario_reg,
                                pagsim.fecha_reg,
                                pagsim.id_usuario_ai,
                                pagsim.usuario_ai,
                                pagsim.id_usuario_mod,
                                pagsim.fecha_mod,
                                tcb.nombre                   
                            from cbr.tcobro_simple pagsim
                            left join cbr.ttipo_cobro_simple tcb on tcb.id_tipo_cobro_simple = pagsim.id_tipo_cobro_simple
                                 where ';
    			
                
           
               -- v_consulta = v_consulta || v_filtro;
                
                --Definicion de la respuesta
                v_consulta:=v_consulta||v_parametros.filtro;
               -- v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
				 --RAISE NOTICE '%',v_consulta; 
            	--RAISE EXCEPTION '%', v_consulta;	
                --Devuelve la respuesta
                return v_consulta;
                            
            end;
                  
            /*********************************    
          #TRANSACCION:  'CBR_CBRCOMBO_CONT'
          #DESCRIPCION:   Conteo de registros de cobros combo
          #AUTOR:     admin   
          #FECHA:     31-12-2017 12:33:30
          ***********************************/

          elsif(p_transaccion='CBR_CBRCOMBO_CONT')then

              begin

                  v_filtro='';
                
                  --Sentencia de la consulta de conteo de registros
                  v_consulta:='select count(pagsim.id_cobro_simple)
                              from cbr.tcobro_simple pagsim
                              where ';

                  --v_consulta = v_consulta || v_filtro;
                  
                  --Definicion de la respuesta            
                  v_consulta:=v_consulta||v_parametros.filtro;

                  --Devuelve la respuesta
                  return v_consulta;

              end;
      
          /*********************************
 	#TRANSACCION:  'CBR_COBREFA_SEL'
 	#DESCRIPCION:	Consulta de datos reporte facturas en pdf
 	#AUTOR:		
 	#FECHA:		04-04-2018 15:57:09
	***********************************/

	elseif(p_transaccion='CBR_COBREFA_SEL')then

    	begin
         --RAISE NOTICE 'hola'; 
         --RAISE EXCEPTION 'hola';
         
               
         
    		--Sentencia de la consulta
			v_consulta:='
             WITH  doc_cobrado(
                                     id_doc_compra_venta,                                  
                                    importe_mb,
                                    importe_mt) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,                                      
                                       sum(csd.importe_mb) as importe_mb,
                                       sum(csd.importe_mt) as importe_mt                                        
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                   
                                    group by dcv.id_doc_compra_venta
                           )
            
            select          
            				ROW_NUMBER () OVER (ORDER BY  dcv.id_doc_compra_venta )as id,
                            dcv.id_doc_compra_venta,
                            dcv.revisado,
                            dcv.movil,
                            dcv.tipo,
                            COALESCE(dcv.importe_excento,0)::numeric as importe_excento,
                            dcv.id_plantilla,
                            dcv.fecha,
                            dcv.nro_documento,
                            dcv.nit,
                            COALESCE(dcv.importe_ice,0)::numeric as importe_ice,
                            dcv.nro_autorizacion,
                            COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                            COALESCE(dcv.importe_descuento,0)::numeric as importe_descuento,
                            COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                            dcv.sw_contabilizar,
                            COALESCE(dcv.tabla_origen,''ninguno'') as tabla_origen,
                            dcv.estado,
                            dcv.id_depto_conta,
                            dcv.id_origen,
                            dcv.obs,
                            dcv.estado_reg,
                            dcv.codigo_control,
                            COALESCE(dcv.importe_it,0)::numeric as importe_it,
                            dcv.razon_social,
                            dcv.id_usuario_ai,
                            dcv.id_usuario_reg,
                            dcv.fecha_reg,
                            dcv.usuario_ai,
                            dcv.id_usuario_mod,
                            dcv.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dep.nombre as desc_depto,
                            pla.desc_plantilla,
                            COALESCE(dcv.importe_descuento_ley,0)::numeric as importe_descuento_ley,
                            COALESCE(dcv.importe_pago_liquido,0)::numeric as importe_pago_liquido,
                            dcv.nro_dui,
                            dcv.id_moneda,
                            mon.codigo as desc_moneda,
                            dcv.id_int_comprobante,
                            COALESCE(dcv.nro_tramite,''''),
                            COALESCE(ic.nro_cbte,dcv.id_int_comprobante::varchar)::varchar  as desc_comprobante,
                            COALESCE(dcv.importe_pendiente,0)::numeric as importe_pendiente,
                            COALESCE(dcv.importe_anticipo,0)::numeric as importe_anticipo,
                            COALESCE(dcv.importe_retgar,0)::numeric as importe_retgar,
                            COALESCE(dcv.importe_neto,0)::numeric as importe_neto,
                            aux.id_auxiliar,
                            aux.codigo_auxiliar,
                            aux.nombre_auxiliar,
                            dcv.id_tipo_doc_compra_venta,
                            (tdcv.codigo||'' - ''||tdcv.nombre)::Varchar as desc_tipo_doc_compra_venta,
                            (dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0))     as importe_aux_neto,
                            fun.id_funcionario,
                            fun.desc_funcionario2::varchar,
                            ic.fecha as fecha_cbte,
                            ic.estado_reg as estado_cbte,
                            paside.id_cobro_simple,
                            pagsim.fecha as fecha_cobro,  
                            pagsim.nro_tramite as nro_tramite_cobro,
                            pagsim.importe as importe_cobro,
                            pagsim.id_moneda as id_moneda_cobro,
                            mo.codigo as desc_moneda_cobro,
                            paside.importe_mb as importe_cobro_factura,
                            dcv.id_periodo, 
                            per.id_gestion,
                           (COALESCE(doc.importe_mb,0)+ COALESCE(dcv.importe_pago_liquido,0)) as importe_cobrado_mb,
                            COALESCE(doc.importe_mt,0) as importe_cobrado_mt,
                            case
                              when dcv.id_moneda  = 1  then
                                COALESCE(dcv.importe_pendiente,0) + COALESCE(dcv.importe_retgar,0)+ COALESCE(dcv.importe_anticipo,0) - COALESCE(doc.importe_mb,0) 
                              when  dcv.id_moneda  = 2 then
                                COALESCE(dcv.importe_pendiente,0) + COALESCE(dcv.importe_retgar,0)+ COALESCE(dcv.importe_anticipo,0) - COALESCE(doc.importe_mt ,0)
                              else  
                                 0 
                            end as saldo_por_cobrar,
                            dcv.id_contrato,
                            cto.numero as nro_contrato
                            
						from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join doc_cobrado doc on doc.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante                         
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
                          left join orga.vfuncionario fun on fun.id_funcionario = dcv.id_funcionario
                          left join cbr.tcobro_simple_det paside on paside.id_doc_compra_venta =  dcv.id_doc_compra_venta
                          left join cbr.tcobro_simple pagsim on pagsim.id_cobro_simple =  paside.id_cobro_simple
                          left join param.tperiodo per on per.id_periodo =  dcv.id_periodo
                          left join param.tmoneda mo on mo.id_moneda = pagsim.id_moneda
                          left join leg.tcontrato cto on cto.id_contrato = dcv.id_contrato
				        where  pla.tipo_plantilla = ''venta'' and  '; 
 
		--RAISE NOTICE '%',v_consulta; 
        --RAISE EXCEPTION '%', v_consulta;
            v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;
        
                
     /*********************************
 	#TRANSACCION:  'CBR_LISFAC_SEL'
 	#DESCRIPCION:	Consulta de datos lista combo
 	#AUTOR:		EGS
 	#FECHA:		04-04-2018 15:57:09
	***********************************/

	elseif(p_transaccion='CBR_LISFAC_SEL')then

    	begin
            
                      
    		--Sentencia de la consulta
			v_consulta:='
            WITH  doc_cobrado(
                                    id_doc_compra_venta,
                                    fecha_ultimo_pago,                                 
                                    importe_mb,
                                    importe_mt) 
                            
                           as (
                                    select 
                                       dcv.id_doc_compra_venta,
                                       MAX(cs.fecha),                                      
                                       sum(csd.importe_mb) as importe_mb,
                                       sum(csd.importe_mt) as importe_mt                                        
                                    from conta.tdoc_compra_venta dcv 
                                    inner join cbr.tcobro_simple_det csd on csd.id_doc_compra_venta = dcv.id_doc_compra_venta
                                    inner join cbr.tcobro_simple cs on cs.id_cobro_simple = csd.id_cobro_simple   
                                   
                                    group by dcv.id_doc_compra_venta
                           )
            
            select          
            				ROW_NUMBER () OVER (ORDER BY  dcv.id_doc_compra_venta )as id,
                            dcv.id_doc_compra_venta,
                            dcv.revisado,
                            dcv.movil,
                            dcv.tipo,
                            COALESCE(dcv.importe_excento,0)::numeric as importe_excento,
                            dcv.id_plantilla,
                            dcv.fecha,
                            dcv.nro_documento,
                            dcv.nit,
                            COALESCE(dcv.importe_ice,0)::numeric as importe_ice,
                            dcv.nro_autorizacion,
                            COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                            COALESCE(dcv.importe_descuento,0)::numeric as importe_descuento,
                            COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                            dcv.sw_contabilizar,
                            COALESCE(dcv.tabla_origen,''ninguno'') as tabla_origen,
                            dcv.estado,
                            dcv.id_depto_conta,
                            dcv.id_origen,
                            dcv.obs,
                            dcv.estado_reg,
                            dcv.codigo_control,
                            COALESCE(dcv.importe_it,0)::numeric as importe_it,
                            dcv.razon_social,
                            dcv.id_usuario_ai,
                            dcv.id_usuario_reg,
                            dcv.fecha_reg,
                            dcv.usuario_ai,
                            dcv.id_usuario_mod,
                            dcv.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dep.nombre as desc_depto,
                            pla.desc_plantilla,
                            COALESCE(dcv.importe_descuento_ley,0)::numeric as importe_descuento_ley,
                            COALESCE(dcv.importe_pago_liquido,0)::numeric as importe_pago_liquido,
                            dcv.nro_dui,
                            dcv.id_moneda,
                            mon.codigo as desc_moneda,
                            dcv.id_int_comprobante,
                            COALESCE(dcv.nro_tramite,''''),
                            COALESCE(ic.nro_cbte,dcv.id_int_comprobante::varchar)::varchar  as desc_comprobante,
                            COALESCE(dcv.importe_pendiente,0)::numeric as importe_pendiente,
                            COALESCE(dcv.importe_anticipo,0)::numeric as importe_anticipo,
                            COALESCE(dcv.importe_retgar,0)::numeric as importe_retgar,
                            COALESCE(dcv.importe_neto,0)::numeric as importe_neto,
                            aux.id_auxiliar,
                            aux.codigo_auxiliar,
                            aux.nombre_auxiliar,
                            dcv.id_tipo_doc_compra_venta,
                            (tdcv.codigo||'' - ''||tdcv.nombre)::Varchar as desc_tipo_doc_compra_venta,
                            (dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0))     as importe_aux_neto,
                            fun.id_funcionario,
                            fun.desc_funcionario2::varchar,
                            ic.fecha as fecha_cbte,
                            ic.estado_reg as estado_cbte,
                            ic.nro_cbte,
                           (COALESCE(doc.importe_mb,0)+ COALESCE(dcv.importe_pago_liquido,0)) as importe_cobrado_mb,
                            COALESCE(doc.importe_mt,0) as importe_cobrado_mt,
                            doc.fecha_ultimo_pago::date,
                            case
                              when dcv.id_moneda  = 1  then
                                COALESCE(dcv.importe_pendiente,0) + COALESCE(dcv.importe_retgar,0) + COALESCE(dcv.importe_anticipo,0)- COALESCE(doc.importe_mb,0) 
                              when  dcv.id_moneda  = 2 then
                                COALESCE(dcv.importe_pendiente,0) + COALESCE(dcv.importe_retgar,0) + COALESCE(dcv.importe_anticipo,0)- COALESCE(doc.importe_mt ,0)
                              else  
                                 0 
                            end as saldo_por_cobrar,
                            dcv.id_contrato,
                            cto.numero as nro_contrato
                            
						from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join doc_cobrado doc on doc.id_doc_compra_venta = dcv.id_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante                         
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
                          left join orga.vfuncionario fun on fun.id_funcionario = dcv.id_funcionario
                          left join leg.tcontrato cto on cto.id_contrato = dcv.id_contrato
				        where  pla.tipo_plantilla = ''venta''  and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by dcv.razon_social asc' ;
		--RAISE NOTICE '%',v_consulta; 
        --RAISE EXCEPTION '%', v_consulta;
            
			--Devuelve la respuesta
			return v_consulta;

		end;
        

    /*********************************
 	#TRANSACCION:  'CBR_LISFAC_CONT'
 	#DESCRIPCION:	Conteo de registros lista facturas  combo
 	#AUTOR:		rensi
 	#FECHA:		04-04-2018 15:57:09
	***********************************/

	elsif(p_transaccion='CBR_LISFAC_CONT')then

		
            begin
            
                      
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                              count(dcv.id_doc_compra_venta),
                              COALESCE(sum(dcv.importe_ice),0)::numeric  as total_importe_ice,
                              COALESCE(sum(dcv.importe_excento),0)::numeric  as total_importe_excento,
                              COALESCE(sum(dcv.importe_it),0)::numeric  as total_importe_it,
                              COALESCE(sum(dcv.importe_iva),0)::numeric  as total_importe_iva,
                              COALESCE(sum(dcv.importe_descuento),0)::numeric  as total_importe_descuento,
                              COALESCE(sum(dcv.importe_doc),0)::numeric  as total_importe_doc,
                              COALESCE(sum(dcv.importe_retgar),0)::numeric  as total_importe_retgar,
                              COALESCE(sum(dcv.importe_anticipo),0)::numeric  as total_importe_anticipo,
                              COALESCE(sum(dcv.importe_pendiente),0)::numeric  as tota_importe_pendiente,
                              COALESCE(sum(dcv.importe_neto),0)::numeric  as total_importe_neto,
                              COALESCE(sum(dcv.importe_descuento_ley),0)::numeric  as total_importe_descuento_ley,
                              COALESCE(sum(dcv.importe_pago_liquido),0)::numeric  as total_importe_pago_liquido,
                              COALESCE(sum(dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0)),0) as total_importe_aux_neto
                         from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante                         
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
                          left join orga.vfuncionario fun on fun.id_funcionario = dcv.id_funcionario
                          left join leg.tcontrato cto on cto.id_contrato = dcv.id_contrato       
				        where   pla.tipo_plantilla = ''venta'' and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
    	   --RAISE NOTICE '%',v_consulta; 
          -- RAISE EXCEPTION '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
	
					
	else
					     
		raise exception 'Transaccion inexistente';
					         
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

ALTER FUNCTION cbr.ft_cobro_recibo_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;