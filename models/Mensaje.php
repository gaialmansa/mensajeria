<?php

class  Mensaje
{
   public $db; //Acceso a base de datos

   public function __construct($db)    // constructor. Conexion a bd
     {
        $this->db = $db;
     }
   public function recuperarUsuariosEquipo($id_equipo) //Recupera todos los usuarios que pertenecen a un equipo
     {
        $qry = "
          SELECT * FROM roles
              NATURAL JOIN usuarios
          WHERE id_grupo = $id_grupo";
        return $this->db->qa($qry);
     }
    
   public function recuperar($id_usuario, $nmensajes, $offset) //Recupera n mensajes de un usuario a partir de un offset. Hay que corregir todo
     {
        $qry = "
        SELECT uo.nombre AS origen,hora_edicion AS hora,mensaje, rmu.*
        FROM rmu
                NATURAL JOIN usuarios
                NATURAL JOIN mensajes
                JOIN usuarios uo ON mensajes.id_usuario_o = uo.id_usuario
        WHERE rmu.id_usuario = $id_usuario
        ORDER BY hora_edicion DESC 
        LIMIT $nmensajes
        OFFSET $offset";
        $listamensajes = $this->db->qa($qry);
        
        foreach ($listamensajes as &$m)
        {
        $idm = $m['id_mensaje'];
        $qry = "
                SELECT visto FROM rmu WHERE id_mensaje = $idm AND  NOT visto
                ";      // este query devuelve resultados si hay algun destinatario que aun no haya visto el mensaje
            $m['visto'] =  ! $this->db->qa($qry);

            $qry = "
                SELECT atendido FROM rmu WHERE id_mensaje = $idm AND  NOT atendido
            ";      // este query devuelve resultados si hay algun destinatario que aun no haya atendido el mensaje
            $m['atendido'] =  ! $this->db->qa($qry);
            
        }
        return $listamensajes;
     }
    
   
   public function atender($id) //Marca un mensaje como atendido. TODO: corregir el query
     {
        $timestamp = $fechaHora = date('Y-m-d H:i:s', time());
        $qry = "
                UPDATE rmu
                SET atendido = TRUE, hora_atendido ='$timestamp' 
                WHERE id = $id";
        return $this->db->q($qry);
     }
   
   public function atendido($id_mensaje)  // devuelve verdadero cuando el mensaje ya ha sido atendido
      {
         $qry = "
         SELECT id
            FROM rmu 
            WHERE id_mensaje = $id_mensaje
             AND atendido 
            ";
            return $this->db->qr($qry); 

      }
  


   }
