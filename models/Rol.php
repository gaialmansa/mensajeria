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
 

}