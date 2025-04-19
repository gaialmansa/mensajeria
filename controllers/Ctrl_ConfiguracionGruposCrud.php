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

class Ctrl_ConfiguracionGruposCrud extends Abs_AppCrudController
{

    protected function initData()
    {
        // Required
        $this->auto('zfx_group');

        $this->editAfterInsert = TRUE;

        // Field translations
        $this->tableView->setLabels(array(
            'id'          => $this->loc->getString('ctl', 'col-groups-id'),
            'name'        => $this->loc->getString('ctl', 'col-groups-name'),
            'description' => $this->loc->getString('ctl', 'col-groups-description'),
            'ref1'        => Config::get('zafGroupRef1Label'),
            'ref2'        => Config::get('zafGroupRef2Label'),
        ));

        $this->vTemplateForm = 'zaf/' . Config::get('admTheme') . '/crud-form-tabs';
        $this->tabList       = array(
            'princ' => 'Grupo',
            'perm'  => 'Permisos',
            'miem'  => 'Miembros'
        );
        $this->tabForm       = 'princ';
        $this->tabSelected   = 'princ';

        if (Config::get('appDisableGroupREF1')) {
            $this->lstHideFields[] = 'ref1';
            $this->edtHideFields[] = 'ref1';
            $this->addHideFields[] = 'ref1';
        }
        if (Config::get('appDisableGroupREF2')) {
            $this->lstHideFields[] = 'ref2';
            $this->edtHideFields[] = 'ref2';
            $this->addHideFields[] = 'ref2';
        }
        // Veamos los mapas para los campos REF1 si hay.
        $typeProvider = Config::get('zafGroupRef1Model');
        if (is_callable($typeProvider)) {
            $accountTypes = call_user_func($typeProvider);
            if (va($accountTypes)) {
                $this->tableView->stringMapFromArray('ref1', $accountTypes);
            }
        }

        // Veamos los mapas para los campos REF2 si hay.
        $typeProvider = Config::get('zafGroupRef2Model');
        if (is_callable($typeProvider)) {
            $accountTypes = call_user_func($typeProvider);
            if (va($accountTypes)) {
                $this->tableView->stringMapFromArray('ref2', $accountTypes);
            }
        }
        $this->simpleOrder = 'id';

        // Clonación
        $this->cloneType   = self::CLONE_TYPE_MULTIPLE;
        $this->cloneResult = self::CLONE_RESULT_SAVE;

    }

    // --------------------------------------------------------------------

    protected function setupViewForm($packedID = '')
    {
        parent::setupViewForm($packedID);
        if ($packedID != '') {
            $this->addFrmSectionRel($packedID, 'zfx_group_permission_relGroup', 'ConfiguracionPermisosGrupoCrud', $this->loc->getString('ctl', 'perm-rel-title'), 'perm');
            $this->addFrmSectionRel($packedID, 'zfx_user_group_relGroup', 'ConfiguracionGruposUsuarioCrud', 'Usuarios', 'miem');
        }
    }

    // --------------------------------------------------------------------

    protected function _checkPermission()
    {
        $res = parent::_checkPermission();
        if (!$res) {
            return FALSE;
        }
        return ($this->_getUser()->checkMenuPermission(Config::get('appPermConfGroups')));
    }
    // --------------------------------------------------------------------
}
