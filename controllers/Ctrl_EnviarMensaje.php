<?php

use zfx\Controller;
use zfx\Config;

include_once('Abs_AppController.php');

class Ctrl_EnviarMensaje extends Abs_AppController
{
    

    public function _main()
    {
        
        $data = array();
        $data['equipos'] = $this->getEquipos();
        $data['roles'] = $this->getRoles();
        //die(var_dump($data));
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
    private function getEquipos()
    {
        $equipo = New Equipo(New \zfx\DB());
    
        return $equipo->getEquipos();
    }
    private function getRoles()
    {
        $rol = New Rol(New \zfx\DB());
    
        return $rol->getRoles();
    }
    public function accion()
    {
        $id_rol = $_GET['rol'];
        $id_equipo = $_GET['equipo'];
        $mensajeText = $_GET['mensaje'];
        $id_origen = 15;  // TODO: cambiarlo por el usuario logueado en el sistema
        //die(var_dump($_GET));
        if ($id_equipo != -1)   // el mensaje se esta enviando a un grupo
        {
            $Mensaje = New Mensaje(New \zfx\DB());
            $id_mensaje = $Mensaje->crear($id_origen,$mensajeText);           // primero creamos el mensaje
            $lista_roles = $Mensaje->recuperarUsuariosEquipo($id_equipo);
                foreach( $lista_roles as $l )
                        $Mensaje->enlazarMensajeRol($id_mensaje,$l['id_rol']);
        }
        if($id_rol != -1)
        {
            $Mensaje = New Mensaje(New \zfx\DB());
            $id_mensaje = $Mensaje->crear($id_oç, $mensajeText);    // creamos el mensaje
            $Mensaje->enlazarMensajeRol($id_mensaje, $id_rol);   // lo enlazamos a nuestro unico destinatario
        }
        $this->_view->addSection('body','MensajeEnviado',array('mensaje'=>$mensajeText));
    }
    
}