<?php

class  Mensaje
{
   public $db; //Acceso a base de datos

   public function __construct($db)
     {
        $this->db = $db;
     }
   public function crear($id_usuario_o, $mensaje) //Crea un mensaje SIN enlazarlo con los usuarios destino. 
     {
                $timestamp = date('Y-m-d H:i:s', time());
                $qry = "
                INSERT INTO mensajes
                (id_usuario_o,  hora_edicion, mensaje)
                VALUES ($id_usuario_o,'$timestamp', '$mensaje')
                RETURNING id_mensaje";
                return $this->db->qr($qry)['id_mensaje'];
     }
   public function enlazarMensajeUsuario($id_mensaje, $id_usuario) //Enlaza un mensaje a un unico usuario
     {
            $qry = "
            INSERT INTO rmu
            (id_mensaje, id_usuario)
            VALUES
             ($id_mensaje, $id_usuario)";
            $this->db->q($qry);
     }
    
   public function recuperarUsuariosGrupo($id_grupo) //Recupera todos los usuarios que pertenecen a un grupo
     {
        $qry = "
          SELECT * FROM rug
              NATURAL JOIN usuarios
              NATURAL JOIN grupos
          WHERE id_grupo = $id_grupo";
        return $this->db->qa($qry);
     }
    
   public function recuperar($id_usuario, $nmensajes, $offset) //Recupera n mensajes de un usuario a partir de un offset
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
    
   public function recuperarPrimeroNoVisto($id_usuario) //Recupera el primer mensaje no visto de un usuario
     {
        $qry = "
        SELECT uo.nombre AS origen,hora_edicion AS hora,mensaje, rmu.*
        FROM rmu
         NATURAL JOIN usuarios
         NATURAL JOIN mensajes
         JOIN usuarios uo ON mensajes.id_usuario_o = uo.id_usuario
        WHERE rmu.id_usuario = $id_usuario AND NOT visto
        ORDER BY hora_edicion ";
        
        return $this->db->qr($qry);
     }
   public function ver($id) //Marca un mensaje como visto
     {
        $timestamp = $fechaHora = date('Y-m-d H:i:s', time());
        $qry = "
        UPDATE rmu
        SET visto = TRUE, hora_visto ='$timestamp' 
        WHERE id = $id";
        return $this->db->q($qry);
     }
   public function atender($id) //Marca un mensaje como atendido
     {
        $timestamp = $fechaHora = date('Y-m-d H:i:s', time());
        $qry = "
                UPDATE rmu
                SET atendido = TRUE, hora_atendido ='$timestamp' 
                WHERE id = $id";
        return $this->db->q($qry);
     }
   public function enviadosRecuperar($id_usuario, $numero) //Recupera los n ultimos mensajes enviados por un usuario
     {
        $qry = "
        SELECT hora_edicion AS hora,mensaje, rmu.*
        FROM rmu
                NATURAL JOIN usuarios
                NATURAL JOIN mensajes
        WHERE mensajes.id_usuario_o = $id_usuario
        ORDER BY hora DESC
        LIMIT $numero";
        return $this->db->qa($qry);
     }
   public function status($id_mensaje) //Recupera el estado del mensaje
     {
        $qry = "
        SELECT visto, atendido,hora_visto, hora_atendido, nombre,id_mensaje FROM rmu
            NATURAL JOIN mensajes
            NATURAL JOIN usuarios 
        WHERE id_mensaje = $id_mensaje
        ";
        return $this->db->qa($qry);
     }
   public function rnat($id_usuario)//Recupera los mensajes que se han dirigido al usuario y que no han sido atendidios por nadie
     {
      $qry = "
      SELECT uo.nombre AS origen,hora_edicion AS hora,*
         FROM mensajes 
         NATURAL JOIN rmu 
         JOIN usuarios uo ON mensajes.id_usuario_o = uo.id_usuario
         WHERE rmu.id_usuario = $id_usuario
         AND NOT EXISTS 
         (
            SELECT 1
            FROM rmu
            WHERE rmu.id_mensaje = mensajes.id_mensaje
            AND atendido 
         )";
         return $this->db->qa($qry);
      }
   public function rpnat($id_usuario,$offset)//Recupera el primer mensaje que se ha dirigido al usuario y que aun no ha sido atendidio por nadie
      {
      $qry = "
      SELECT uo.nombre AS origen,hora_edicion AS hora,*
         FROM mensajes 
         NATURAL JOIN rmu 
         JOIN usuarios uo ON mensajes.id_usuario_o = uo.id_usuario
         WHERE rmu.id_usuario = $id_usuario
         AND NOT EXISTS 
         (
            SELECT 1
            FROM rmu
            WHERE rmu.id_mensaje = mensajes.id_mensaje
            AND atendido 
         )
         ORDER BY hora_edicion LIMIT 1 OFFSET $offset";
         return $this->db->qa($qry);
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
   public function leido($id_mensaje)  // devuelve verdadero cuando el mensaje ya ha sido atendido
      {
         $qry = "
         SELECT id
            FROM rmu 
            WHERE id_mensaje = $id_mensaje
             AND visto 
            ";
            return $this->db->qr($qry); 

      }


   }
