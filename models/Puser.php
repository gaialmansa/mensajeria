<?php
class Puser
{
    
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUsuarios()
    {
        $qry = "
                SELECT * 
                FROM USUARIOS
                ORDER BY nombre
        ";
        return $this->db->qa($qry);
    }


}