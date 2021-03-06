<?php

class AutoPagosController extends ControladorBase{

	public function __construct() {
		parent::__construct();
	}



	public function index(){
	
		session_start();
	
	
		if (isset(  $_SESSION['usuario_usuarios']) )
		{
			
			//creacion ddl de secretarios o abogadpos
			$resultAsignacion=array(0=>'--Seleccione--',1=>'Secretario',2=>'Abg Impulsor');
	
			$permisos_rol = new PermisosRolesModel();
			
			$nombre_controladores = "AutoPagos";
			$id_rol= $_SESSION['id_rol'];
			
			$ciudad = new CiudadModel();
			$resultCiu = $ciudad->getAll("nombre_ciudad");
			
			$usuarios=new UsuariosModel();
			$resultUsu = $usuarios->getAll("nombre_usuarios");
			
			$estado=new EstadoModel();
			$resultEstado=$estado->getBy("nombre_estado='PENDIENTE'");
			
			$asignacion_titulo_credito = new AsignacionTitulosCreditoModel();
			
			
			$columnas = " clientes.id_clientes, 
						  titulo_credito.id_titulo_credito, 
						  clientes.identificacion_clientes, 
						  clientes.nombres_clientes, 
						  clientes.celular_clientes, 
						  titulo_credito.total, 
						  titulo_credito.fecha_corte, 
						  titulo_credito.id_ciudad, 
						  ciudad.nombre_ciudad";
			
			$tablas   = "public.titulo_credito, 
						  public.clientes, 
						  public.ciudad";
			
			$where    = "clientes.id_clientes = titulo_credito.id_clientes AND
                         ciudad.id_ciudad = titulo_credito.id_ciudad";
			
			$id       = "titulo_credito.id_titulo_credito";
				
			
			$resultDatos=$asignacion_titulo_credito->getCondiciones($columnas ,$tablas ,$where, $id);
			
			
			$resultPer = $permisos_rol->getPermisosVer("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
				
			if (!empty($resultPer))
			{
					
				
					//CONSULTA DE USUARIOS POR SU ROL 
					$columnas = "usuarios.id_usuarios,usuarios.nombre_usuarios";
					$tablas="usuarios inner join rol on(usuarios.id_rol=rol.id_rol)";
					$id="rol.id_rol";
					
					$usuarios=new UsuariosModel();
					
					$where="rol.nombre_rol='CIUDAD'";
					$resultCiudad=$ciudad->getCondiciones($columnas ,$tablas , $where, $id);
					
					$where="rol.nombre_rol='SECRETARIO'";
					$resultUsuarioSecretario=$usuarios->getCondiciones($columnas ,$tablas , $where, $id);
					
					$where="rol.nombre_rol='ABOGADO IMPULSOR'";
					$resultUsuarioImpulsor=$usuarios->getCondiciones($columnas ,$tablas , $where, $id);
					
					
					//roles
					$rol = new RolesModel();
					$resultRol=$rol->getAll("nombre_rol");
					
					$controladores=new ControladoresModel();
					$resultCon=$controladores->getAll("nombre_controladores");
			
			
					
					$resultEdit = "";
					$resul = "";
			
					if (isset ($_GET["id_asignacion_secretarios"])   )
					{
						$nombre_controladores = "AutoPagos";
						$id_rol= $_SESSION['id_rol'];
						$resultPer = $permisos_rol->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
						
						if (!empty($resultPer))
						{
							
							
							$resultEdit=$asignacionSecretario->getCondiciones($columnas, $tablas, $where, $id);
							
							
							$traza=new TrazasModel();
							$_nombre_controlador = "AutoPagos";
							$_accion_trazas  = "Editar";
							$_parametros_trazas = $_id_asignacion_secretarios;
							$resultado = $traza->AuditoriaControladores($_accion_trazas, $_parametros_trazas, $_nombre_controlador);
							
						}
						else
						{
							$this->view("Error",array(
									"resultado"=>"No tiene Permisos de Editar Asignacion Secretarios"
						
									
							));
						
							exit();
						}
						
						
						
					}
					
					if(isset($_POST["buscar"])){
					
						$criterio_busqueda=$_POST["criterio_busqueda"];
						$contenido_busqueda=$_POST["contenido_busqueda"];
					
						$asignacion_titulo_credito = new AsignacionTitulosCreditoModel();
							
							
						$columnas = " clientes.id_clientes,
							  titulo_credito.id_titulo_credito,
							  clientes.identificacion_clientes,
							  clientes.nombres_clientes,
							  clientes.celular_clientes,
							  titulo_credito.total,
							  titulo_credito.fecha_corte,
							  titulo_credito.id_ciudad,
							  ciudad.nombre_ciudad";
					
						$tablas   = "public.titulo_credito,
							  public.clientes,
							  public.ciudad";
					
						$where    = "clientes.id_clientes = titulo_credito.id_clientes AND
	                         ciudad.id_ciudad = titulo_credito.id_ciudad";
					
						$id       = "titulo_credito.id_titulo_credito";
							
					
					
						$where_0 = "";
						$where_1 = "";
						$where_2 = "";
					
						switch ($criterio_busqueda) {
							case 0:
								$where_0 = " AND  clientes.identificacion_clientes LIKE '$contenido_busqueda'  ";
								break;
							case 1:
								$where_1 = " AND  titulo_credito.id_titulo_credito = '$contenido_busqueda'   ";
								break;
								
							
						}
					
					
					
						$where_to  = $where .  $where_0 . $where_1 . $where_2;
						
							
						$resultDatos=$asignacion_titulo_credito->getCondiciones($columnas ,$tablas ,$where_to, $id);
						
						/*$this->view("Error",array(
								"resultado"=>$columnas.$tablas .$where_to. $id
					
						));
						
						exit();*/
					
							
					}
					
					
			
					if (isset ($_POST["ddl_resultado"]) && isset($_POST["ddl_busqueda"]))
					{
					
						//busqueda  WHERE  B.id_secretario_asignacion_secretarios=28
							
					$columnas = "B.id_asignacion_secretarios AS id_asignacion_secretarios ,
								(SELECT A.nombre_usuarios FROM usuarios A WHERE A.id_usuarios=B.id_secretario_asignacion_secretarios) AS secretarios,
								(SELECT A.nombre_usuarios FROM usuarios A WHERE A.id_usuarios=B.id_abogado_asignacion_secretarios) AS impulsadores";
					$tablas   = "asignacion_secretarios B";
					//$where    = "B.id_asignacion_secretarios>0";
					$where="";
					$id       = "B.id_asignacion_secretarios";
							
					
						$criterio = $_POST["ddl_resultado"];
						$contenido = $_POST["ddl_busqueda"];
					
							
						//$resultSet=$usuarios->getCondiciones($columnas ,$tablas ,$where, $id);
					
						if ($contenido ==1)
						{
							$where="B.id_secretario_asignacion_secretarios=".$criterio;					
							
						}elseif ($contenido ==2)
						{
							$where="B.id_abogado_asignacion_secretarios=".$criterio;
						}
					
							//Conseguimos todos los usuarios con filtros
					$resultSet=$usuarios->getCondiciones($columnas ,$tablas ,$where, $id);
					
						}
					
					
					
					
					$this->view("AutoPagos",array(
							
							"resultCon"=>$resultCon, "resultEdit"=>$resultEdit, "resultRol"=>$resultRol,"resultUsuarioSecretario"=>$resultUsuarioSecretario,"resultUsuarioImpulsor"=>$resultUsuarioImpulsor,"resultAsignacion"=>$resultAsignacion, "resultCiu"=>$resultCiu, "resultUsu"=>$resultUsu, "resultDatos"=>$resultDatos,"resultEstado"=>$resultEstado
					));
			
			
			}
			else
			{
				$this->view("Error",array(
						"resultado"=>"No tiene Permisos de Acceso a Asignacion Secretarios"
			
				));
			
			
			}
			
		}
		else
		{
	
			$this->view("ErrorSesion",array(
					"resultSet"=>""
		
						));
		}
	
	}
	 
	
	public function InsertaAutoPagos(){

		session_start();
		
		$resultado = null;
		$permisos_rol=new PermisosRolesModel();
		$auto_pagos = new AutoPagosModel();
	    $nombre_controladores = "AutoPagos";
		$id_rol= $_SESSION['id_rol'];
		$resultPer = $auto_pagos->getPermisosEditar("   nombre_controladores = '$nombre_controladores' AND id_rol = '$id_rol' " );
		
		if (!empty($resultPer))
		{
			

			if (isset ($_POST["Guardar"])   )
			{
				
				$_array_titulo_credito = $_POST["id_titulo_credito"];
				$_usuario_asigna = $_SESSION['id_usuarios'];
				$_id_ciudad = $_POST["id_ciudad"];
				$_id_usuario_impulsor = $_POST["id_usuarioImpulsor"];
				$_id_usuario_agente = $_POST["id_usuarioAgente"];
				$_fecha_asignado=$_POST["fecha_asignacion"];
				$_estado =$_POST["id_estado"];
				
				foreach($_array_titulo_credito  as $id  )
				{
						if (!empty($id) )
					{
						//busco si existe este nuevo id
						try 
						{
							$_id_titulo_credito = $id;
							//parametros  _id_titulo_credito integer, _id_usuario_asigna integer, _id_usuario_impulsor integer, _id_usuario_agente integer, _id_estado integer, _fecha_asiganacion_auto_pagos date
							$funcion = "ins_auto_pagos";
							$parametros = "'$_id_titulo_credito','$_usuario_asigna', '$_id_usuario_impulsor','$_id_usuario_agente','$_estado','$_fecha_asignado'";
							$auto_pagos->setFuncion($funcion);
							$auto_pagos->setParametros($parametros);
							$resultado=$auto_pagos->Insert();
							
							$controlador="AprobacionAutoPago";
							$tipo_notificacion = new TipoNotificacionModel();
							$resul_tipo_notificacion=$tipo_notificacion->getBy("controlador_tipo_notificacion='$controlador'");
							
							
							//inserta las notificacion
							$notificaciones = new NotificacionesModel();							
							$id_tipo_notificacion=$resul_tipo_notificacion[0]->id_tipo_notificacion;
							
							//dirigir notificacion
							$id_impulsor=$_SESSION['id_usuarios'];
							$asignacion_secreatario= new AsignacionSecretariosModel();
							$result_asg_secretario=$asignacion_secreatario->getBy("id_abogado_asignacion_secretarios='$id_impulsor'");
							$id_usuarios_dirigido_notificacion=$result_asg_secretario[0]->id_secretario_asignacion_secretarios;
							
							//descripcion de notificacion
							$descripcion_notificaciones="Auto Pago Pendiente del titulo de credito (".$_id_titulo_credito.") ";
							
							$result_notificaciones=$notificaciones->InsertaNotificaciones($id_tipo_notificacion, $id_usuarios_dirigido_notificacion, $descripcion_notificaciones);
							
										
						} catch (Exception $e) 
						{
							$this->view("Error",array(
									"resultado"=>"Eror al Asignar ->". $id
							));
						}
							
					}
					 
				}
				$traza=new TrazasModel();
				$_nombre_controlador = "AutoPagos";
				$_accion_trazas  = "Guardar";
				$_parametros_trazas = $_id_titulo_credito;
				$resultado = $traza->AuditoriaControladores($_accion_trazas, $_parametros_trazas, $_nombre_controlador);
		
				
			}
			

			$this->redirect("AutoPagos", "index");
				
			
		}
		else
		{
			$this->view("Error",array(
					"resultado"=>"No tiene Permisos de Editar Auto Pagos"
		
			));
		
		
		}
		
		
		
	}
	
	public function borrarId()
	{
		$permisos_rol = new PermisosRolesModel();

		session_start();
		
		$nombre_controladores = "AsignacionTituloCredito";
		$id_rol= $_SESSION['id_rol'];
		$resultPer = $permisos_rol->getPermisosBorrar("   nombre_controladores = '$nombre_controladores' AND id_rol = '$id_rol' " );
		
		if (!empty($resultPer))
		{
			if(isset($_GET["id_asignacion_secretarios"]))
			{
				$id_asigancionSecretarios=(int)$_GET["id_asignacion_secretarios"];
		
				$asignacionSecretario=new AsignacionSecretariosModel();
			
				$asignacionSecretario->deleteBy(" id_asignacion_secretarios",$id_asigancionSecretarios);
			
				$traza=new TrazasModel();
				$_nombre_controlador = "AsignacionTituloCredito";
				$_accion_trazas  = "Borrar";
				$_parametros_trazas = $id_asigancionSecretarios;
				$resultado = $traza->AuditoriaControladores($_accion_trazas, $_parametros_trazas, $_nombre_controlador);
			}
			
			
			$this->redirect("AsignacionTituloCredito", "index");
			
		}
		else
		{
			$this->view("Error",array(
					"resultado"=>"No tiene Permisos de Borrar Asignacion Titulo Credito"
		
			));
		
		
		}
		
	}
	
	
	
	
	
	
	public function devuelveAcciones()
	{
		$resultAcc = array();
	
		if(isset($_POST["id_controladores"]))
		{
	
			$id_controladores=(int)$_POST["id_controladores"];
	
			$acciones=new AccionesModel();
	
			$resultAcc = $acciones->getBy(" id_controladores = '$id_controladores'  ");
	
	
		}
	
		echo json_encode($resultAcc);
	
	}
	
	
	public function devuelveSubByAcciones()
	{
		$resultAcc = array();
	
		if(isset($_POST["id_acciones"]))
		{
	
			$id_acciones=(int)$_POST["id_acciones"];
	
			$acciones=new AccionesModel();
	
			$resultAcc = $acciones->getBy(" id_acciones = '$id_acciones'  ");
	
	
		}
	
		echo json_encode($resultAcc);
	
	}
	
 public function devuelveAllAcciones()
	{
		$resultAcc = array();
	
		$acciones=new AccionesModel();
	
		$resultAcc = $acciones->getAll(" id_controladores, nombre_acciones");
	
		echo json_encode($resultAcc);
	
	}
	
	public function returnImpulsorbyciudad()
	{
	
		//CONSULTA DE USUARIOS POR SU ROL
		$idciudad=(int)$_POST["ciudad"];
		$usuarios=new UsuariosModel();
		$columnas = "usuarios.id_usuarios,usuarios.nombre_usuarios";
		$tablas="usuarios,ciudad,rol";
		$id="rol.id_rol";
	
		$where="rol.id_rol=usuarios.id_rol AND usuarios.id_ciudad=ciudad.id_ciudad
		AND rol.nombre_rol='ABOGADO IMPULSOR' AND ciudad.id_ciudad='$idciudad'";
	
		$resultUsuarioImpulC=$usuarios->getCondiciones($columnas ,$tablas , $where, $id);
	
		echo json_encode($resultUsuarioImpulC);
	}
		
		
		
		public function returnAgentesbyciudad()
	{
		
		//CONSULTA DE USUARIOS POR SU ROL
		$idciudad=(int)$_POST["ciudad"];
		$usuarios=new UsuariosModel();
		$columnas = "usuarios.id_usuarios,usuarios.nombre_usuarios";
		$tablas="usuarios,ciudad,rol";
		$id="rol.id_rol";
		
		$where="rol.id_rol=usuarios.id_rol AND usuarios.id_ciudad=ciudad.id_ciudad
		AND rol.nombre_rol='AGENTE JUDICIAL' AND ciudad.id_ciudad='$idciudad'";
		
		$resultUsuarioAgenteC=$usuarios->getCondiciones($columnas ,$tablas , $where, $id);
	
		echo json_encode($resultUsuarioAgenteC);
	}
	
	
	
	public function returnSecretarios()
	{
	
		
		//CONSULTA DE USUARIOS POR SU ROL
		$columnas = "usuarios.id_usuarios,usuarios.nombre_usuarios";
		$tablas="usuarios inner join rol on(usuarios.id_rol=rol.id_rol)";
		$id="rol.id_rol";
			
		$usuarios=new UsuariosModel();
		
		$where="rol.nombre_rol='SECRETARIO'";
		$resultUsuarioSecretario=$usuarios->getCondiciones($columnas ,$tablas , $where, $id);
	
		echo json_encode($resultUsuarioSecretario);
	
	}
	
	public function returnImpulsores()
	{
	
		//CONSULTA DE USUARIOS POR SU ROL
		$columnas = "usuarios.id_usuarios,usuarios.nombre_usuarios";
		$tablas="usuarios inner join rol on(usuarios.id_rol=rol.id_rol)";
		$id="rol.id_rol";
			
		$usuarios=new UsuariosModel();
	
		$where="rol.nombre_rol='ABOGADO IMPULSOR'";
		$resultUsuarioImpulsor=$usuarios->getCondiciones($columnas ,$tablas , $where, $id);
	
		echo json_encode($resultUsuarioImpulsor);
	
	}
	
	public function CompruebaImpulsores()
	{
		$resultado=0;
		//consulta para ver si hay impulsores en la tabla asignacio secretario
		$asignacionSecretarios=new AsignacionSecretariosModel();
			
		$_id_impulsor=$_POST['id_abgImpulsor'];
		$col="id_abogado_asignacion_secretarios";
		$tbl="asignacion_secretarios";
		$whre="id_abogado_asignacion_secretarios=".$_id_impulsor;
		$id="id_asignacion_secretarios";
			
		$ressultAsg=$asignacionSecretarios->getCondiciones($col, $tbl, $whre, $id);
		
		if(empty($ressultAsg)){
			
			$this->view("Error",array(
					"resultado"=>"No existen datos"
			
			));
			exit();
		}else{
			$this->view("Error",array(
					"resultado"=>"datos extraidos"
		
			));
			exit();
		}
			
		echo json_encode($ressultAsg);
	
	}
	
	public function Reporte(){
	
		//Creamos el objeto usuario
		$subcategorias=new SubCategoriasModel();
		//Conseguimos todos los usuarios
	
	
		$columnas = " subcategorias.id_subcategorias, categorias.nombre_categorias, subcategorias.nombre_subcategorias, subcategorias.path_subcategorias";
		$tablas   = "public.subcategorias, public.categorias";
		$where    = "subcategorias.id_categorias = categorias.id_categorias";
		$id       = "categorias.nombre_categorias,subcategorias.nombre_subcategorias";
		
	
		session_start();
	
	
		if (isset(  $_SESSION['usuario']) )
		{
			$resultRep = $subcategorias->getCondicionesPDF($columnas, $tablas, $where, $id);
			
			$this->report("SubCategorias",array(	"resultRep"=>$resultRep));
	
		}
			
	
	}
	

	
}
?>      