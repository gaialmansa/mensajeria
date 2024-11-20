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
                \zfx\Debug::show($res);
                if (!$res) 
                        die("Action not found");
                
   //$g = new Grupos($this->db);

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
                $id_grupo = $_POST['id_grupo'];
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
                $qry = "
                INSERT INTO usuarios (usuario, nombre, observaciones) VALUES ('$usuario', '$nombre', '$observaciones')";
                $this->db->q( $qry);
                $this->out(array("usuario"=>$usuario,
                                          "nombre"=>$nombre,
                                          "observaciones"=>$observaciones),0,"");

        }
        public function mcrear()
        {
                $id_usuario_o = $_POST["id_usuario_o"];
                $id_usuario_d = $_POST["id_usuario_d"];
                $mensaje = $_POST["mensaje"];
                $id_mensaje = $this->_crearmensaje($id_usuario_o, $mensaje);    // creamos el mensaje
                $this->_enlazar_mensaje_usuario($id_mensaje, $id_usuario_d);   // lo enlazamos a nuestro unico destinatario
                $this->out(array("id_usuario_o"=>$id_usuario_o,
                "id_usuario_d"=>$id_usuario_d,
                "mensaje"=>$mensaje),0,"");

        }
        public function mcrearg()
        { 
                $id_usuario_o = $_POST["id_usuario_o"];
                $id_grupo = $_POST["id_grupo"];
                $mensaje = $_POST["mensaje"];
                $id_mensaje = $this->_crearmensaje($id_usuario_o,$mensaje);           // primero creamos el mensaje
                $lista_usuarios = $this->_recuperarusuariosgrupo($id_grupo);
                //die(var_dump($lista_usuarios));
                foreach( $lista_usuarios as $l )
                        $this->_enlazar_mensaje_usuario($id_mensaje,$l['id_usuario']);
                $this->out(array("id_usuario_o"=>$id_usuario_o,
                                 "destinatarios"=>$lista_usuarios,
                                 "mensaje"=>$mensaje),0,"");


        }
        public function mrecuperar()
        {
                $id_usuario = $_POST["id_usuario"];
                $qry = "
                SELECT uo.nombre AS origen,hora_edicion AS hora,mensaje, rmu.*
                FROM rmu
                        NATURAL JOIN usuarios
                        NATURAL JOIN mensajes
                        JOIN usuarios uo ON mensajes.id_usuario_o = uo.id_usuario
                WHERE rmu.id_usuario = $id_usuario";
                $this->out($this->db->qa($qry),0,'');


                
        }
        public function mver()
        {
                $timestamp = $fechaHora = date('Y-m-d H:i:s', time());
                $id = $_POST['id'];
                $qry = "
                UPDATE rmu
                SET visto = TRUE, hora_visto ='$timestamp' 
                WHERE id = $id";
                $this->db->q($qry);
                $this->out(array(),0,"");

        }
        public function matender()
        {
                $timestamp = $fechaHora = date('Y-m-d H:i:s', time());
                $id = $_POST['id'];
                $qry = "
                UPDATE rmu
                SET atendido = TRUE, hora_atendido ='$timestamp' 
                WHERE id = $id";
                $this->db->q($qry);
                $this->out(array(),0,"");

        }
        public function mrehusar()
        {
                $timestamp = $fechaHora = date('Y-m-d H:i:s', time());
                $id = $_POST['id'];
                $qry = "
                UPDATE rmu
                SET rehusado = TRUE, hora_rehusado ='$timestamp' 
                WHERE id = $id";
                $this->db->q($qry);
                $this->out(array(),0,"");

        }
        // recupera los últimos n mensajes enviados por el usuario id_user
        public function menviadosrecuperar()
        {
                $id_usuario = $_POST["id_usuario"];
                $numero = $_POST["numero"];
                $qry = "
                SELECT hora_edicion AS hora,mensaje, rmu.*
                FROM rmu
                        NATURAL JOIN usuarios
                        NATURAL JOIN mensajes
                WHERE mensajes.id_usuario_o = $id_usuario
                ORDER BY hora DESC
                LIMIT $numero";
                $this->out($this->db->qa($qry),0,'');

        }
        //           P R O P O S I T O    G E N E R A L 

        /**
         * Summary of out
         * @param array $payload
         * @param int $error
         * @param string $errmsg
         * @return void
         */
        public function out($payload, $error=0, $errmsg="")
        {
                $payload['error'] = $error;
                $payload['errmsg'] = $errmsg;
                echo(json_encode($payload));
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
        private function _crearmensaje($id_usuario_o, $mensaje)
        {
                $timestamp = $fechaHora = date('Y-m-d H:i:s', time());
                $qry = "
                INSERT INTO mensajes
                (id_usuario_o,  hora_edicion, mensaje)
                VALUES ($id_usuario_o,'$timestamp', '$mensaje')
                RETURNING id_mensaje";
                return $this->db->qr($qry)['id_mensaje'];
        }
        private function _enlazar_mensaje_usuario($id_mensaje, $id_usuario)
        {
                $qry = "
                INSERT INTO rmu
                (id_mensaje, id_usuario)
                VALUES
                 ($id_mensaje, $id_usuario)";
                $this->db->q($qry);
        }
        private function _recuperarusuariosgrupo($id_grupo)
        {
                $qry = "
                SELECT * FROM rug
                        NATURAL JOIN usuarios
                        NATURAL JOIN grupos
                WHERE id_grupo = $id_grupo";
                return $this->db->qa($qry);
        }
}