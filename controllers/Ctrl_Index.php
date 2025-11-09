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
       
        
        if ($this->_segment(0) == "chgrol")
            $rol->unsetRol($userId);
        if ($this->_segment(0) == "setrol")
            $rol->setRol($userId, $this->_segment(1));  // Grabamos en BD el rol que viene como parametro
        

        
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
            $data = array();                                        // array para pasar a la vista
            $data['roles'] = $rol->getListaRolesEnviar($userId);    // Lista de roles a los que el usuario puede enviar
            $data['equipos'] = $rol->getListaEquiposEnviar($userId);// Lista de equipos e los que user puede enviar un mensaje
            $data['rolId'] = $this->rolId['id_rol'];                // El identificador de rol 
            $data['rolname'] = $this->rolId['nombre'];              // El nombre de rol 
            $data['userId'] = $userId;                              // El id del usuario 
            $equipoId = $rol->getTeamByRolId($this->rolId['id_rol']);// El id del equipo al que pertenece el usuario
            $data['usr'] = $user->getLogin();               // El usuario en el sistema
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

//Fatal error: Uncaught TypeError: array_keys(): Argument #1 ($array) must be of type array, null given in 
//var/www/html/msg/models/pg/User.php:540 Stack trace: #0 /var/www/html/msg/models/pg/User.php(540): array_keys() 
#1 /var/www/html/msg/views/zaf/zth1/section-menu.php(25): User->getPermissions() 
#2 /var/www/html/msg/base/zfx/core/View.php(301): require('...') 
#3 /var/www/html/msg/base/zfx/core/View.php(274): zfx\View->showSection() 
#4 /var/www/html/msg/views/zaf/zth1/page-base.php(43): zfx\View->section() 
#5 /var/www/html/msg/base/zfx/core/View.php(213): require('...') 
#6 /var/www/html/msg/controllers/Ctrl_CuentaContrasena.php(54): zfx\View->show() 
#7 /var/www/html/msg/index.php(203): Ctrl_CuentaContrasena->_main() 
#8 {main} thrown in /var/www/html/msg/models/pg/User.php on line 540