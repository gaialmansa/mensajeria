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
                SELECT id_rol
                FROM roles 
                WHERE roles.current_user = {$userId}
        ";
        
         return $this->db->qa($qry)[0]['id_rol'];
      }
    public function getListaRoles($id_user)  // Obtiene un rol por su ID
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
 

}