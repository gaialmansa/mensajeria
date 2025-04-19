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
use zfx\FieldViewPassword;
use zfx\FieldViewStringMap;
use zfx\StrFilter;
use function zfx\a;
use function zfx\aa;
use function zfx\av;
use function zfx\va;

include_once('Abs_AppCrudController.php');

class Ctrl_ConfiguracionUsuariosCrud extends Abs_AppCrudController
{

    protected function initData()
    {
        // Al menos esto se necesita, si se quiere partir de una tabla automática
        $this->auto('zfx_user');

        if (Config::get('zaf-user-view')) {
            $this->auto(Config::get('zaf-user-view'), NULL, self::MODE_LST, $this->getSchema());
            $this->getTableView(self::MODE_LST)->setLabels(Config::get('zaf-user-view-fields'));
            $this->getTableView(self::MODE_LST)->setFieldOrder(array_keys(Config::get('zaf-user-view-fields')));
        }

        $this->lstHideFields = array('password_hash');
        $this->edtHideFields = array('password_hash');
        $this->addHideFields = array('password_hash');

        if (Config::get('appDisableUserREF1')) {
            $this->lstHideFields[] = 'ref1';
            $this->edtHideFields[] = 'ref1';
            $this->addHideFields[] = 'ref1';
        }
        if (Config::get('appDisableUserREF2')) {
            $this->lstHideFields[] = 'ref2';
            $this->edtHideFields[] = 'ref2';
            $this->addHideFields[] = 'ref2';
        }

        // Veamos los mapas para los campos REF1 si hay.
        $typeProvider = Config::get('zafUserRef1Model');
        if (is_callable($typeProvider)) {
            $accountTypes = call_user_func($typeProvider);
            if (va($accountTypes)) {
                $this->getTableView()->stringMapFromArray('ref1', $accountTypes);
            }
        }

        // Veamos los mapas para los campos REF2 si hay.
        $typeProvider = Config::get('zafUserRef2Model');
        if (is_callable($typeProvider)) {
            $accountTypes = call_user_func($typeProvider);
            if (va($accountTypes)) {
                $this->getTableView()->stringMapFromArray('ref2', $accountTypes);
            }
        }

        // Editar después de insertar
        $this->editAfterInsert = TRUE;

        // Usar TABS
        $this->vTemplateForm = 'zaf/' . Config::get('admTheme') . '/crud-form-tabs';
        $this->tabList       = array(
            'userprinc'  => 'Usuario',
            'usergroups' => 'Grupos',
            'userattrs'  => 'Atributos'
        );
        $this->tabForm       = 'userprinc';
        $this->tabSelected   = 'userprinc';

        // Promote language field to a dropdown (but will show a simple string).
        $fieldLanguage = FieldViewStringMap::promote($this->tableView->getFieldView('language'));
        $langs         = array();
        foreach (Config::get('languages') as $lang) {
            $langs[$lang] = aa(Config::get('languageInfo'), $lang, 'name');
        }
        $fieldLanguage->setMap($langs);
        $this->getTableView()->setFieldView('language', $fieldLanguage);
        // Will not allow NULL languages
        $this->schema->getField('language')->setRequired(TRUE);

        // Aplico configuración anterior si se usa una vista.
        if (Config::get('zaf-user-view')) {
            $this->getTableView(self::MODE_LST)->setFieldView('language', $fieldLanguage);
            $this->getSchema(self::MODE_LST)->getField('language')->setRequired(TRUE);
        }

        // Field translations
        $this->getTableView()->setLabels(array(
            'id'            => $this->loc->getString('ctl', 'col-users-id'),
            'login'         => $this->loc->getString('ctl', 'col-users-login'),
            'password_hash' => $this->loc->getString('ctl', 'col-users-password_hash'),
            'language'      => $this->loc->getString('ctl', 'col-users-language'),
            'ref1'          => Config::get('zafUserRef1Label'),
            'ref2'          => Config::get('zafUserRef2Label'),
        ));

        // Default filterbox field (instead of blank)
        $this->filterBoxDefault = 'login';

        $this->simpleOrder       = 'id';
        $this->filterIndexedOnly = FALSE;


    }

    // --------------------------------------------------------------------

    protected function form($id = '')
    {

        // Password fields
        $pass1 = new FieldViewPassword();
        $pass1->setEditable(TRUE);
        $pass1->setElementName('_pass1');
        $pass2 = new FieldViewPassword();
        $pass2->setEditable(TRUE);
        $pass2->setElementName('_pass2');
        if ($id == '') {
            $pass1->setCssClass('zjMirror zjGroupMirror_1 zjFvReq');
            $pass2->setCssClass('zjMirror zjGroupMirror_1 zjFvReq');
        }
        else {
            $pass1->setCssClass('zjMirror zjGroupMirror_1');
            $pass2->setCssClass('zjMirror zjGroupMirror_1');
        }
        $this->tableView->addfieldView($pass1, '_pass1');
        $this->tableView->addfieldView($pass2, '_pass2');
        $this->tableView->setLabels(array(
            '_pass1' => $this->loc->getString('ctl', 'col-users-pass1'),
            '_pass2' => $this->loc->getString('ctl', 'col-users-pass2')
        ));

        parent::form($id);
    }

    // --------------------------------------------------------------------

    protected function setupViewForm($packedID = '')
    {
        if ($packedID != '') {
            $user = User::get(\zfx\PKView::unpack($packedID, 'id'));
            if ($user && $user->getLogin() == Config::get('zafProtectedUser')) {
                $this->view->addSection('userprinc', 'zaf/' . Config::get('admTheme') . '/usuario-protegido');
            }
        }
        parent::setupViewForm($packedID);
        if ($packedID != '') {
            $this->addFrmSectionRel($packedID, 'zfx_user_group_relUser', 'ConfiguracionGruposUsuarioCrud', $this->loc->getString('ctl', 'group-rel-title'), 'usergroups');
            $this->addFrmSectionRel($packedID, 'zfx_userattribute_relUser', 'ConfiguracionUsuarioAtributosCrud', $this->loc->getString('ctl', 'attr-rel-title'), 'userattrs');
        }
    }

    // --------------------------------------------------------------------

    protected function processPOST($postData)
    {
        if (av($postData, '_pass1') && a($postData, '_pass1') == a($postData, '_pass2')) {
            $postData['_' . StrFilter::safeEncode('password_hash')] = md5(StrFilter::spaceClear(a($postData, '_pass1')));
        }
        parent::processPOST($postData);
        if (!$this->isUpdate($postData)) {
            $afterCreate = Config::get('zafUserCreation');
            if (is_callable($afterCreate)) {
                call_user_func($afterCreate, $this->insertResult);
            }
        }
    }

    // --------------------------------------------------------------------


    protected function _checkPermission()
    {
        $res = parent::_checkPermission();
        if (!$res) {
            return FALSE;
        }
        return ($this->_getUser()->checkMenuPermission(Config::get('appPermConfUser')));
    }

    // --------------------------------------------------------------------


    protected function lstGetActions()
    {
        // Protegemos a cierto usuario (normalmente el administrador)
        $actions               = parent::lstGetActions();
        $del                   = $actions['del'];
        $del['disablerColumn'] = 'login';
        $del['disablerValue']  = Config::get('zafProtectedUser');
        $actions['del']        = $del;
        return $actions;
    }

    // --------------------------------------------------------------------

}
