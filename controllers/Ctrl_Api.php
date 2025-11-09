<?php
////////////////////////////////////////////////
//////////////////////////////////////////////// API ANTIGUA. No usar
class  Ctrl_Api extends \zfx\Controller 
{
    public $db;


    public function _init()
        {
            $this->db = New \zfx\DB();
        }
    public function _main()
        {
            $res = $this->_autoexec();
            if (!$res) 
                die("Action not found");
        }
    public function esp32init()             // Obtiene el id del equipo al que está asignado el dispositivo
        {
            $rol = New Rol($this->db);
            $mac = $_POST['mac'];
            echo $rol->getTeamFromMac($mac);
        }
    public function haymsg()                // Devuelve true cuando el equipo tiene mensajes sin atender
        {
            $equipo = $_POST['equipo'];
            $msg = New Mensaje($this->db,0);
            //echo var_dump($equipo);die();
            echo  (is_null($msg->haymsg($equipo)) ? 0 : 1);
        }
}