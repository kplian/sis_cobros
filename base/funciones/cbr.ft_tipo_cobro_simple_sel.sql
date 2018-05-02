--------------- SQL ---------------

CREATE OR REPLACE FUNCTION cbr.ft_tipo_cobro_simple_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Cuenta Documenta
 FUNCION: 		cbr.ft_tipo_cobro_simple_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'cd.ttipo_pago_simple'
 AUTOR: 		 (admin)
 FECHA:	        02-12-2017 02:49:10
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				02-12-2017 02:49:10								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'cd.ttipo_pago_simple'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'cbr.ft_tipo_cobro_simple_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CBR_TIPASI_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		02-12-2017 02:49:10
	***********************************/

	if(p_transaccion='CBR_TIPASI_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
                          tipasi.id_tipo_cobro_simple,
                          tipasi.estado_reg,
                          tipasi.codigo,
                          tipasi.nombre,
                          tipasi.plantilla_cbte,
                          tipasi.plantilla_cbte_1,
                          tipasi.id_usuario_reg,
                          tipasi.fecha_reg,
                          tipasi.id_usuario_ai,
                          tipasi.usuario_ai,
                          tipasi.id_usuario_mod,
                          tipasi.fecha_mod,
                          usu1.cuenta as usr_reg,
                          usu2.cuenta as usr_mod,
                          tipasi.flujo_wf
						from cbr.ttipo_cobro_simple tipasi
						inner join segu.tusuario usu1 on usu1.id_usuario = tipasi.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tipasi.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'CBR_TIPASI_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		02-12-2017 02:49:10
	***********************************/

	elsif(p_transaccion='CBR_TIPASI_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_tipo_cobro_simple)
					    from cbr.ttipo_cobro_simple tipasi
					    inner join segu.tusuario usu1 on usu1.id_usuario = tipasi.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tipasi.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

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