<?php
/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */

use zfx\Config;
use function zfx\va;

include_once('Abs_AppCrudController.php');

class Ctrl_EquiposCrud extends Abs_AppCrudController
{

    protected function initData()
    {
        $this->auto('equipos');
    
        // Aprovechamos la clave foránea para mostrar roles en vez de numeros
        $this->relName('roles_id_equipo_fkey', 'nombre');
        // Rellenamos el usuario siempre en un nuevo registro
        //$idUsuario = $this->_getUser()->getId();
       // $this->defaultRS = [
       //     'id_equipo' => $id_equipo
       // ];
        $this->nonEditableFields = [ 'id_equipo' ];
       
        
    // --------------------------------------------------------------------
    }
        protected function setupViewForm($packedID = '')
    {
        parent::setupViewForm($packedID);
        if ($packedID != '') {
            $this->addFrmSectionRel($packedID, 'roles_id_equipo_fkey', 'RolesCrud', 'Roles');
        }
    }

}
