<?php

class  Ctrl_Api extends \zfx\Controller 
{
        public $db, $retorno;


        public function _init()
         {
                $this->db = New \zfx\DB();
         }
        public function _main()
         {
                $res = $this->_autoexec();
                if (!$res) 
                        die("Action not found");
                
          //$g = new Grupos($this->db);

         }
        /************************************************
               B E E P E R S
        *************************************************/
       public function bregister() // Registra un dispositivo en el sistema
         {
         $beeper = New Beeper($this->db);
         $mac = $this->getpost('mac');  // recuperamos la mac que viene por post
         $e = $beeper->existe($mac);
         //$this->out($e);
         if (! $e)      // no existe. Cuando no existe devuelve un false
           {
           $beeper->insertar($mac);
           $this->out(array('insertado'=>1));
           }
         else
           $this->out($e);
         }
       public function bunregister() //Elimina un registro de dispositivo
         {
         $beeper = New Beeper($this->db);
         $mac = $_POST['mac'];  // recuperamos la mac que viene por post
         $e = $beeper->borrar($mac);
         }
       /************************************************
               G R U P O S 
        *************************************************/
        /**
         * Summary of creargrupo.
         * POST string $nombre
         * @return void
         */
       public function creargrupo() // crea un grupo llamado nombre. Devuelve error si ya existe
         {
                
                $nombre = $_POST['nombre'];
                // miramos si ese nombre ya existe en BD
                $Grupo = New Grupos($this->db);
                if(Grupo.insertar($nombre) == -1)
                        $this->out(array(),10,"El grupo $nombre ya existe.");        
                $this->out(array(),0,"");
         }    
        
       public function asignarusuariogrupo() // Asigna un grupo a un usuario
        {
                $id_usuario = $_POST["id_usuario"];
                $id_grupo = $_POST["id_grupo"];
                // comprobamos que no existe el registro
                $qry = "
                SELECT id FROM rug WHERE id_usuario = $id_usuario AND id_grupo = $id_grupo";
                if (!is_null($this->db->qa($qry)))      // el registro ya existe
                        {
                                $this->out(array(),10,"El usuario ya está asignado a ese grupo");
                                return;
                        }
                // comprobamos que existe el grupo
                $qry = "
                SELECT id_grupo FROM grupos WHERE id_grupo = $id_grupo";
                if (is_null($this->db->qa($qry)))      // el grupo no existe
                        {
                                $this->out(array(),11,"El grupo no existe.");
                                return;
                        }
                // comprobamos el usuario
                $qry = "
                SELECT id_usuario FROM usuarios WHERE id_usuario = $id_usuario";
                if (is_null($this->db->qa($qry)))      // el registro ya existe
                       {
                                $this->out(array(),10,"El usuario no existe");
                                return;
                       }
                        
                $qry = "
                INSERT INTO rug (id_usuario,id_grupo) VALUES ($id_usuario,$id_grupo)";
                $this->db->q($qry);
                $this->out(array(),0,"");
        }
       public function quitarusuariogrupo() // Elimina un usuario de un grupo
         {
                $id_usuario = $_POST["id_usuario"];
                $id_grupo = $_POST["id_grupo"];
                $qry = "
                DELETE  FROM rug WHERE id_usuario = $id_usuario AND id_grupo = $id_grupo";
                $this->db->q($qry);
                $this->out(array(),0,"");
         }
       public function recuperarusuariosgrupo() // Recupera todos los usuarios de un grupo
         {
                
                $id_grupo = $this->getpost('id_grupo');
                
                $this->out($this->_recuperarusuariosgrupo($id_grupo),0,"");
         }
       public function borrargrupo()   // Elimina un grupo
         {
                $this->_borrargrupo( $_POST["id_grupo"]);      
         }
       public function modificargrupo() // Modifica los datos de un grupo
         {
                $id_grupo = $_POST["id_grupo"];
                $nombre = $_POST["nombre"];
                 // comprobamos que existe el grupo
                 $qry = "
                 SELECT id_grupo FROM grupos WHERE id_grupo = $id_grupo";
                 if (is_null($this->db->qa($qry)))      // el grupo no existe
                         {
                                 $this->out(array(),11,"El grupo no existe.");
                                 return;
                         }
                $qry = "
                UPDATE grupos SET grupo = '$nombre' WHERE id_grupo = $id_grupo";
                $this->db->q($qry);
                $this->out(array(),0,"");
         }
       public function heredargrupo() // Hace que un grupo pertenezca a otro
         {
                $id_grupo = $_POST["id_grupo"];
                $id_padre = $_POST["id_padre"];
                // comprobamos que existe el grupo padre
                $qry = "
                SELECT id_grupo FROM grupos WHERE id_grupo = $id_padre";
                if (is_null($this->db->qa($qry)))      // el grupo no existe
                        {
                                $this->out(array(),11,"El grupo padre no existe.");
                                return;
                        }
                $qry = "
                SELECT id_grupo FROM grupos WHERE id_grupo = $id_grupo";
                if (is_null($this->db->qa($qry)))      // el grupo no existe
                        {
                                $this->out(array(),11,"El grupo hijo no existe.");
                                return;
                        }
                $qry = "
                UPDATE grupos SET id_padre = $id_padre WHERE id_grupo = $id_grupo";
                $this->db->q( $qry);
                $this->out(array(),0,"");
         }
       public function borrargruponombre() // Borra un grupo buscando por nombre
         {
                $nombre = $_POST["nombre"];                
                $borrame = $this->_recuperargruponombre($nombre);
                if (!is_null($borrame))
                        $this->_borrargrupo($borrame);
         }
/**
* U S U A R I O S
*/
       public function unombre() //Cambia o establece el nombre de un usuario
         {
                $id_usuario = $_POST["id_usuario"];
                $nombre = $_POST["nombre"];
                $oldUser = $this->_recuperarusuario($id_usuario);
                if (!is_null($oldUser))
                        {
                                $qry = "UPDATE usuarios SET nombre = '$nombre' WHERE id_usuario = $id_usuario";
                                $this->db->q( $qry);
                                $payload = array("anterior"=>$oldUser['nombre'], "actual"=>$nombre);
                                $this->out($payload,0,"");
                        }
                else
                        $this->out(array(),11,"El usuario $id_usuario no existe.");
                
         }
       public function ualias() // CAmbia o establece el alias de un usuario
         {
                $id_usuario = $_POST["id_usuario"];
                $usuario = $_POST["usuario"];
                $oldUser = $this->_recuperarusuario($id_usuario);
                if (!is_null($oldUser))
                        {
                                $qry = "UPDATE usuarios SET usuario = '$usuario' WHERE id_usuario = $id_usuario";
                                $this->db->q( $qry);
                                $payload = array("anterior"=>$oldUser['usuario'], "actual"=>$usuario);
                                $this->out($payload,0,"");
                        }
                else
                        $this->out(array(),11,"El usuario $id_usuario no existe.");
                
         }
       public function uobservaciones() //Cambia o establece las observaciones de un usuario
         {
                $id_usuario = $_POST["id_usuario"];
                $observaciones = $_POST["observaciones"];
                $oldUser = $this->_recuperarusuario($id_usuario);
                if (!is_null($oldUser))
                        {
                                $qry = "UPDATE usuarios SET observaciones = '$observaciones' WHERE id_usuario = $id_usuario";
                                $this->db->q( $qry);
                                $payload = array("anterior"=>$oldUser['observaciones'], "actual"=>$observaciones);
                                $this->out($payload,0,"");
                        }
                else
                        $this->out(array(),11,"El usuario $id_usuario no existe.");
                
         }
       public function borrarusuario() // Borra un usuario
         {
                $this->_borrarusuario( $_POST["id_usuario"]);
         }
       public function ucrear() //Crea un usuario nuevo
         {
                $usuario = $_POST["usuario"];
                $nombre = $_POST["nombre"];
                $observaciones = $_POST["observaciones"];
                $password = $_POST["password"];
                $ret = $this->_recuperarusuarionombre($nombre);
                if (! is_null($ret))    
                {
                        $this->out(array(),10,"Ya existe un usuario con nombre $nombre");
                        return;
                }
                $ret = $this->_recuperarusuarioalias($usuario);
                if (! is_null($ret))    
                {
                        $this->out(array(),10,"Ya existe un usuario con usuario $usuario");
                        return;
                }
                $token = md5(strcat(usuario,password));
                $qry = "
                INSERT INTO usuarios (usuario, nombre, observaciones,token) VALUES ('$usuario', '$nombre', '$observaciones','$token')";
                $this->db->q( $qry);
                $this->out(array("usuario"=>$usuario,
                                          "nombre"=>$nombre,
                                          "observaciones"=>$observaciones),0,"");

         }
      /*
      *
      * M E N S A J E S 
      *
      */
       public function mcrear() // Crea un mensaje para un usuario
         {
                $id_usuario_o = $_POST["id_usuario_o"];
                $id_usuario_d = $_POST["id_usuario_d"];
                $mensajeText = $_POST["mensaje"];
                $Mensaje = New Mensaje($this->db);
                $id_mensaje = $Mensaje->crear($id_usuario_o, $mensajeText);    // creamos el mensaje
                $Mensaje->enlazarMensajeUsuario($id_mensaje, $id_usuario_d);   // lo enlazamos a nuestro unico destinatario
                $this->out(array("id_usuario_o"=>$id_usuario_o,
                "id_usuario_d"=>$id_usuario_d,
                "mensaje"=>$mensajeText),0,"");

         }
       public function mcrearg() // Crea un mensaje para un grupo de usuarios
         { 
                $id_usuario_o = $_POST["id_usuario_o"];
                $id_grupo = $_POST["id_grupo"];
                $mensajeText = $_POST["mensaje"];
                $Mensaje = New Mensaje($this->db);
                $id_mensaje = $Mensaje->crear($id_usuario_o,$mensajeText);           // primero creamos el mensaje
                $lista_usuarios = $Mensaje->recuperarUsuariosGrupo($id_grupo);
                foreach( $lista_usuarios as $l )
                        $Mensaje->enlazarMensajeUsuario($id_mensaje,$l['id_usuario']);
                $this->out(array("id_usuario_o"=>$id_usuario_o,
                                 "destinatarios"=>$lista_usuarios,
                                 "mensaje"=>$mensajeText,
                                 "id_mensaje"=>$id_mensaje),0,"");

         }
       public function mrecuperar() //Recupera los ultimos n mensajes del usuario desde un offset
         {
                $id_usuario = $_POST["id_usuario"];
                $nmensajes = $_POST["nmensajes"];
                $offset = $_POST["offset"];
                $Mensaje = New Mensaje($this->db);
                $listamensajes = $Mensaje->recuperar($id_usuario, $nmensajes, $offset);
                $this->out($listamensajes,0,'');
         }
       public function mver() // marca el mensaje como visto
         {
                $id = $_POST['id'];
                $Mensaje = New Mensaje($this->db);
                $Mensaje->ver($id);
                $this->out(array(),0,"");

         }
       public function matender()// recupera los últimos n mensajes enviados por el usuario id_user
         {
                $id = $_POST['id'];
                $Mensaje = New Mensaje($this->db);
                $Mensaje->atender($id);
                $this->out(array(),0,"");

         }
       public function menviadosrecuperar() // recupera los últimos n mensajes enviados por el usuario id_user
         {
                $id_usuario = $_POST["id_usuario"];
                $numero = $_POST["numero"];
                $Mensaje = New Mensaje($this->db);
                $lista = $Mensaje->enviadosRecuperar($id_usuario, $numero);
                $this->out($lista,0,'');


         }
        
       public function mnv()//Recupera el mensaje, para el usuario, mas reciente y no visto
         {
          $id_usuario = $_POST["id_usuario"];
          $Mensaje = New Mensaje($this->db);
          $lista = $Mensaje->recuperarPrimeroNoVisto($id_usuario); // devuelve el primer mensaje no visto.
          $this->out($lista,0,''); 
         }
       public function mstatus() //Recupera el estado de un mensaje


         {
                $id_mensaje = $_POST["id"];
                $Mensaje = New Mensaje($this->db);
                $lista = $Mensaje->status($id_mensaje); // devuelve el estatus del mensaje
                $this->out($lista,0,''); 
              
         }
        
       public function mrnat() // Recupera el ultimo mensaje dirigido a un usuario pendiente de atender
         {
             $id_usuario = $_POST["id_usuario"];
             $Mensaje = New Mensaje($this->db);
             $lista = $Mensaje->rnat($id_usuario); // devuelve el estatus del mensaje
             $this->out($lista,0,''); 
         }
       public function mrpnat() // Recupera los mensajes dirigidos a un usuario que no han sido atendidos
         {
             $id_usuario = $_POST["id_usuario"];
             $offset = $_POST["offset"];
             $Mensaje = New Mensaje($this->db);
             $lista = $Mensaje->rpnat($id_usuario,$offset); // devuelve el estatus del mensaje
             $this->out($lista,0,''); 
         }
       public function mat() // comprueba si el mensaje ya ha sido atendido
         {
              $id_mensaje = $_POST['id_mensaje'];
              $Mensaje = New Mensaje($this->db);
              $atendido = $Mensaje->atendido($id_mensaje); // devuelve el estatus del mensaje
              
              $this->out($atendido,0,'');       
         }
         public function mv() // comprueba si el mensaje ya ha sido leido
         {
              $id_mensaje = $_POST['id_mensaje'];
              $Mensaje = New Mensaje($this->db);
              $atendido = $Mensaje->leido($id_mensaje); // devuelve el estatus del mensaje
              
              $this->out($atendido,0,'');       
         }
       
       
       
/**
* Experimental
*/


       public function exrtime() //Registra la hora
        {
              $id_usuario = $_POST["id_usuario"];
              $Exp = New Experimental($this->db);
              $Exp->grabarHora($id_usuario);
        }
       
       
       
       
       
/*
//           P R O P O S I T O    G E N E R A L 
*/
       
        
       public function out($payload) //Salida de $payload en formato Json
         {
                echo json_encode($payload);
         }
       public function outErr($errcode,$errmsg) // Salida con errores
         {
                $salida = array ('errcode'=>$errcode, 'errmsg'=>$errmsg);
                echo json_encode($salida);
         }    
       private function getpost($varname) // Recupera una variable que se ha pasado por get o posr
         {
                if(isset($_POST[$varname]))
                        return $_POST[$varname];
                else
                if(isset($_GET[$varname]))
                        return $_GET[$varname]; 
                else
                return null;
          }
       private function _borrargrupo($id_grupo) // Borra un grupo de usuarios
         {
                $qry = "SELECT id_grupo FROM grupos WHERE id_grupo = $id_grupo";
                if(is_null($this->db->qa($qry)))
                {
                        $this->out(array(),11,"El registro $id_grupo no existe");
                        return;
                }

                $qry = "DELETE FROM grupos WHERE id_grupo = $id_grupo";
                $this->db->q($qry);
                $this->out(array(),0,"");
         }
       private function _recuperargruponombre($nombre) // Recupera el id de un grupo buscando por nombre
         {
                $qry = "
                SELECT id_grupo FROM grupos WHERE grupo = '$nombre'";
                $res = $this->db->qr($qry);
                if (is_null($res))      // el grupo no existe
                        {
                                $this->out(array(),11,"El grupo $nombre no existe.");
                                return;
                        }
                return $res['id_grupo'];
         }
       private function _borrarusuario($id_usuario) // Borra un usuario
         {
                $qry = "SELECT id_usuario FROM usuarios WHERE id_usuario = $id_usuario";
                if(is_null($this->db->qa($qry)))
                {
                        $this->out(array(),11,"El registro $id_usuario no existe");
                        return;
                }

                $qry = "DELETE FROM usuarios WHERE id_usuario = $id_usuario";
                $this->db->q($qry);
                $this->out(array(),0,"");     
         }
       private function _recuperarusuario( $id_usuario) //Recupera un usuario
         {
                $qry = "
                SELECT * FROM usuarios WHERE id_usuario = $id_usuario";
                return $this->db->qr($qry);
         }
       private function _recuperarusuarionombre( $nombre) //Recupera un usuario buscando por nombre
         {
                $qry = "
                SELECT * FROM usuarios WHERE nombre = '$nombre'";
                return $this->db->qr($qry);
         }
       private function _recuperarusuarioalias( $alias) //Recupera un usuario buscando por alias
         {
                $qry = "
                SELECT * FROM usuarios WHERE usuario = '$alias'";
                return $this->db->qr($qry);
         }
        
        
        
}