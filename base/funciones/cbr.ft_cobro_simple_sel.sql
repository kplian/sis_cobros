--------------- SQL ---------------

CREATE OR REPLACE FUNCTION cbr.ft_cobro_simple_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:       Cuenta Documenta
 FUNCION:       cbr.ft_cobro_simple_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'cbr.tcobro_simple'
 AUTOR:          (admin)
 FECHA:         31-12-2017 12:33:30
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE              FECHA               AUTOR               DESCRIPCION
 #0             31-12-2017 12:33:30          rac                     Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'cbr.tcobro_simple'
 #1             21/08/2018              EGS				se modifico las  transacciones CBR_PAGSIM_SEL , CBR_PAGSIM_CONT
 #2				13/09/2018				EGS				se modifico la transaccion CBR_PAGSIM_SEL y se declaro variables
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

      -- #2				13/09/2018				EGS
     v_bandera				 varchar;
     v_bandera_rg 			 varchar;
     v_bandera_ant			 varchar;
    -- #2				13/09/2018				EGS

BEGIN

    v_nombre_funcion = 'cbr.ft_cobro_simple_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
    #TRANSACCION:  'CBR_PAGSIM_SEL'
    #DESCRIPCION:   Consulta de datos
    #AUTOR:     admin
    #FECHA:     31-12-2017 12:33:30
    ***********************************/

    if(p_transaccion='CBR_PAGSIM_SEL')then

        begin
			 -- variables globales para que lleguen a redender de los campos en la vista
             --#2				13/09/2018				EGS
        	 v_bandera = split_part(pxp.f_get_variable_global('v_cobro_comun'), ',', 1);
             v_bandera_rg = split_part(pxp.f_get_variable_global('v_cobro_retencion_garantia'), ',', 1);
             v_bandera_ant = split_part(pxp.f_get_variable_global('v_cobro_anticipo'), ',', 1);
             -- #2				13/09/2018				EGS


            --Filtros
            v_parametros.ordenacion = replace(v_parametros.ordenacion,'id_int_comprobante','pagsim.id_int_comprobante');
            v_filtro='';

            if pxp.f_existe_parametro(p_tabla,'historico') then
                v_historico =  v_parametros.historico;
            else
                v_historico = 'no';
            end if;
            /*
             if p_administrador != 1  then

            	 v_filtro = v_filtro || 'pagsim.id_usuario_reg in (54,218,447,304 )and';

             END if;
			*/
            if v_parametros.tipo_interfaz in ('PagoSimpleSol') then

                if p_administrador != 1  then
                    --Filtro para  visualizacion de usuarios
                    v_filtro = '(
                        (pagsim.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' or pagsim.id_usuario_reg='||p_id_usuario||')
                        or
                        ('||p_id_usuario||' in (select id_usuario from param.tdepto_usuario where id_depto = pagsim.id_depto_conta))
                    ) and ';

                    --Filtro de los estados
                    if v_historico = 'no' then
                        v_filtro = v_filtro || 'pagsim.estado in (''borrador'') and ';
                    end if;

                end if;

            elsif v_parametros.tipo_interfaz in ('PagoSimpleVb') then
                v_filtro = ' pagsim.estado not in (''borrador'',''finalizado'') and ';

                if p_administrador != 1  then
                    --v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' or pagsim.id_usuario_reg='||p_id_usuario||') and ';
                    v_filtro = v_filtro||'(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||') and ';
                end if;

            end if;

             -- agregaron campos para variables globales para que lleguen a redender de los campos en la vista
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
                            pagsim.forma_cambio,
                            COALESCE(pagsim.id_int_comprobante,0),
                            cbte.nro_cbte,
                            '''||v_bandera||'''::varchar as globalComun,
                            '''||v_bandera_rg||'''::varchar as globalRetgar,
                            '''||v_bandera_ant||'''::varchar as globalAnti


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
                        where  ';


            v_consulta = v_consulta || v_filtro;

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

        end;
    /*********************************
    #TRANSACCION:  'CBR_DETPAG_SEL'
    #DESCRIPCION:   Consulta de datos
    #AUTOR:     JUAN
    #FECHA:     07-01-2018 12:33:30
    ***********************************/

    ELSIF(p_transaccion='CBR_DETPAG_SEL')then

        begin


            --Sentencia de la consulta
            v_consulta:='SELECT
                          cv.id_doc_compra_venta::INTEGER,
                          cv.id_funcionario::INTEGER,
                          (select vf.desc_funcionario1 from orga.tfuncionario f join orga.vfuncionario vf on vf.id_funcionario=f.id_funcionario where f.id_funcionario=cv.id_funcionario)::varchar desc_funcionario1,
                          cv.id_plantilla::INTEGER,
                          (select p.desc_plantilla from param.tplantilla p where  p.id_plantilla=cv.id_plantilla)::varchar desc_plantilla,
                          ps.id_proveedor::INTEGER,
                          (SELECT pr.rotulo_comercial from param.tproveedor pr where pr.id_proveedor=ps.id_proveedor)::VARCHAR rotulo_comercial,
                          cv.importe_pago_liquido::numeric,
                          cv.importe_excento::numeric,
                          cv.fecha::date fecha_compra_venta,
                          ps.fecha::date fecha_cobro_simple,
                          cv.sw_pgs::varchar sw_pgs
                          FROM conta.tdoc_compra_venta cv
                          join cbr.tcobro_simple_det psd on psd.id_doc_compra_venta=cv.id_doc_compra_venta
                          join cbr.tcobro_simple ps on ps.id_cobro_simple= psd.id_cobro_simple
                          inner join wf.testado_wf ew on ew.id_estado_wf = ps.id_estado_wf
                        where  ';


            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

        end;
    /*********************************
    #TRANSACCION:  'CBR_DETPAG_CONT'
    #DESCRIPCION:   Conteo de registros
    #AUTOR:     JUAN
    #FECHA:     07-01-2018 12:33:30
    ***********************************/

    elsif(p_transaccion='CBR_DETPAG_CONT')then

        begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='SELECT
                count(cv.id_funcionario)
                FROM conta.tdoc_compra_venta cv
                join cbr.tcobro_simple_det psd on psd.id_doc_compra_venta=cv.id_doc_compra_venta
                join cbr.tcobro_simple ps on ps.id_cobro_simple= psd.id_cobro_simple
                inner join wf.testado_wf ew on ew.id_estado_wf = ps.id_estado_wf
                        where ';

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
    #TRANSACCION:  'CBR_PAGSIM_CONT'
    #DESCRIPCION:   Conteo de registros
    #AUTOR:     admin
    #FECHA:     31-12-2017 12:33:30
    ***********************************/

    elsif(p_transaccion='CBR_PAGSIM_CONT')then

        begin

            v_filtro='';
            if v_parametros.tipo_interfaz in ('PagoSimpleSol') then

                if p_administrador != 1  then
                    --Filtro para  visualizacion de usuarios
                    v_filtro = '(
                        (pagsim.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' or pagsim.id_usuario_reg='||p_id_usuario||')
                        or
                        ('||p_id_usuario||' in (select id_usuario from param.tdepto_usuario where id_depto = pagsim.id_depto_conta))
                    ) and ';

                    --Filtro de los estados
                    if v_historico = 'no' then
                        v_filtro = v_filtro || 'pagsim.estado in (''borrador'') and ';
                    end if;

                end if;

            elsif v_parametros.tipo_interfaz in ('PagoSimpleVb') then
                v_filtro = ' pagsim.estado not in (''borrador'',''finalizado'') and ';

                if p_administrador != 1  then
                    --v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' or pagsim.id_usuario_reg='||p_id_usuario||') and ';
                    v_filtro = v_filtro||'(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||') and ';
                end if;

            end if;


            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count(id_cobro_simple)
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
                        where ';

            v_consulta = v_consulta || v_filtro;

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;
    /*********************************
    #TRANSACCION:  'CBR_DEPASIMPLE_SEL'
    #DESCRIPCION:   Consulta de datos
    #AUTOR:     JUAN
    #FECHA:     20-01-2018 12:33:30
    ***********************************/

    ELSIF(p_transaccion='CBR_DEPASIMPLE_SEL')then

        begin

            --raise exception 'error provocado %',v_parametros.id_cobro_simple;
            --Sentencia de la consulta
            v_consulta:='select
                         id_doc_compra_venta::integer,
                         tipo::Varchar,
                         fecha::date,
                         nit::varchar,
                         razon_social::Varchar,
                         COALESCE(nro_documento::varchar, ''0'')::Varchar as nro_documento,
                         COALESCE(nro_dui::varchar, ''0'')::Varchar as nro_dui,
                         nro_autorizacion::Varchar,
                         importe_doc::numeric,
                         total_excento::numeric,
                         sujeto_cf::numeric,
                         importe_descuento::numeric,
                         subtotal::numeric,
                         credito_fiscal::numeric,
                         importe_iva::numeric,
                         codigo_control::varchar,
                         --tipo_doc::varchar,
                         id_plantilla::integer,
                         id_moneda::integer,
                         codigo_moneda::Varchar,
                         id_periodo::integer,
                         id_gestion::integer,
                         periodo::integer,
                         gestion::integer,
                         venta_gravada_cero::numeric,
                         subtotal_venta::numeric,
                         sujeto_df::numeric,
                         importe_ice::numeric,
                         importe_excento::numeric

                         from conta.vldet_doc_pag_simple
                         where  id_cobro_simple='||v_parametros.id_cobro_simple||'  ';

        RAISE NOTICE 'ver consulta juan %',v_consulta;
            --Definicion de la respuesta
            --v_consulta:=v_consulta||v_parametros.filtro;
            --v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --raise exception 'error provocado %',v_consulta;
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