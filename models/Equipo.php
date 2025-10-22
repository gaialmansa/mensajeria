<?php

class  Equipo
{
        public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }   
  /*  public function insertar($nombre)
    {
        $qry = "SELECT id_grupo FROM grupos WHERE grupo = '$nombre'";
        $res = $this->db->qa($qry);
        if ( !is_null($res))
            return ; // mandar cabecera de error
        $qry = "INSERT INTO grupos 
                (grupo) 
                 VALUES ('$nombre')";
        return $this->db->q($qry);
    }*/
    private function existe($nombre)
    {
        $qry = "SELECT id_equipo FROM equipos WHERE nombre = '$nombre'";
        return is_null($this->db->qo($qry));

    }
    public function getEquipos()
    {
        $qry = "
            SELECT *
            FROM equipos
            ORDER BY nombre ASC
        ";                          // Recuperamos todos los grupos definidos
        return($this->db->qa($qry));
    }
}