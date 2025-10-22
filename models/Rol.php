<?php
class Rol
{
    
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getRoles()
    {
        $qry = "
                SELECT * 
                FROM roles
                ORDER BY nombre
        ";
        return $this->db->qa($qry);
    }


}