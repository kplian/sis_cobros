--------------- SQL ---------------

CREATE OR REPLACE FUNCTION cbr.ft_cobro_simple_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Cuenta Documenta
 FUNCION: 		cbr.ft_cobro_simple_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'cbr.tcobro_simple'
 AUTOR: 		 (admin)
 FECHA:	        31-12-2017 12:33:30
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				31-12-2017 12:33:30								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'cbr.tcobro_simple'	
 #1				21/08/2018				EGS					se aumento campos en la sentencia de insercion y modificacion en las transacciones CBR_PAGSIM_INS,CBR_PAGSIM_MOD
 
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_cobro_simple		integer;
	v_id_proceso_macro 		integer;
	v_codigo_proceso_macro 	varchar;
	v_codigo_tipo_proceso 	varchar;
	v_id_gestion 			integer;
	v_num_tramite         	varchar;
    v_id_proceso_wf         integer;
    v_id_estado_wf          integer;
    v_codigo_estado         varchar;
    v_id_tipo_estado		integer;
    v_id_depto 				integer;
    v_obs 					varchar;
    v_acceso_directo        varchar;
    v_clase             	varchar;
    v_parametros_ad         varchar;
    v_tipo_noti           	varchar;
    v_titulo              	varchar;
    v_id_estado_actual		integer;
    v_registros_proc		record;
    v_codigo_tipo_pro 		varchar;
    v_codigo_estado_siguiente varchar;
    v_operacion 			varchar;
    v_id_funcionario		integer;
    v_id_usuario_reg 		integer;
    v_id_estado_wf_ant		integer;
    v_where					varchar;
    v_sql					varchar;
    v_id_cuenta_bancaria	integer;
    v_id_depto_lb			integer;
    v_id_moneda				integer;
    v_id_depto_conta		integer;
    v_permitir				boolean;
    v_fac_imp				integer;
    v_fac_imp_ant			integer;
    v_obligacion_pago		record;
    v_cobro_simple			record;
    v_registros_doc	        record;
    v_codigo_tipo_cobro_simple varchar;
    v_monto_mb                 numeric;
    v_monto_mt                 numeric;
    v_monto_ma                 numeric;
    v_id_moneda_base		   integer;
    v_id_moneda_tri		       integer;
    v_id_moneda_act            integer;
    va_montos        	       numeric[];
    v_reg_cbte		           record;
			    
BEGIN

    v_nombre_funcion = 'cbr.ft_cobro_simple_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CBR_PAGSIM_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		31-12-2017 12:33:30
	***********************************/

	if(p_transaccion='CBR_PAGSIM_INS')then
					
        begin
        
            v_id_moneda_base = param.f_get_moneda_base();
            v_id_moneda_tri = param.f_get_moneda_triangulacion();
            v_id_moneda_act = param.f_get_moneda_intercambio();

        	--Obtienen el codigo macro
        	select tps.flujo_wf
        	into v_codigo_proceso_macro
        	from cbr.ttipo_cobro_simple tps
        	where tps.id_tipo_cobro_simple = v_parametros.id_tipo_cobro_simple;

        	--Obtener id del proceso macro
            select
            pm.id_proceso_macro
            into
            v_id_proceso_macro
            from wf.tproceso_macro pm
            where pm.codigo = v_codigo_proceso_macro;

            if v_id_proceso_macro is NULL THEN
                raise exception 'El proceso macro de codigo % no esta configurado en el sistema WF',v_codigo_proceso_macro;
            END IF;

            --Obtener el codigo del tipo_proceso
            select tp.codigo
            into v_codigo_tipo_proceso
            from wf.ttipo_proceso tp
            where tp.id_proceso_macro = v_id_proceso_macro
            and tp.estado_reg = 'activo' and tp.inicio = 'si';

            if v_codigo_tipo_proceso is NULL THEN
                raise exception 'No existe un proceso inicial para el proceso macro indicado % (Revise la configuración)',v_codigo_proceso_macro;
            end if;
            
            --jrr: si se genera a partir de una obligacion de pago 
		if (v_parametros.id_int_comprobante is not null) then
            	--obtener datos op
            	select * into v_reg_cbte
                from conta.tint_comprobante cbte
                where cbte.id_int_comprobante = v_parametros.id_int_comprobante;
                
                 -- disparar creacion de procesos seleccionados
                      
                SELECT
                         ps_id_proceso_wf,
                         ps_id_estado_wf,
                         ps_codigo_estado
                   into
                         v_id_proceso_wf,
                         v_id_estado_wf,
                         v_codigo_estado
                FROM wf.f_registra_proceso_disparado_wf(
                         p_id_usuario,
                         v_parametros._id_usuario_ai,
                         v_parametros._nombre_usuario_ai,
                         v_reg_cbte.id_estado_wf, 
                         v_parametros.id_funcionario, 
                         v_parametros.id_depto_conta,
                         'Solicitud de Cobro Simple',
                         v_codigo_tipo_proceso,    
                         v_codigo_tipo_proceso);
                --el num tramite es el mismo
                v_num_tramite =  v_reg_cbte.nro_tramite;
            else
                ---------------------------
                --Inicio del tramite de WF
                ---------------------------              

                --Obtencion de la gestion
                select
                per.id_gestion
                into
                v_id_gestion
                from param.tperiodo per
                where per.fecha_ini <= v_parametros.fecha and per.fecha_fin >= v_parametros.fecha
                limit 1 offset 0;


                --Inicio del tramite en el sistema de WF
                select
                   ps_num_tramite ,
                   ps_id_proceso_wf ,
                   ps_id_estado_wf ,
                   ps_codigo_estado
                into
                   v_num_tramite,
                   v_id_proceso_wf,
                   v_id_estado_wf,
                   v_codigo_estado
                from wf.f_inicia_tramite(
                   p_id_usuario,
                   v_parametros._id_usuario_ai,
                   v_parametros._nombre_usuario_ai,
                   v_id_gestion,
                   v_codigo_tipo_proceso,
                   v_parametros.id_funcionario,
                   v_parametros.id_depto_conta,
                   'Solicitud de Cobro simple',
                   '' );
            end if;		
            
            -- CALCULAR MONEDA BASE PARA LA FECHA
            
             --validacion de tipos de cambios
            IF v_parametros.tipo_cambio is NULL or  v_parametros.tipo_cambio_mt is NULL or  v_parametros.tipo_cambio_ma is NULL THEN
              raise exception 'no se definieron los tipos de cambio';
            END IF;
            
            IF  v_parametros.id_config_cambiaria is NULL  THEN
              raise exception 'la configuracion cambiara no puede ser nula';
            END IF;
            
            v_id_moneda_base = param.f_get_moneda_base();
            v_id_moneda_tri = param.f_get_moneda_triangulacion();
            v_id_moneda_act = param.f_get_moneda_intercambio();
            
             --------------------------------------------------------------------------
              --calcula los valor  en moneda base y triangulacion segun configuracion
              ------------------------------------------------------------------------
              va_montos  = conta.f_calcular_monedas_segun_config(v_parametros.id_moneda, v_id_moneda_base, v_id_moneda_tri, v_id_moneda_act, v_parametros.tipo_cambio, v_parametros.tipo_cambio_mt,v_parametros.tipo_cambio_ma, v_parametros.importe, v_parametros.id_config_cambiaria, v_parametros.fecha);
              v_monto_mb = va_montos[1];
              v_monto_mt = va_montos[2];
              v_monto_ma = va_montos[3];
            
            
            	

        	--Sentencia de la insercion
        	insert into cbr.tcobro_simple(
				estado_reg,
				id_depto_conta,
				nro_tramite,
				fecha,
				id_funcionario,
				estado,
				id_estado_wf,
				id_proceso_wf,
				obs,
				id_cuenta_bancaria,
				id_depto_lb,
				id_usuario_reg,
				fecha_reg,
				id_usuario_ai,
				usuario_ai,
				id_usuario_mod,
				fecha_mod,
				id_proveedor,
				id_moneda,
				id_tipo_cobro_simple,
				id_funcionario_pago,			
				importe,
				id_obligacion_pago,
				id_caja,
                tipo_cambio,
                tipo_cambio_mt,
                tipo_cambio_ma,
                id_config_cambiaria,
                importe_mt,
                importe_mb,
                importe_ma,
                forma_cambio,
                id_int_comprobante    --EGS--1A
          	) values(
				'activo',
				v_parametros.id_depto_conta,
				v_num_tramite,
				v_parametros.fecha,
				v_parametros.id_funcionario,
				v_codigo_estado,
				v_id_estado_wf,
				v_id_proceso_wf,
				v_parametros.obs,
				v_parametros.id_cuenta_bancaria,
				v_parametros.id_depto_lb,
				p_id_usuario,
				now(),
				v_parametros._id_usuario_ai,
				v_parametros._nombre_usuario_ai,
				null,
				null,
				v_parametros.id_proveedor,
				v_parametros.id_moneda,
				v_parametros.id_tipo_cobro_simple,
				v_parametros.id_funcionario_pago,			
				v_parametros.importe,
				v_parametros.id_obligacion_pago,
				v_parametros.id_caja,
                v_parametros.tipo_cambio,
                v_parametros.tipo_cambio_mt,
                v_parametros.tipo_cambio_ma,
                v_parametros.id_config_cambiaria,
                v_monto_mt,
                v_monto_mb,
                v_monto_ma,
                v_parametros.forma_cambio,
                v_parametros.id_int_comprobante  --EGS--1A
			) RETURNING id_cobro_simple into v_id_cobro_simple;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Pago Simple almacenado(a) con exito (id_cobro_simple'||v_id_cobro_simple||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_cobro_simple',v_id_cobro_simple::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'CBR_PAGSIM_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		31-12-2017 12:33:30
	***********************************/

	elsif(p_transaccion='CBR_PAGSIM_MOD')then

		begin
        
        	select * into v_cobro_simple
            from cbr.tcobro_simple
            where id_cobro_simple = v_parametros.id_cobro_simple;
            
            if (v_cobro_simple.estado != 'borrador') then
            	raise exception 'No se puede modificar un pago que no esta en estado borrador. Envie el pago a estado borrador para poder modificarlo';
            end if;

            if v_parametros.id_tipo_cobro_simple <> v_cobro_simple.id_tipo_cobro_simple then
            	raise exception 'No es posible cambiar el Tipo de Pago';
            end if;
            
            
            --NO peude cambiar la moenda 
            
            
            --TODO CALCULAR MONEDA BASE PARA LA FECHA
            --------------------------------------------------------------------------
            --calcula los valor  en moneda base y triangulacion segun configuracion
            ------------------------------------------------------------------------
              va_montos  = conta.f_calcular_monedas_segun_config(v_parametros.id_moneda, v_id_moneda_base, v_id_moneda_tri, v_id_moneda_act, v_parametros.tipo_cambio, v_parametros.tipo_cambio_mt,v_parametros.tipo_cambio_ma, v_parametros.importe, v_parametros.id_config_cambiaria, v_parametros.fecha);
              v_monto_mb = va_montos[1];
              v_monto_mt = va_montos[2];
              v_monto_ma = va_montos[3];
              
             --verificar si peude cambiar el monto
             select 
                 c.importe,
                 c.id_moneda,
                 c.fecha,
                 c.tipo_cambio,
                 c.tipo_cambio_ma,
                 c.tipo_cambio_mt
               into
                 v_registros_doc
             from  cbr.tcobro_simple c 
             where c.id_cobro_simple = v_parametros.id_cobro_simple;
              
            
            IF  v_registros_doc.id_moneda !=  v_parametros.id_moneda  or 
                v_registros_doc.importe  != v_parametros.importe  or
                v_registros_doc.fecha  != v_parametros.fecha  or
                v_registros_doc.tipo_cambio  != v_parametros.tipo_cambio  or
                v_registros_doc.tipo_cambio_ma  != v_parametros.tipo_cambio_ma  or
                v_registros_doc.tipo_cambio_mt  != v_parametros.tipo_cambio_mt  THEN
            
                  --si alguno de estos datos varia vemos que no se tenga facturas registradas en el detalle
                  
                 IF EXISTS(select 
                               1
                            from cbr.tcobro_simple_det csd
                            where     csd.id_cobro_simple = v_parametros.id_cobro_simple
                                  and csd.estado_reg = 'activo') THEN
                         
                            raise exception 'Para hacer esta moficiacón primero elimine las facturas del detalle';       
                          
                  END IF;
            
            END IF;
            
          
            
			
			--Sentencia de la modificacion
			update cbr.tcobro_simple set
                id_depto_conta = v_parametros.id_depto_conta,
                fecha = v_parametros.fecha,
                id_funcionario = v_parametros.id_funcionario,
                obs = v_parametros.obs,
                id_cuenta_bancaria = v_parametros.id_cuenta_bancaria,
                id_depto_lb = v_parametros.id_depto_lb,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai,
                id_moneda = v_parametros.id_moneda,
                id_proveedor = v_parametros.id_proveedor,
                id_tipo_cobro_simple = v_parametros.id_tipo_cobro_simple,
                id_funcionario_pago = v_parametros.id_funcionario_pago,			
                importe = v_parametros.importe,
                id_obligacion_pago = v_parametros.id_obligacion_pago,
                id_caja = v_parametros.id_caja,
                tipo_cambio = v_parametros.tipo_cambio,
                tipo_cambio_mt = v_parametros.tipo_cambio_mt,
                tipo_cambio_ma = v_parametros.tipo_cambio_ma,
                id_config_cambiaria = v_parametros.id_config_cambiaria,
                importe_mt = v_monto_mt,
                importe_mb = v_monto_mb,
                importe_ma = v_monto_ma,
                forma_cambio = v_parametros.forma_cambio,
                id_int_comprobante=v_parametros.id_int_comprobante   --EGS--1A
			where id_cobro_simple=v_parametros.id_cobro_simple;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Cobro  Simple modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_cobro_simple',v_parametros.id_cobro_simple::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'CBR_PAGSIM_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		31-12-2017 12:33:30
	***********************************/

	elsif(p_transaccion='CBR_PAGSIM_ELI')then

		begin
        	select * into v_cobro_simple
            from cbr.tcobro_simple
            where id_cobro_simple = v_parametros.id_cobro_simple;
            
            if (v_cobro_simple.estado != 'borrador') then
            	raise exception 'No se puede eliminar un pago que no esta en estado borrador. Envie el pago a estado borrador para poder eliminarlo';
            end if;
            
            --TODO  no peude eliminar un pago si tiene todumentos asociados primero pedir que elimine las asociaciones
            
             IF EXISTS(select 
                               1
                            from cbr.tcobro_simple_det csd
                            where     csd.id_cobro_simple = v_parametros.id_cobro_simple
                                  and csd.estado_reg = 'activo') THEN
                         
                            raise exception 'Para eliminar  primero elimine las facturas del detalle';       
                          
            END IF;
            
			--Sentencia de la eliminacion
			delete from cbr.tcobro_simple
            where id_cobro_simple=v_parametros.id_cobro_simple;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Pago Simple eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_cobro_simple',v_parametros.id_cobro_simple::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
	#TRANSACCION:  	'CBR_SIGEPS_INS'
	#DESCRIPCION:  	Controla el cambio al siguiente estado
	#AUTOR:   		RCM
	#FECHA:   		05/01/2018
	***********************************/

  	elseif(p_transaccion='CBR_SIGEPS_INS')then
        
        begin

	        /*   PARAMETROS

	        $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
	        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
	        $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
	        $this->setParametro('id_depto_wf','id_depto_wf','int4');
	        $this->setParametro('obs','obs','text');
	        $this->setParametro('json_procesos','json_procesos','text');
	        */

	        --Obtenemos datos basicos
			select
			c.id_proceso_wf,
			c.id_estado_wf,
			c.estado,
			tps.codigo
			into
			v_id_proceso_wf,
			v_id_estado_wf,
			v_codigo_estado,
			v_codigo_tipo_cobro_simple
			from cbr.tcobro_simple c
			inner join cbr.ttipo_cobro_simple tps on tps.id_tipo_cobro_simple = c.id_tipo_cobro_simple
			where c.id_cobro_simple = v_parametros.id_cobro_simple;

	        --Recupera datos del estado
			select
			ew.id_tipo_estado,
			te.codigo
			into
			v_id_tipo_estado,
			v_codigo_estado
			from wf.testado_wf ew
			inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
			where ew.id_estado_wf = v_parametros.id_estado_wf_act;


			-- obtener datos tipo estado
			select
			te.codigo
			into
			v_codigo_estado_siguiente
			from wf.ttipo_estado te
			where te.id_tipo_estado = v_parametros.id_tipo_estado;

			if pxp.f_existe_parametro(p_tabla,'id_depto_wf') then
				v_id_depto = v_parametros.id_depto_wf;
			end if;

			if pxp.f_existe_parametro(p_tabla,'obs') then
				v_obs=v_parametros.obs;
			else
				v_obs='---';
			end if;

			--Acciones por estado siguiente que podrian realizarse
			if v_codigo_estado_siguiente in ('') then

			end if;

			---------------------------------------
			-- REGISTRA EL SIGUIENTE ESTADO DEL WF
			---------------------------------------
			--Configurar acceso directo para la alarma
			v_acceso_directo = '';
			v_clase = '';
			v_parametros_ad = '';
			v_tipo_noti = 'notificacion';
			v_titulo  = 'Visto Bueno';

			if v_codigo_estado_siguiente not in('borrador','finalizado','anulado') then

				v_acceso_directo = '../../../sis_cuenta_documentada/vista/pago_simle/PagoSimple.php';
				v_clase = 'PagoSimple';
				v_parametros_ad = '{filtro_directo:{campo:"cbr.id_proceso_wf",valor:"'||v_id_proceso_wf::varchar||'"}}';
				v_tipo_noti = 'notificacion';
				v_titulo  = 'Visto Bueno';

			end if;

			v_id_estado_actual = wf.f_registra_estado_wf(v_parametros.id_tipo_estado,
			                                       v_parametros.id_funcionario_wf,
			                                       v_parametros.id_estado_wf_act,
			                                       v_id_proceso_wf,
			                                       p_id_usuario,
			                                       v_parametros._id_usuario_ai,
			                                       v_parametros._nombre_usuario_ai,
			                                       v_id_depto,                       --depto del estado anterior
			                                       v_obs,
			                                       v_acceso_directo,
			                                       v_clase,
			                                       v_parametros_ad,
			                                       v_tipo_noti,
			                                       v_titulo);

			--------------------------------------
			-- Registra los procesos disparados
			--------------------------------------
			for v_registros_proc in ( select * from json_populate_recordset(null::wf.proceso_disparado_wf, v_parametros.json_procesos::json)) loop

				--Obtencion del codigo tipo proceso
				select
				tp.codigo
				into
				v_codigo_tipo_pro
				from wf.ttipo_proceso tp
				where tp.id_tipo_proceso =  v_registros_proc.id_tipo_proceso_pro;

				--Disparar creacion de procesos seleccionados
				select
				ps_id_proceso_wf,
				ps_id_estado_wf,
				ps_codigo_estado
				into
				v_id_proceso_wf,
				v_id_estado_wf,
				v_codigo_estado
				from wf.f_registra_proceso_disparado_wf(
				p_id_usuario,
				v_parametros._id_usuario_ai,
				v_parametros._nombre_usuario_ai,
				v_id_estado_actual,
				v_registros_proc.id_funcionario_wf_pro,
				v_registros_proc.id_depto_wf_pro,
				v_registros_proc.obs_pro,
				v_codigo_tipo_pro,
				v_codigo_tipo_pro);

			end loop;

			--------------------------------------------------
			--  ACTUALIZA EL NUEVO ESTADO DE LA CUENTA DOCUMENTADA
			----------------------------------------------------
			IF pxp.f_existe_parametro(p_tabla,'id_cuenta_bancaria') THEN
                v_id_cuenta_bancaria =  v_parametros.id_cuenta_bancaria;
            END IF;

            IF pxp.f_existe_parametro(p_tabla,'id_depto_lb') THEN
                v_id_depto_lb =  v_parametros.id_depto_lb;
            END IF;

			if v_codigo_tipo_cobro_simple = 'PAG_CAJ' then

				if cbr.f_fun_inicio_cobro_simple_caja_wf(p_id_usuario,
						v_parametros._id_usuario_ai,
						v_parametros._nombre_usuario_ai,
						v_id_estado_actual,
						v_id_proceso_wf,
						v_codigo_estado_siguiente,
						v_id_depto_lb,
		                v_id_cuenta_bancaria,
		                v_codigo_estado
					) then

				end if;

			else

				if cbr.f_fun_inicio_cobro_simple_wf(p_id_usuario,
						v_parametros._id_usuario_ai,
						v_parametros._nombre_usuario_ai,
						v_id_estado_actual,
						v_id_proceso_wf,
						v_codigo_estado_siguiente,
						v_id_depto_lb,
		                v_id_cuenta_bancaria,
		                v_codigo_estado
					) then

				end if;

			end if;
			


			-- si hay mas de un estado disponible  preguntamos al usuario
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del pago simple id='||v_parametros.id_cobro_simple);
			v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


			-- Devuelve la respuesta
			return v_resp;

     	end;

	/*********************************
	#TRANSACCION:  	'CBR_ANTEPS_IME'
	#DESCRIPCION: 	Retrocede el estado del pago simple
	#AUTOR:   		RCM
	#FECHA:   		06/01/2018
	***********************************/

  	elseif(p_transaccion='CBR_ANTEPS_IME')then

        begin

			--Obtenemos datos basicos
			select
			  c.id_cobro_simple,
			  c.id_proceso_wf,
			  c.estado,
			  pwf.id_tipo_proceso
			into
			v_registros_proc
			from cbr.tcobro_simple c
			inner join wf.tproceso_wf pwf on  pwf.id_proceso_wf = c.id_proceso_wf
			where c.id_proceso_wf = v_parametros.id_proceso_wf;


        	v_id_proceso_wf = v_registros_proc.id_proceso_wf;

            --------------------------------------------------
            --Retrocede al estado inmediatamente anterior
            -------------------------------------------------
           	--recupera estado anterior segun Log del WF
			select
			  ps_id_tipo_estado,
			  ps_id_funcionario,
			  ps_id_usuario_reg,
			  ps_id_depto,
			  ps_codigo_estado,
			  ps_id_estado_wf_ant
			into
			  v_id_tipo_estado,
			  v_id_funcionario,
			  v_id_usuario_reg,
			  v_id_depto,
			  v_codigo_estado,
			  v_id_estado_wf_ant
			from wf.f_obtener_estado_ant_log_wf(v_parametros.id_estado_wf);

			--Configurar acceso directo para la alarma
			v_acceso_directo = '';
			v_clase = '';
			v_parametros_ad = '';
			v_tipo_noti = 'notificacion';
			v_titulo  = 'Visto Bueno';

			if v_codigo_estado_siguiente not in('borrador','finalizado','anulado') then
				v_acceso_directo = '../../../sis_cobros/vista/cobro_simple/CobroSimple.php';
				v_clase = 'CobroSimple';
				v_parametros_ad = '{filtro_directo:{campo:"cbr.id_proceso_wf",valor:"'||v_id_proceso_wf::varchar||'"}}';
				v_tipo_noti = 'notificacion';
				v_titulo  = 'Visto Bueno';
			end if;


          	--Registra nuevo estado
			v_id_estado_actual = wf.f_registra_estado_wf(
			    v_id_tipo_estado,                --  id_tipo_estado al que retrocede
			    v_id_funcionario,                --  funcionario del estado anterior
			    v_parametros.id_estado_wf,       --  estado actual ...
			    v_id_proceso_wf,                 --  id del proceso actual
			    p_id_usuario,                    -- usuario que registra
			    v_parametros._id_usuario_ai,
			    v_parametros._nombre_usuario_ai,
			    v_id_depto,                       --depto del estado anterior
			    '[RETROCESO] '|| v_parametros.obs,
			    v_acceso_directo,
			    v_clase,
			    v_parametros_ad,
			    v_tipo_noti,
			    v_titulo);

			if not cbr.f_fun_regreso_cobro_simple_wf(p_id_usuario,
												v_parametros._id_usuario_ai,
												v_parametros._nombre_usuario_ai,
												v_id_estado_actual,
												v_parametros.id_proceso_wf,
												v_codigo_estado) then

				raise exception 'Error al retroceder estado';

			end if;

			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del pago simple)');
			v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');

          	--Devuelve la respuesta
            return v_resp;

        end;

    /*********************************
	#TRANSACCION:  	'CBR_PSAGRDOC_IME'
	#DESCRIPCION: 	Agrega documentos para el pago simple
	#AUTOR:   		RCM
	#FECHA:   		06/01/2018
	***********************************/

  	elseif(p_transaccion='CBR_PSAGRDOC_IME')then

        begin
        
        	select * into v_cobro_simple
            from cbr.tcobro_simple
            where id_cobro_simple = v_parametros.id_cobro_simple;
            
            if (v_cobro_simple.estado not in ( 'borrador', 'rendicion','vbconta')) then
            	raise exception 'No se puede agregar documentos por|que no esta en estado Borrador, Rendicion o Vbconta. Envie el pago a dichos estados para poder modificarlo';
            end if;

        	--Obtenemos datos basicos
			select
			c.id_cobro_simple,
			c.id_tipo_cobro_simple,
			c.estado,
			tps.codigo as codigo_tipo_cobro_simple,
			c.id_depto_conta,
			c.id_moneda
			into
			v_registros_proc
			from cbr.tcobro_simple c
			inner join cbr.ttipo_cobro_simple tps on  tps.id_tipo_cobro_simple = c.id_tipo_cobro_simple
			where c.id_cobro_simple = v_parametros.id_cobro_simple;


        	--Validación para permitir o no la importación de facturas
        	v_permitir=true;
        	/*v_permitir = false;
        	if v_registros_proc.estado = 'borrador' and v_registros_proc.codigo_tipo_cobro_simple NOT IN ('PAG_DEV','ADU_GEST_ANT','DVPGPR','SOLO_DEV') then
        		--Obliga la importación de facturas
        		v_permitir = true;
        	elsif v_registros_proc.estado = 'rendicion' and v_registros_proc.codigo_tipo_cobro_simple IN ('PAG_DEV','ADU_GEST_ANT','DVPGPR','SOLO_DEV') then
        		--Obliga la importación de facturas
        		v_permitir = true;
        	end if;*/

			if not v_permitir then
				raise exception 'No está permitido agregar documentos en este estado';
			end if;

        	if coalesce(v_parametros.id_cobro_simple,0) = 0 then
        		raise exception 'Pago no identificado';
        	end if;

        	--Obtencion de datos del pago
        	select
        	id_moneda,id_depto_conta
        	into
        	v_id_moneda,v_id_depto_conta
        	from cbr.tcobro_simple
        	where id_cobro_simple = v_parametros.id_cobro_simple;

        	--Definicion del filtro a aplicar
        	if v_parametros.id_usuario = 0 then

        		if v_parametros.id_plantilla <> 0 then
        			v_where = 'dcv.id_plantilla = '||v_parametros.id_plantilla;
        		else
        			v_where = ' 0=0';
        		end if;
        		
        	else
        		v_where = 'dcv.id_usuario_reg = '||v_parametros.id_usuario;

        		if v_parametros.id_plantilla <> 0 then
        			v_where = v_where||' and dcv.id_plantilla = '||v_parametros.id_plantilla;
        		end if;

        	end if;

        	--Cuenta las facturas antes de la importación para luego poder determinar solamente las últimas que se agregaron
        	select coalesce(count(1),0) into v_fac_imp_ant
		    from cbr.tcobro_simple_det
		    where id_cobro_simple = v_parametros.id_cobro_simple;

        	--Consulta para la importación de las facturas
        	v_sql = 'insert into cbr.tcobro_simple_det(
		        	id_usuario_reg,fecha_reg,estado_reg,id_cobro_simple,id_doc_compra_venta
		        	)
		        	select
		        	'||p_id_usuario||',now(),''activo'','||v_parametros.id_cobro_simple||',dcv.id_doc_compra_venta
		        	from conta.tdoc_compra_venta dcv
		        	where dcv.sw_pgs = ''reg''
		        	and id_doc_compra_venta not in (select id_doc_compra_venta
		        									from cbr.tcobro_simple_det)
		        	and id_depto_conta = '||v_id_depto_conta||'
		        	and id_moneda = '||v_id_moneda||'
		        	and ';
		    v_sql = v_sql || v_where;

		    execute(v_sql);

		    update conta.tdoc_compra_venta set
		    sw_pgs = 'proc'
		    where id_doc_compra_venta in (select id_doc_compra_venta
	    								from cbr.tcobro_simple_det
	    								where id_cobro_simple = v_parametros.id_cobro_simple);

		    --Cuenta la cantidad de facturas importadas
		    select coalesce(count(1),0) into v_fac_imp
		    from cbr.tcobro_simple_det
		    where id_cobro_simple = v_parametros.id_cobro_simple;

		    --Se actualiza el campo importe de la cabecera
            if v_registros_proc.codigo_tipo_cobro_simple NOT IN ('PAG_DEV','ADU_GEST_ANT') then
                update cbr.tcobro_simple set
                importe = (
                       SELECT f_get_saldo_totales_cobro_simple.o_liquido_pagado
                       FROM cbr.f_get_saldo_totales_cobro_simple(v_parametros.id_cobro_simple)
                         f_get_saldo_totales_cobro_simple(p_monto, o_total_documentos,
                         o_liquido_pagado)
                     )
                where id_cobro_simple = v_parametros.id_cobro_simple;
            end if;

		    v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Facturas/Documentos agregadas');
		    v_resp = pxp.f_agrega_clave(v_resp,'tot_fact',(v_fac_imp - v_fac_imp_ant)::varchar);

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
