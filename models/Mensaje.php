<?php

class  Mensaje
{
   public $userId,$db; //Usuario y Acceso a base de datos

   public function __construct($db, $userId)    // constructor. Conexion a bd
     {
        $this->db = $db;
        $this->userId = $userId;
     }
   public function recuperarUsuariosEquipo($id_equipo) //Recupera todos los usuarios que pertenecen a un equipo
     {
        $qry = "
          SELECT * FROM roles
          WHERE id_equipo = $id_equipo";
        return $this->db->qa($qry);
     }
    
   public function recuperar($id_rol, $nmensajes, $offset) //Recupera n mensajes de un rol a partir de un offset. Hay que corregir todo
     {
        $qry = "
        SELECT roles.nombre AS origen,enviado AS hora,mensaje,*
        FROM mensajes
                NATURAL JOIN roles
        WHERE id_origen = $id_rol OR id_rol_dest = $id_rol
        ORDER BY enviado DESC 
        LIMIT $nmensajes
        OFFSET $offset";
        return $this->db->qa($qry);
   }
    
   
   public function atender($id_mensaje) //Marca un mensaje como atendido. 
     {
        $timestamp = $fechaHora = date('Y-m-d H:i:s', time());
        $qry = "
                UPDATE mensajes
                SET atendido = T'$timestamp',
                     id_user_atendido = $this->userId
                WHERE id = $id_mensaje";
        return $this->db->q($qry);
     }
   

  


   }
