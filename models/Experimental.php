<?php

class Experimental
{
    public $db; //Acceso a base de datos

    public function __construct($db)
     {
        $this->db = $db;
     }

    public function grabarHora($id_usuario,$pcbat)
     {
        $qry = "
                INSERT INTO extime (id_usuario, hora,pcbat)
                VALUES ($id_usuario, NOW(),$pcbat)";
        $this->db->qr($qry);
     }


     


}