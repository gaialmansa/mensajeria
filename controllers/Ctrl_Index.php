<?php

include_once('Abs_AppController.php');

class Ctrl_Index extends Abs_AppController
{
    public $user;
    public function _main()
    {
        if ($this->_segment(0) == 'logout') {
            $this->logout($this->_urlController());
            die;
        }
        $this->user = $this->_getUser();
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
