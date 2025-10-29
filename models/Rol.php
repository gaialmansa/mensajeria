<?php
class Rol
{
    
    public $db;

    public function __construct($db) // Constructor. Conexion a BD
     {
        $this->db = $db;
     }

    public function getRoles()  // Obtiene la lista de roles
     {
        $qry = "
                SELECT * 
                FROM roles
                ORDER BY nombre
        ";
        return $this->db->qa($qry);
     }
    public function getRolByUserId($userId)  // Obtiene el rol de un usuario
     {
        $qry = "
                SELECT id_rol, nombre
                FROM roles 
                WHERE current = {$userId}
        ";
        
         return $this->db->qa($qry)[0];
     }
   
    public function getRolById($id_rol)
     {
      $qry = "
               SELECT nombre 
               FROM roles
               WHERE id_rol = $id_rol";
      return $this->db->qa($qry)[0]['nombre'];
     }
    public function getTeamById($id_equipo)
     {
      $qry = "
               SELECT nombre 
               FROM equipos
               WHERE id_equipo = $id_equipo";
      return $this->db->qa($qry)[0]['nombre'];
     }
   public function getTeamByRolId($id_rol)
     {
      $qry = "
               SELECT roles.id_equipo 
               FROM equipos
                  JOIN roles ON roles.id_equipo = equipos.id_equipo
               WHERE id_rol = $id_rol";
      return $this->db->qa($qry)[0]['id_equipo'];
     }
    public function getListaRoles($id_user)  // Obtiene la lista de roles que puede tomar un usuario
     {
        $qry = "
                SELECT roles.nombre, roles.id_rol FROM zfx_user 
                  JOIN zfx_user_group ON zfx_user.id = zfx_user_group.id_user
                  JOIN equipos ON zfx_user_group.id_group = equipos.id_grupo
                  JOIN roles ON equipos.id_equipo = roles.id_equipo
               WHERE zfx_user.id = $id_user
        ";
         return $this->db->qa($qry);
      }
      public function getListaRolesEnviar($id_user)  // Obtiene la lista de roles a los que un usuario puede enviar mensajes
     {
        $qry = "
                SELECT roles.nombre, roles.id_rol 
                FROM roles 
                WHERE roles.id_rol NOT IN
                  (
                  SELECT roles.id_rol FROM zfx_user 
                     JOIN zfx_user_group ON zfx_user.id = zfx_user_group.id_user
                     JOIN equipos ON zfx_user_group.id_group = equipos.id_grupo
                     JOIN roles ON equipos.id_equipo = roles.id_equipo
                  WHERE zfx_user.id = $id_user
                  )
        ";
         return $this->db->qa($qry);
      }
    public function getListaEquiposEnviar($id_user)  // Obtiene la lista de equipos a los que un usuario puede enviar mensajes
     {
        $qry = "
                SELECT equipos.nombre, equipos.id_equipo 
                FROM equipos
                WHERE id_equipo NOT IN
                  (
                  SELECT equipos.id_equipo FROM zfx_user 
                     JOIN zfx_user_group ON zfx_user.id = zfx_user_group.id_user
                     JOIN equipos ON zfx_user_group.id_group = equipos.id_grupo
                     JOIN roles ON equipos.id_equipo = roles.id_equipo
                  WHERE zfx_user.id = $id_user
                  )
        ";
         return $this->db->qa($qry);
      }
    public function setRol($userId,$rolId)
     {
         $qry = "
                  UPDATE roles
                     SET current = $userId
                  WHERE id_rol = $rolId
                  ";
         return $this->db->qa($qry);
     }
 

}