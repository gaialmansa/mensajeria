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
          SELECT  id_mensaje as id,
                  mensaje AS contenido, 
                  enviado AS fecha, 
                  (mensajes.id_origen = $id_rol) AS enviado,
                  CONCAT(equipos.nombre,destino.nombre) AS nombre_par, 
                  atendido,
                  CONCAT( origen.nombre,'(',user_origen.login,')') AS nombre_de,
                  att.nombre AS useratt
          FROM mensajes 
              JOIN roles AS origen ON origen.id_rol = id_origen 
              LEFT JOIN roles AS destino ON destino.id_rol = id_rol_dest 
              LEFT JOIN equipos ON id_equipo_dest = equipos.id_equipo 
              JOIN zfx_user AS user_origen ON origen.current = user_origen.id 
              LEFT JOIN zfx_user AS user_destino ON destino.current = user_destino.id
              LEFT JOIN roles AS att ON att.id_rol = id_rol_atendido
              
          WHERE id_origen = $id_rol OR id_rol_dest = $id_rol OR id_equipo_dest = $id_equipo
          ORDER BY fecha ASC 
          LIMIT $nmensajes
          OFFSET $offset";
       //die($qry);
        return $this->db->qa($qry);
     }
   
   public function recuperarById($id_mensaje,$id_rol) //Recupera un único mensaje
     {

      
        $qry = "
          SELECT  id_mensaje as id,
                  mensaje AS contenido, 
                  enviado AS fecha, 
                  (mensajes.id_origen = $id_rol) AS enviado,
                  CONCAT(equipos.nombre,destino.nombre) AS nombre_par, 
                  atendido,
                  CONCAT( origen.nombre,'(',user_origen.login,')') AS nombre_de,
                  att.nombre AS useratt
          FROM mensajes 
              JOIN roles AS origen ON origen.id_rol = id_origen 
              LEFT JOIN roles AS destino ON destino.id_rol = id_rol_dest 
              LEFT JOIN equipos ON id_equipo_dest = equipos.id_equipo 
              JOIN zfx_user AS user_origen ON origen.current = user_origen.id 
              LEFT JOIN zfx_user AS user_destino ON destino.current = user_destino.id
              LEFT JOIN roles AS att ON att.id_rol = id_rol_atendido
              
          WHERE id_mensaje = $id_mensaje";
       //die($qry);
        return $this->db->qa($qry);
     }
   
     public function atender($accion, $id_mensaje, $userId)
     {
      $rol = New Rol($this->db);
      $rolUser = $rol->getRolByUserId($userId);
      if ($accion == 'atender')
        {
        $qry = "
        UPDATE mensajes SET atendido = now(), id_rol_atendido = {$rolUser['id_rol']}
        WHERE id_mensaje = $id_mensaje
          ";
        }
      if ($accion == 'desatender')
        {
        $qry = "UPDATE mensajes SET atendido = NULL, id_rol_atendido = NULL WHERE id_mensaje = $id_mensaje";  
        }
      //echo $qry;
      $this->db->qa($qry);
      return $this->recuperarById($id_mensaje,$rolUser['id_rol'])[0];
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
