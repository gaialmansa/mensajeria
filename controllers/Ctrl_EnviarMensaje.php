<?php

use zfx\Controller;
use zfx\Config;

include_once('Abs_AppController.php');

class Ctrl_EnviarMensaje extends Abs_AppController
{
    

    public function _main()
    {
        
        $data = array();
        $data['grupos'] = $this->getGrupos();
        $data['usuarios'] = $this->getUsuarios();
        $this->_view->addSection('body',"EnviarMensaje",$data);
        $res = $this->_autoexec();
        $this->_view->show();
    }
    public function _getCurrentSection()
    {
        return '';
    }

    // --------------------------------------------------------------------

    public function _getCurrentSubSection()
    {
        return '';
    }
    private function getGrupos()
    {
        $grupo = New Grupos(New \zfx\DB());
    
        return $grupo->getGrupos();
    }
    private function getUsuarios()
    {
        $grupo = New Puser(New \zfx\DB());
    
        return $grupo->getUsuarios();
    }
    public function accion()
    {
        $id_usuario = $_GET['usuario'];
        $id_grupo = $_GET['grupo'];
        $mensajeText = $_GET['mensaje'];
        $id_usuario_o = 2;  // TODO: cambiarlo por el usuario logueado en el sistema
        //die(var_dump($_GET));
        if ($id_grupo != -1)   // el mensaje se esta enviando a un grupo
        {
            $Mensaje = New Mensaje(New \zfx\DB());
            $id_mensaje = $Mensaje->crear($id_usuario_o,$mensajeText);           // primero creamos el mensaje
            $lista_usuarios = $Mensaje->recuperarUsuariosGrupo($id_grupo);
                foreach( $lista_usuarios as $l )
                        $Mensaje->enlazarMensajeUsuario($id_mensaje,$l['id_usuario']);
        }
        if($id_usuario != -1)
        {
            $Mensaje = New Mensaje(New \zfx\DB());
            $id_mensaje = $Mensaje->crear($id_usuario_o, $mensajeText);    // creamos el mensaje
            $Mensaje->enlazarMensajeUsuario($id_mensaje, $id_usuario);   // lo enlazamos a nuestro unico destinatario
        }
        $this->_view->addSection('body','MensajeEnviado',array('mensaje'=>$mensajeText));
    }
    
}