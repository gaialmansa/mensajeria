<?php

use zfx\View;
include_once('Abs_AppController.php');

class Ctrl_Index extends Abs_AppController
{
    public $user, $db, $rolId;
    public function _main()
    {
        $this->db = New \zfx\DB();          // instanciamos DB
        $user= $this->_getUser();           // Objeto User
        $userId = $user->getId();           // Id del usuario actual
        $rol = New Rol($this->db);          // Objeto Rol
            
        

        if ($this->_segment(0) == 'logout') // salida de la aplicacion
        {
            $this->logout();
            die;
        }
        if ($userId == 2)
        {
            $this->_view->show();
            die;
        }
        if ($this->_segment(0) == 'setrol') // El usuario ha seleccionado un rol (index/setrol/$rolId)
        {
            
            $rolId = $this->_segment(1);    // argumento pasado en linea
            $rol->setRol($userId, $rolId);  // Grabamos en BD
            $this->_view->show();           // TODO: hay que pasar al controlador de envio de mensajes
            die;
        }

                
        $this->rolId = $rol->getRolByUserId($userId);   // Buscamos si el usuario tiene algun rol asignado
        /**************************** SELECCION DE ROL **********************************/
        if (is_null($this->rolId ))                     // si no tiene ninguno hay que seleccionar el rol del usuario en curso
         {
            $data = array();                                // array para pasar a la vista
            $data['roles'] = $rol->getListaRoles($userId);  // Lista de roles a asumir según el grupo del usuario                    
            $this->_view = new View('SelecRol',$data);      // lanzamos la vista
            $this->_view->show();
        }
        else    /********************************** MENSAJERIA ***************************/
        {
            $mensaje = New Mensaje($this->db, $userId);             // instanciamos la mensajeria
            $data = array();                                // array para pasar a la vista
            $data['roles'] = $rol->getListaRolesEnviar($userId);    // Lista de roles a los que el usuario puede enviar
            $data['equipos'] = $rol->getListaEquiposEnviar($userId);// Lista de equipos e los que user puede enviar un mensaje
            $data['rolId'] = $this->rolId['id_rol'];                 // El identificador de rol 
            $data['userId'] = $userId;                     // El id del usuario 
            $equipoId = $rol->getTeamByRolId($this->rolId['id_rol']);    // El id del equipo al que pertenece el usuario
            $data['mensajes'] = $mensaje->recuperar($this->rolId['id_rol'],$equipoId); // recuperamos la lista de los mensajes dirigidos a este rol
//die(var_dump($data));            
            $this->_view = new View('chat',$data);      // lanzamos la vista
            $this->_view->show();
        }
        //die();

        
    }

    // --------------------------------------------------------------------

    public function _getCurrentSection()
    {
        return 'inicio';
    }

    // --------------------------------------------------------------------

    public function _getCurrentSubSection()
    {
        return '';
    }
    
    // --------------------------------------------------------------------

}
