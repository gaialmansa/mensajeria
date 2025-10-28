<?php

include_once('Abs_AppController.php');

class Ctrl_Index extends Abs_AppController
{
    public $user, $db, $rolId;
    public function _main()
    {
        if ($this->_segment(0) == 'logout') {
            $this->logout();
            die;
        }
        $this->db = New \zfx\DB();
        $this->user = $this->_getUser();
        $rol = New Rol($this->db);
        $userId = $this->user->getId();
        if( $rol->getRolByUserId($userId) == null)
        {
            $listaRoles = $rol->getListaRoles($userId);
        // TODO: si $rol->getRolByUserId es null, hay que seleccionar uno de la lista antes de continuar

        }
        $this->rolId = $rol->getRolByUserId($userId);
        die(var_dump($this->rolId));
        $this->_view->show();
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
