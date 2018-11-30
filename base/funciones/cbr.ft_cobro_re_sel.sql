CREATE OR REPLACE FUNCTION cbr.ft_cobro_re_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:       Cuenta Documenta
 FUNCION:       cbr.ft_cobro_re_sel
 DESCRIPCION:    Funcion que se usa para el reporte de cobros
 AUTOR:          (admin)
 FECHA:         
 COMENTARIOS:   
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE              FECHA               AUTOR               DESCRIPCION
 #
 ***************************************************************************/

DECLARE

    v_consulta          varchar;
    v_parametros        record;
    v_nombre_funcion    text;
    v_resp              varchar;
    v_estado            varchar;
    v_filtro            varchar;
    v_historico         varchar;
    v_inner             varchar;
    v_strg_cd           varchar;
    v_strg_obs          varchar;
   

	v_id_moneda_base	integer;
    v_id_moneda_tri	    integer;
     v_registro_moneda	record;
    va_id_depto			integer[];
    v_codigo_moneda_base		varchar;
    v_desde				varchar;
    v_hasta				varchar;
    
    
    
     v_bandera				 varchar;
     v_bandera_rg 			 varchar;
     v_bandera_ant			 varchar;
			     
                
BEGIN

    v_nombre_funcion = 'cbr.ft_cobro_re_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************    
    #TRANSACCION:  'CBR_CBRE_SEL'
    #DESCRIPCION:   Consulta de datos de la grilla cobros
    #AUTOR:     admin   
    #FECHA:     31-12-2017 12:33:30
    ***********************************/

    if(p_transaccion='CBR_CBRE_SEL')then
                    
        begin
			
        	 v_bandera = split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 1);
             v_bandera_rg = split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 1);
             v_bandera_ant = split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 1);
 
        
            --Sentencia de la consulta
            v_consulta:='  
            				
             WITH  tgestion(
                                    id_cobro_simple,                                  
                                    id_gestion,
                                    id_periodo) 
                            
                           as (
                                    select 
                                       cs.id_cobro_simple,
                                        (select 
                                          ges.id_gestion
                                          from param.tgestion ges
                                          where ges.gestion = (date_part(''year'', cs.fecha))::integer
                                          limit 1 offset 0)::integer as id_gestion,
                                          (select
                                          id_periodo
                                          from param.tperiodo
                                          where cs.fecha between fecha_ini and fecha_fin
                                          limit 1 offset 0)::integer as id_periodo                                      
                                    from cbr.tcobro_simple cs 
                                   
                                   
                           )
      select
                        	ROW_NUMBER () OVER (ORDER BY  pagsim.id_cobro_simple )::integer as id,
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
                            pagsim.tipo_cambio,
                            pagsim.tipo_cambio_mt,
                            pagsim.tipo_cambio_ma,
                            pagsim.id_config_cambiaria,
                            pagsim.importe_mt,
                            pagsim.importe_mb,
                            pagsim.importe_ma,
                            pagsim.forma_cambio,
                            COALESCE(pagsim.id_int_comprobante,0),
                            cbte.nro_cbte,
                            '''||v_bandera||'''::varchar as globalComun,
                            '''||v_bandera_rg||'''::varchar as globalRetgar,
                            '''||v_bandera_ant||'''::varchar as globalAnti,
                        	gest.id_gestion,
                            gest.id_periodo
                            
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
                        left join conta.tint_comprobante cbte on cbte.id_int_comprobante = pagsim.id_int_comprobante
                        inner join tgestion gest on gest.id_cobro_simple = pagsim.id_cobro_simple
                      
                        where  ';

           -- raise exception 'consulta %',v_consulta;
            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
           v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;
                        
        end;
    
        
    /*********************************    
    #TRANSACCION:  'CBR_CBRE_CONT'
    #DESCRIPCION:   Conteo de registros
    #AUTOR:     admin   
    #FECHA:     31-12-2017 12:33:30
    ***********************************/

    elsif(p_transaccion='CBR_CBRE_CONT')then

        begin

     


            --Sentencia de la consulta de conteo de registros
            v_consulta:='
             WITH  tgestion(
                                    id_cobro_simple,                                  
                                    id_gestion,
                                    id_periodo) 
                            
                           as (
                                    select 
                                       cs.id_cobro_simple,
                                        (select 
                                          ges.id_gestion
                                          from param.tgestion ges
                                          where ges.gestion = (date_part(''year'', cs.fecha))::integer
                                          limit 1 offset 0)::integer as id_gestion,
                                          (select
                                          id_periodo
                                          from param.tperiodo
                                          where cs.fecha between fecha_ini and fecha_fin
                                          limit 1 offset 0)::integer as id_periodo                                      
                                    from cbr.tcobro_simple cs 
                                  
                                   
                           )
                      
            
            			select count(pagsim.id_cobro_simple)
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
                        left join conta.tint_comprobante cbte on cbte.id_int_comprobante = pagsim.id_int_comprobante
						inner join tgestion gest on gest.id_cobro_simple = pagsim.id_cobro_simple
                        
                       
                        
                        where ';

            --Definicion de la respuesta          
            --v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
  
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;
        /*********************************
 	#TRANSACCION:  'CBR_CBRFA_SEL'
 	#DESCRIPCION:	Consulta de datos para la grilla factura
 	#AUTOR:		Rensi Arteaga Copari
 	#FECHA:		04-04-2018 15:57:09
	***********************************/

	elseif(p_transaccion='CBR_CBRFA_SEL')then

    	begin
            
                      
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
                            pagsim.id_tipo_cobro_simple,
                            pagsim.fecha as fecha_cobro,  
                            pagsim.nro_tramite as nro_tramite_cobro,
                            pagsim.importe as importe_cobro,
                            pagsim.id_moneda as id_moneda_cobro,
                            mo.codigo as desc_moneda_cobro,
                            paside.importe_mb as importe_cobro_factura,
                            dcv.id_periodo, 
                            per.id_gestion,
                            provee.id_proveedor,
                            vprovee.desc_proveedor,
                            COALESCE(doc.importe_mb,0) as importe_cobrado_mb,
                            COALESCE(doc.importe_mt,0) as importe_cobrado_mt,
                            case
                              when dcv.id_moneda  = 1  then
                                COALESCE(dcv.importe_pendiente,0) + COALESCE(dcv.importe_retgar,0)+ COALESCE(dcv.importe_anticipo,0) - COALESCE(doc.importe_mb,0) 
                              when  dcv.id_moneda  = 2 then
                                COALESCE(dcv.importe_pendiente,0) + COALESCE(dcv.importe_retgar,0)+ COALESCE(dcv.importe_anticipo,0) - COALESCE(doc.importe_mt ,0)
                              else  
                                 0 
                            end as saldo_por_cobrar
                            
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
                          left join param.tproveedor provee on provee.id_auxiliar = aux.id_auxiliar
                          left join param.vproveedor vprovee on vprovee.id_proveedor = provee.id_proveedor
				        where  pla.tipo_plantilla = ''venta''  and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			--v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			--RAISE NOTICE '%',v_consulta; 
          -- RAISE EXCEPTION '%', v_consulta;
            
			--Devuelve la respuesta
			return v_consulta;

		end;
        

    /*********************************
 	#TRANSACCION:  'CBR_CBRFA_CONT'
 	#DESCRIPCION:	Conteo de registros para la grilla
 	#AUTOR:		rensi
 	#FECHA:		04-04-2018 15:57:09
	***********************************/

	elsif(p_transaccion='CBR_CBRFA_CONT')then
        
             --RAISE EXCEPTION 'id_cobro_simple %', v_parametros.filtro;
      
        	 begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:=' WITH  doc_cobrado(
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
                              count(dcv.id_doc_compra_venta),
                              COALESCE(sum(dcv.importe_doc),0)::numeric  as total_importe,
                              COALESCE(sum(doc.importe_mb),0)::numeric  as total_importe_cobrado,
                              COALESCE(sum( dcv.importe_pendiente + dcv.importe_retgar + dcv.importe_anticipo - COALESCE(doc.importe_mb,0)),0)::numeric  as total_saldo_por_cobrar
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
                          left join param.tproveedor provee on provee.id_auxiliar = aux.id_auxiliar
                          left join param.vproveedor vprovee on vprovee.id_proveedor = provee.id_proveedor
				        where   pla.tipo_plantilla = ''venta'' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
    	   --RAISE NOTICE '%',v_consulta; 
          -- RAISE EXCEPTION '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;
			end ;
            
             /*********************************    
    #TRANSACCION:  'CBR_CBREP_SEL'
    #DESCRIPCION:   Consulta de datos para los reportes pdf y excel
    #AUTOR:     admin   
    #FECHA:     31-12-2017 12:33:30
    ***********************************/

    elsif(p_transaccion='CBR_CBREP_SEL')then
                    
        begin
    
            --Sentencia de la consulta
            v_consulta:='  
            				
             WITH  tgestion(
                                    id_cobro_simple,                                  
                                    id_gestion,
                                    id_periodo) 
                            
                           as (
                                    select 
                                       cs.id_cobro_simple,
                                        (select 
                                          ges.id_gestion
                                          from param.tgestion ges
                                          where ges.gestion = (date_part(''year'', cs.fecha))::integer
                                          limit 1 offset 0)::integer as id_gestion,
                                          (select
                                          id_periodo
                                          from param.tperiodo
                                          where cs.fecha between fecha_ini and fecha_fin
                                          limit 1 offset 0)::integer as id_periodo                                      
                                    from cbr.tcobro_simple cs 
                                   
                                   
                           )
      select
                        	ROW_NUMBER () OVER (ORDER BY  pagsim.id_cobro_simple )::integer as id,
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
                            tipasi.codigo || '' - '' || tipasi.nombre as desc_tipo_cobro_simple,
                            tipasi.codigo as codigo_tipo_cobro_simple,
                            pagsim.nro_tramite_asociado,
                            pagsim.importe,
                            pagsim.id_obligacion_pago,
                            op.num_tramite as desc_obligacion_pago,
                            pagsim.id_caja,
                            caj.codigo as desc_caja,
                            pagsim.tipo_cambio,
                            pagsim.tipo_cambio_mt,
                            pagsim.tipo_cambio_ma,
                            pagsim.id_config_cambiaria,
                            pagsim.importe_mt,
                            pagsim.importe_mb,
                            pagsim.importe_ma,
                            pagsim.forma_cambio,
                            COALESCE(pagsim.id_int_comprobante,0),
                            cbte.nro_cbte,
                        	gest.id_gestion,
                            gest.id_periodo,
                            csd.importe_mb as importe_cobro_factura,
							dcv.id_doc_compra_venta,
                            dcv.nro_documento,
                            dcv.razon_social
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
                        inner join cbr.ttipo_cobro_simple tipasi on tipasi.id_tipo_cobro_simple = pagsim.id_tipo_cobro_simple
                        left join orga.vfuncionario fun1 on fun1.id_funcionario = pagsim.id_funcionario_pago
                        left join tes.tobligacion_pago op on op.id_obligacion_pago = pagsim.id_obligacion_pago
                        left join tes.tcaja caj on caj.id_caja = pagsim.id_caja
                        left join conta.tint_comprobante cbte on cbte.id_int_comprobante = pagsim.id_int_comprobante
                        inner join tgestion gest on gest.id_cobro_simple = pagsim.id_cobro_simple
                        left join cbr.tcobro_simple_det csd on csd.id_cobro_simple = pagsim.id_cobro_simple
                        left join conta.tdoc_compra_venta dcv on dcv.id_doc_compra_venta = csd.id_doc_compra_venta
                       
                      
                        where  ';

           -- raise exception 'consulta %',v_consulta;
            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

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

ALTER FUNCTION cbr.ft_cobro_re_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;