<?php

class  Equipo
{
        public $db;

    public function __construct($db) // constructor. Setea la conexion a BD
     {
        $this->db = $db;
     }   
    public function getEquipos()    // obtiene la lista de equipos
     {
        $qry = "
            SELECT *
            FROM equipos
            ORDER BY nombre ASC
        ";                          
        return($this->db->qa($qry));
     }
}