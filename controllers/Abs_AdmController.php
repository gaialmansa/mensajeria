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
use zfx\Localizer;
use zfx\View;
use function zfx\a;
use function zfx\av;
use function zfx\trueEmpty;

include_once('Abs_SessionController.php');

abstract class Abs_AdmController extends Abs_SessionController
{

    /**
     * Main view
     * @var View
     */
    protected $_view;


    /**
     * TRUE if is an AJAX request.
     * @var boolean
     */
    protected $_ajax;


    /**
     *
     * @var Localizer
     */
    protected $_loc;

    /**
     * Array of JS URIs for being passed to JS Loader View
     * @var array $jsList ;
     */
    protected $_jsList;

    /**
     * Array of CSS URIs for being passed to CSS Loader View
     * @var array $cssList ;
     */
    protected $_cssList;

    /**
     * Array of variables for being passed to JS Vars View
     * @var array $varList ;
     */
    protected $_varList;


    protected $_pageBase;


    // --------------------------------------------------------------------


    protected function _setup()
    {
        // JS, Var and CSS lists
        $this->_jsList  = array();
        $this->_cssList = array();
        $this->_varList = array(
            'rootUrl' => Config::getModRootUrl()
        );

        // Main Template View
        $this->_pageBase = 'zaf/' . Config::get('admTheme') . '/page-base';
    }

    // --------------------------------------------------------------------

    /**
     * Abs_AdmController constructor
     *
     * Ojo, en esta función todavía no tenemos los segmentos disponibles,
     * pues se asignan DESPUÉS del constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_setup();

        // Check AJAX
        if (strtolower((string)a($_SERVER, 'HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') {
            $this->_ajax = TRUE;
        }
        else {
            $this->_ajax = FALSE;
        }

        // Process POST form data and uploaded files
        $this->_procPOST();

        // Procesar el autologin si se especificó
        if (!$this->_autoLogin()) {
            // Check if a login is requested
            $this->_loginPOST();
        }

        // Is there a logged user?
        $user = $this->_getUser();


        // No valid user? Show login page
        if (!$user) {
            $data = array(
                '_title' => '',
                '_lang'  => Config::get('defaultLanguage'),
            );
            View::direct('zaf/' . Config::get('admTheme') . '/page-login', $data);
            die;
        } // No permissions? Go to index
        else {
            if (!$this->_checkPermission()) {
                $this->logout();
            }
        }

        // Localizer
        $this->_loc = $this->_getLocalizer();
    }

    // --------------------------------------------------------------------
    // Capture and process uploaded files

    protected function _procPOST()
    {
        if ($_POST) {
            $_SESSION['_post'] = $_POST;
            $this->_procFILES(); // Antes de redirect
            if (!$this->_ajax) {
                $this->_redirect($_SERVER['REQUEST_URI']);
            }
        }
        else {
            $this->_procFILES();
        }
    }

    // --------------------------------------------------------------------

    public function _procFILES()
    {
        if (!trueEmpty(Config::get('adm_upload_directory'))) {
            if ($_FILES) {
                foreach ($_FILES as $field => $fileinfo) {
                    if ($fileinfo['error'] == UPLOAD_ERR_OK) {
                        $u = uniqid('file-');
                        move_uploaded_file($fileinfo['tmp_name'], Config::get('adm_upload_directory') . $u);
                        $_SESSION['_files'][$field] = Config::get('adm_upload_directory') . $u;
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------

    protected function _loginPOST()
    {
        $post = a($_SESSION, '_post');
        if (av($post, 'login') && av($post, 'password')) {
            unset($_SESSION['_post']);
            $user = User::logIn($post['login'], $post['password']);
            if ($user) {
                $this->_login($user);
                return TRUE;
            }
            else {
                $this->_loginError();
            }
        }
        else {
            return FALSE;
        }
    }

    // --------------------------------------------------------------------

    protected function _loginError()
    {
        $this->_redirect($this->_urlPath());
    }

    // --------------------------------------------------------------------

    /**
     * All registered users are able to get in by default
     * @return boolean
     */
    protected function _checkPermission()
    {
        if ($this->_getUser()) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    // --------------------------------------------------------------------

    public function logout()
    {
        $this->_logout();
        $this->_redirect(Config::get('rootUrl'));
    }

    // --------------------------------------------------------------------

    public function _init()
    {
        // La vista básica (plantilla)
        $basicData = array(
            '_lang' => $this->_loc->getLang(),
        );
        $homeUrl   = Config::get('appHomeUrl');
        if (!trueEmpty($homeUrl)) {
            $basicData['_homeUrl'] = $homeUrl;
        }
        $this->_view = new View($this->_pageBase, $basicData);

        // La sección de menú
        $this->_view->addSection('menu', 'zaf/' . Config::get('admTheme') . '/section-menu', $this->_getMenuData());


        // La sección de CSS
        $this->_view->addSection('css', 'zaf/' . Config::get('admTheme') . '/section-css',
            array('cssList' => $this->_cssList));

        // La sección de JS
        $this->_view->addSection('js', 'zaf/' . Config::get('admTheme') . '/section-js', array('jsList' => $this->_jsList));

        // La sección de variables JS
        $this->_view->addSection('js', 'zaf/' . Config::get('admTheme') . '/section-js-vars',
            array('varList' => $this->_varList));

    }

    // --------------------------------------------------------------------

    protected function _getMenuData()
    {
        return array(
            '_csec'   => $this->_getCurrentSection(),
            '_cssec'  => $this->_getCurrentSubSection(),
            '_groups' => \zfx\Menu::propagatePerm(Config::get('appMenu')),
            '_user'   => $this->_getUser()
        );
    }

    // --------------------------------------------------------------------

    /**
     * Get current subsection
     * @return string
     */
    public abstract function _getCurrentSection();

    // --------------------------------------------------------------------

    /**
     * Get current subsection
     * @return string
     */
    public abstract function _getCurrentSubSection();

    // --------------------------------------------------------------------

    /**
     * Devuelve una acción extra (para pasar como _extraAction a crud-boostrap-list)
     * El primer segmento debe ser una ruta relativa como /modulo/clientes/edt/xxxxxxxx
     * El segundo semento debe ser un selector para mostrar el resultado como #edt_Clientes
     * Ambos segmentos deben estar codificados de forma segura.
     * @return array
     */
    protected function _getExtraAction()
    {
        $source = \zfx\StrFilter::safeDecode($this->_segment(0));
        if (\zfx\trueEmpty($source)) {
            return [];
        }
        $target = \zfx\StrFilter::safeDecode($this->_segment(1));
        if (\zfx\trueEmpty($target)) {
            return [];
        }
        return
            [
                'source' => \zfx\Config::getModRootUrl() . $source,
                'target' => $target
            ];
    }

    // --------------------------------------------------------------------

    protected function _autoLogin()
    {
        $autoLoginVar       = \zfx\Config::get('zafAutoLoginVar');
        $autoLoginAttribute = \zfx\Config::get('zafAutoLoginAttribute');
        if (\zfx\trueEmpty($autoLoginVar) || \zfx\trueEmpty($autoLoginAttribute)) {
            return FALSE;
        }
        $token = \zfx\a($_GET, $autoLoginVar);
        if (\zfx\trueEmpty($token)) {
            return FALSE;
        }

        $users = \zfx\UserTools::findUsersByAttribute($autoLoginAttribute, $token);
        if (!$users || \zfx\nwcount($users) > 1) {
            return FALSE;
        }

        $user = User::get(current($users));
        if ($user) {
            $this->_login($user);
            return TRUE;
        }
        else {
            return FALSE;
        }

    }

    // --------------------------------------------------------------------

    /**
     * Devolver una extraAction a partir de un id numérico como primer segmento
     * y que sea la acción editar estándar del crud,
     * El controlador CRUD debe ser igual que la clase actual terminado en "Crud".
     * @param $esp Segmento especial que se puede admitir en lugar en lugar de edt,
     * y en ese caso se pasa el siguiente tal cual (en vez considerarse un id),
     * aunque el target va a seguir siendo el formulario de edición.
     * @return array|void
     * @see _getExtraAction()
     */
    protected function _id2edit($esp = '')
    {
        $crudControllerClass = $this->_getController() . 'Crud';
        if ($esp == '' || $this->_segment(0) != $esp) {
            $id = (int)$this->_segment(0);
            if (!$id) {
                return [];
            }
            return
                [
                    'source' => \zfx\Config::getModRootUrl() . \zfx\StrFilter::dashes($crudControllerClass) . '/edt/' . \zfx\PKView::pack(['id' => $id]),
                    'target' => "#edt_" . \zfx\Config::get('controllerPrefix') . $crudControllerClass
                ];
        }
        else {
            $seg = $this->_segment(0);
            return
                [
                    'source' => \zfx\Config::getModRootUrl() . \zfx\StrFilter::dashes($crudControllerClass) . '/' . $seg . '/' . $this->_segment(1),
                    'target' => "#edt_" . \zfx\Config::get('controllerPrefix') . $crudControllerClass
                ];
        }
    }

    // --------------------------------------------------------------------


}
