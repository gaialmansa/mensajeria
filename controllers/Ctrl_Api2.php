<?php 
include_once('Abs_AppController.php');

class Ctrl_Api2 extends Abs_AppController
{
    public $db, $userId;


    public function _init()       // set up de las variables globales
         {
                $this->db = New \zfx\DB();
                $user= $this->_getUser();           // Objeto User
                $this->userId = $user->getId();           // Id del usuario actual
         }
    public function _main()
            {
                $res = $this->_autoexec();
                if (!$res) 
                        die("Action not found");
            }
    public function mensaje()     // Crea un nuevo mensaje
            {
                $json = file_get_contents('php://input');
                $datos = json_decode($json, true);
                $mensaje = New Mensaje($this->db, $userId);     // inicializamos la clase mensaje
                
                
                echo json_encode($mensaje->crear($datos['id_remitente'],$datos['id_team'],$datos['id_rol'],$datos['mensaje']));
            }
    public function msgatender()  // Marca un mensaje como atendido
        {
            $json = file_get_contents('php://input');
            $datos = json_decode($json, true);
            $mensaje = New Mensaje($this->db, $userId);     // inicializamos la clase mensaje
            //die(var_dump($datos));
            echo json_encode($mensaje->atender($datos['action'], $datos['id_mensaje'], $datos['user_id']));
        }
    public function getnewmsg()     // recibe por POST una lista de id_mensaje 
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);
        //die(var_dump($input['visibleMsgsStatus']));
        $mensaje = New Mensaje($this->db, $userId);     // inicializamos la clase mensaje
        $respuesta['actualizados'] = $mensaje->actualizar($input['visibleMsgsStatus']);  // obtenemos los flags de aquellos mensajes que han sido actualizados
        $respuesta['nuevos'] = $mensaje->getLater($input['lastMsgId'],$input['rolId']);    // obtenemos la lista de mensajes posteriores al último de los que hay en pantalla
        echo json_encode($respuesta);
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