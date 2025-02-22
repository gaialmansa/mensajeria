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
        public function bregister()
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
        public function bunregister()
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
                $Grupo = New Grupos($nombre);
                if(Grupo.insertar($nombre) == -1)
                        $this->out(array(),10,"El grupo $nombre ya existe.");        
                $this->out(array(),0,"");
        }    
        
        public function asignarusuariogrupo()
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
        public function quitarusuariogrupo()
        {
                $id_usuario = $_POST["id_usuario"];
                $id_grupo = $_POST["id_grupo"];
                $qry = "
                DELETE  FROM rug WHERE id_usuario = $id_usuario AND id_grupo = $id_grupo";
                $this->db->q($qry);
                $this->out(array(),0,"");
        }
        public function recuperarusuariosgrupo()
        {
                
                $id_grupo = $this->getpost('id_grupo');
                
                $this->out($this->_recuperarusuariosgrupo($id_grupo),0,"");
        }
        public function borrargrupo()
        {
                $this->_borrargrupo( $_POST["id_grupo"]);      
        }
        public function modificargrupo()
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
        public function heredargrupo()
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
        public function borrargruponombre()
        {
                $nombre = $_POST["nombre"];                
                $borrame = $this->_recuperargruponombre($nombre);
                if (!is_null($borrame))
                        $this->_borrargrupo($borrame);
        }
        /**
         * 
         * U S U A R I O S
         * 
         */
        public function uregistrar()
        {
                $id_usuario = $this->getpost("id_usuario");
                $numero = $this->getpost("numero");
                $user = New User($this->db);
                $user->registrar($id_usuario, $numero);
        }
        public function ubregistro()
        {
                $numero = $this->getpost("numero");
                $user = New User($this->db);
                $ret = $user->buscarRegistro($numero);
                if (is_null(($ret)))
                        $this->outErr(404,"Not found");
        }
        public function unombre()
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
        public function ualias()
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
        public function uobservaciones()
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
        public function borrarusuario()
        {
                $this->_borrarusuario( $_POST["id_usuario"]);
        }
        public function ucrear()
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
        public function mcrear()
        {
                $id_usuario_o = $_POST["id_usuario_o"];
                $id_usuario_d = $_POST["id_usuario_d"];
                $mensajeText = $_POST["mensaje"];
                $Mensaje = New Mensaje($this->db);
                $id_mensaje = $Mensaje->crear($id_usuario_o, $mensajeText);    // creamos el mensaje
                $Mensaje->enlazarMensajeUsuario($id_mensaje, $id_usuario_d);   // lo enlazamos a nuestro unico destinatario
                $this->out(array("id_usuario_o"=>$id_usuario_o,
                "id_usuario_d"=>$id_usuario_d,
                "mensaje"=>$mensaje),0,"");

        }
        public function mcrearg()
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
                                 "mensaje"=>$mensaje),0,"");


        }
        public function mrecuperar()
        {
                $id_usuario = $_POST["id_usuario"];
                $nmensajes = $_POST["nmensajes"];
                $offset = $_POST["offset"];
                $Mensaje = New Mensaje($this->db);
                $listamensajes = $Mensaje->recuperar($id_usuario, $nmensajes, $offset);
                $this->out($listamensajes,0,'');
        }
        public function mver()
        {
                $id = $_POST['id'];
                $Mensaje = New Mensaje($this->db);
                $Mensaje->ver($id);
                $this->out(array(),0,"");

        }
        public function matender()
        {
                $id = $_POST['id'];
                $Mensaje = New Mensaje($this->db);
                $Mensaje->atender($id);
                $this->out(array(),0,"");

        }
        public function mrehusar()
        {
                $id = $_POST['id'];
                $Mensaje = New Mensaje($this->db);
                $Mensaje->rehusar($id);
                $this->out(array(),0,"");

        }
        // recupera los últimos n mensajes enviados por el usuario id_user
        public function menviadosrecuperar()
        {
                $id_usuario = $_POST["id_usuario"];
                $numero = $_POST["numero"];
                $Mensaje = New Mensaje($this->db);
                $lista = $Mensaje->enviadosRecuperar($id_usuario, $numero);
                $this->out($lista,0,'');


        }
        public function mnv()
        {
          $id_usuario = $_POST["id_usuario"];
          $Mensaje = New Mensaje($this->db);
          $lista = $Mensaje->recuperarPrimeroNoVisto($id_usuario); // devuelve el primer mensaje no visto.
          $this->out($lista,0,'');

         
        }
        //           P R O P O S I T O    G E N E R A L 

        /**
         * Summary of out
         * @param array $payload
         * @param int $error
         * @param string $errmsg
         * @return void
         */
        public function out($payload)
        {
                echo json_encode($payload);
        }
        public function outErr($errcode,$errmsg)
        {
                $salida = array ('errcode'=>$errcode, 'errmsg'=>$errmsg);
                echo json_encode($salida);
        }    
        private function getpost($varname)
        {
                if(isset($_POST[$varname]))
                        return $_POST[$varname];
                else
                if(isset($_GET[$varname]))
                        return $_GET[$varname]; 
                else
                return null;
        }
        private function _borrargrupo($id_grupo)
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
        private function _recuperargruponombre($nombre)
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
        private function _borrarusuario($id_usuario)
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
        private function _recuperarusuario( $id_usuario)
        {
                $qry = "
                SELECT * FROM usuarios WHERE id_usuario = $id_usuario";
                return $this->db->qr($qry);
        }
        private function _recuperarusuarionombre( $nombre)
        {
                $qry = "
                SELECT * FROM usuarios WHERE nombre = '$nombre'";
                return $this->db->qr($qry);
        }
        private function _recuperarusuarioalias( $alias)
        {
                $qry = "
                SELECT * FROM usuarios WHERE usuario = '$alias'";
                return $this->db->qr($qry);
        }
        
        
        
}