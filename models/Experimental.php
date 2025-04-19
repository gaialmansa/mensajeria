<?php

class Experimental
{
    public $db; //Acceso a base de datos

    public function __construct($db)
     {
        $this->db = $db;
     }

    public function grabarHora($id_usuario)
     {
        $qry = "
                INSERT INTO extime (id_usuario, hora)
                VALUES ($id_usuario, NOW())";
        $this->db->qr($qry);
     }




}