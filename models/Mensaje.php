<?php

class  Mensaje
{
   public $userId,$db; //Usuario y Acceso a base de datos

   public function __construct($db, $userId)    // constructor. Conexion a bd
     {
        $this->db = $db;
        $this->userId = $userId;
     }
   public function recuperarUsuariosEquipo($id_equipo) //Recupera todos los usuarios que pertenecen a un equipo
     {
        $qry = "
          SELECT * FROM roles
          WHERE id_equipo = $id_equipo";
        return $this->db->qa($qry);
     }
    
   public function recuperar($id_rol,$id_equipo, $nmensajes=50, $offset=0) //Recupera n mensajes de un rol a partir de un offset. Por defecto, toma los ultimos 50 mensajes
     {
      
        $qry = "
        SELECT id_mensaje as id, mensaje AS contenido,enviado AS fecha,(mensajes.id_origen = $id_rol) AS enviado, roles.nombre||'('||login||')' AS nombre_par,
               atendido 
        FROM mensajes
           JOIN roles ON id_rol = id_origen OR id_rol = id_rol_dest
           JOIN equipos ON id_equipo_dest = equipos.id_equipo
           JOIN zfx_user ON roles.current = zfx_user.id
              
        WHERE id_origen = $id_rol OR id_rol_dest = $id_rol OR id_equipo_dest = $id_equipo
        ORDER BY fecha ASC 
        LIMIT $nmensajes
        OFFSET $offset";
        return $this->db->qa($qry);
     }
    
   public function atender($accion, $id_mensaje, $userId)
     {
      $rol = New Rol($this->db);
      $rolUser = $rol->getRolByUserId($userId);
      if ($accion == 'atender')
        {
        $qry = "
        UPDATE mensajes SET atendido = now(), id_user_atendido = {$rolUser['id_rol']}
        WHERE id_mensaje = $id_mensaje
          ";
        }
      if ($accion == 'desatender')
        {
        $qry = "UPDATE mensajes SET atendido = NULL, id_user_atendido = NULL WHERE id_mensaje = $id_mensaje";  
        }
      $this->db->qa($qry);
      return $rolUser['nombre'];
     }

   public function crear($id_origen,$id_equipo_dest, $id_rol_dest,$mensaje)
     {
      $rol = New Rol($this->db);
      $ret = array('destinatario'=> '',
                   'id_mensaje'  => '');
      if ($id_equipo_dest == null)
          {
          $id_equipo_dest = "NULL";
          $ret['destinatario'] = $rol->getRolById($id_rol_dest);
          }
      if($id_rol_dest == null)
          {
          $id_rol_dest = "NULL";
          $ret['destinatario'] = $rol->getTeamById($id_equipo_dest);
          }
      $qry ="
              INSERT INTO mensajes
               (id_origen, id_equipo_dest, id_rol_dest, enviado, mensaje)
                VALUES ($id_origen, $id_equipo_dest, $id_rol_dest, now(), '$mensaje')
              RETURNING id_mensaje
              ";
        $ret['id_mensaje'] = $this->db->qa($qry)[0]['id_mensaje'];
        return $ret;
     }

  


   }
