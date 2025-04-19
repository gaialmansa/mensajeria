<?php

class  Beeper
{
        public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }   
    public function insertar($mac)
    {
        $qry = "INSERT INTO beepers (mac) VALUES ('$mac')";
        return $this->db->q($qry);
    }
    public function existe($mac)
    {
        $qry = "
        SELECT *
        FROM beepers
        WHERE mac = '$mac'";
        $ret = $this->db->qa($qry);
        if (!$ret)  // si no encuentra nada devuelve falso
            return false;
               // ha encontrado algo. Tenemos que buscar a ver si está asignado
        if (isset($ret[0]['id_usuario']) && $ret[0]['id_usuario'] !== null ) // usuario asignado
        {
         $qry = "
                      SELECT *
                        FROM beepers
                         NATURAL JOIN usuarios
                        WHERE mac = '$mac'";
        $ret = $this->db->qa($qry);
        }
        
        return $ret[0];
    }
    public function borrar($mac)
    {
        $qry = "DELETE FROM beepers WHERE mac = '$mac'";
        return $this->db->q($qry);
    }
}