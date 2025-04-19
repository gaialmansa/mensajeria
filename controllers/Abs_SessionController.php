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

use zfx\Controller;
use zfx\Localizer;
use zfx\StrValidator;
use function zfx\a;

/**
 * Controller with user and session control
 */
abstract class Abs_SessionController extends Controller
{

    /**
     * Current page number
     * @var integer
     */
    protected $_numPage = 0;
    /**
     *
     * @var User Logged user object reference
     */
    private $_user = NULL;

    // --------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_setupLoggedUser();
    }
    // --------------------------------------------------------------------

    /**
     * Compute previously logged user using session data and set it as current logged user
     */
    private function _setupLoggedUser()
    {
        @session_start(array('cookie_lifetime' => 86400));
        $userId = a($_SESSION, '_userId');
        if ($userId) {
            $this->_setUser(User::get($userId));
        }
    }

    // --------------------------------------------------------------------

    /**
     * Set given user as current logged user
     *
     * @param User $user User object reference
     */
    private function _setUser(User $user = NULL)
    {
        $this->_user = $user;
    }

    // --------------------------------------------------------------------

    public function _getLocalizer()
    {
        if ($this->_getUser()) {
            return new Localizer($this->_getUser()->getLanguage());
        }
        else {
            return new Localizer();
        }
    }
    // --------------------------------------------------------------------

    /**
     * Get current logged user object reference
     *
     * @return User
     */
    public function _getUser()
    {
        return $this->_user;
    }
    // --------------------------------------------------------------------

    /**
     * Log user in: register user as current logged user and set language
     *
     * @param User $user
     */
    public function _login(User $user)
    {
        session_regenerate_id();
        $_SESSION['_userId'] = $user->getId();
        $this->_setUser($user);
    }

    // --------------------------------------------------------------------

    /**
     * Log user out. Set language to default.
     */
    public function _logout()
    {
        $_SESSION = array();
        $params   = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'],
            $params['httponly']);
        session_destroy();
        $this->_setUser(NULL);
    }
    // --------------------------------------------------------------------

    /**
     * Set current page number from URL
     *
     * If last segment is a valid page number, store and delete it from
     * segment list.
     *
     * If $prefix is not null, that string will be required as prefix in
     * order to consider last segment as a valid page number.
     * Example: $prefix is 'page' in http://miapp.com/customers/list/page/3
     *
     * @param string $prefix Page prefix
     *
     * @return integer|null Page number or null if no page detected
     */
    public function _processNumPage($prefix = NULL)
    {
        $usingPrefix = FALSE;
        if ($prefix) {
            if ($this->_segmentCount() >= 2) {
                $prefixSegment = $this->_segment($this->_segmentCount() - 2);
                if ($prefixSegment !== $prefix) {
                    return NULL;
                }
            }
            else {
                return NULL;
            }
            $usingPrefix = TRUE;
        }
        if ($this->_segmentCount() >= 1) {
            $lastSegment = $this->_segment($this->_segmentCount() - 1);
            if (StrValidator::pageNumber($lastSegment)) {
                $this->_numPage = (int)$lastSegment;
                array_pop($this->_segments);
                if ($usingPrefix) {
                    array_pop($this->_segments);
                }
                return $this->_numPage;
            }
        }
    }
    // --------------------------------------------------------------------

    /**
     * Get current number page
     *
     * Use after _processNumPage().
     *
     * @return integer|null Current page number or NULL
     * @see _processNumPage()
     */
    public function _getNumPage()
    {
        return $this->_numPage;
    }
    // --------------------------------------------------------------------
}
