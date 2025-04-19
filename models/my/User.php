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
use zfx\DB;

/**
 * The User Class.
 *
 * Represents an application user that can sign up, log in and log out.
 */
class User
{

    /**
     * @var integer User numeric ID
     */
    private $id;

    /**
     * @var string User login
     */
    private $login;

    /**
     *
     * @var string User ISO language code (2 letters)
     */
    private $language;

    /**
     * @var integer Generic tag for 3rd party use
     */
    private $ref1;

    /**
     * @var integer Generic tag for 3rd party use
     */
    private $ref2;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param integer $id Numeric ID
     * @param string $login User login
     * @param string $language User ISO language code (2 letters)
     */
    private function __construct(
        $id,
        $login,
        $language = NULL,
        $ref1 = 0,
        $ref2 = 0
    )
    {
        $this->id    = (int)$id;
        $this->login = (string)$login;
        $this->ref1  = (int)$ref1;
        $this->ref2  = (int)$ref2;
        if (!$language || !in_array($language, Config::get('languages'))) {
            $this->language = Config::get('defaultLanguage');
        }
        else {
            $this->language = $language;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Gets an existing user using login and password
     *
     * @param string $login User login
     * @param type $password User password
     * @return User|null User object reference or null if wrong name/password
     */
    public static function logIn($login, $password)
    {
        $db = new DB();

        $login = $db->escape($login);
        $hash  = md5($password);

        $sql      = "
            SELECT          *
            FROM            zfx_user
            WHERE           login         = '$login'
            AND             password_hash = '$hash';
        ";
        $userData = $db->qr($sql);
        if ($userData) {
            return new User($userData['id'], $userData['login'],
                $userData['language'], $userData['ref1'],
                $userData['ref2']);
        }
        else {
            return NULL;
        }
    }
    // --------------------------------------------------------------------


    /**
     * Gets an existing user using ID
     *
     * @param integer $id
     * @return User|null User object reference or null if wrong ID
     */
    public static function get($id)
    {
        $db = new DB();
        $id = (int)$id;

        $sql      = "
            SELECT          *
            FROM            zfx_user
            WHERE           id = $id;
        ";
        $userData = $db->qr($sql);
        if ($userData) {
            return new User($userData['id'], $userData['login'], $userData['language'], $userData['ref1'], $userData['ref2']);
        }
        else {
            return NULL;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Create a new user in the system
     *
     * @param string $login Desired nick
     * @param string $password Password (no hash)
     * @param string $language Language code
     * @return User Instance of created user or NULL on error
     */
    public static function create($login, $password, $language = NULL, $ref1 = 0, $ref2 = 0)
    {
        $db    = new DB();
        $login = $db->escape($login);
        $ref1  = (int)$ref1;
        $ref2  = (int)$ref2;
        $hash  = md5($password);
        if (!$language || !in_array($language, Config::get('languages'))) {
            $language = Config::get('defaultLanguage');
        }


        $sql = "
            INSERT INTO     zfx_user
                            (login, password_hash, language, ref1, ref2)
            VALUES          ('$login', '$hash', '$language', $ref1, $ref2);
        ";
        $db->setIgnoreErrors(TRUE);
        $res = $db->q($sql);
        if (!$res) {
            return NULL;
        }
        $id = (int)$db->insert_id;
        if ($id > 0) {
            return new User($id, $login, $language, $ref1, $ref2);
        }
        else {
            return NULL;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Delete from BD
     * @return boolean TRUE on success
     */
    public function delete()
    {
        $db  = new DB();
        $sql = "
            DELETE FROM     zfx_user
            WHERE           id = {$this->id};
        ";
        return $db->q($sql);
    }
    // --------------------------------------------------------------------

    /**
     * Get user's numeric database ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    // --------------------------------------------------------------------


    /**
     * Get user's ref1
     *
     * @return integer
     */
    public function getRef1()
    {
        return $this->ref1;
    }
    // --------------------------------------------------------------------

    /**
     * Get user's ref2
     *
     * @return integer
     */
    public function getRef2()
    {
        return $this->ref2;
    }
    // --------------------------------------------------------------------

    /**
     * Set user's ref1
     *
     * @param string $val
     * @return boolean TRUE on success
     */
    public function setRef1($val = 0)
    {
        $val = (int)$val;
        $res = $this->save('ref1', $val);
        if ($res) {
            $this->ref1 = $val;
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Set user's ref2
     *
     * @param string $val
     * @return boolean TRUE on success
     */
    public function setRef2($val = 0)
    {
        $val = (int)$val;
        $res = $this->save('ref2', $val);
        if ($res) {
            $this->ref2 = $val;
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    // --------------------------------------------------------------------


    /**
     * Save all user data
     *
     * @return boolean
     */
    private function save($column, $data)
    {
        $db = new DB();
        if (is_int($data)) {
            $value = $data;
        }
        else {
            $value = "'" . $db->escape($data) . "'";
        }
        $sql = "
            UPDATE          zfx_user
            SET             $column = $value
            WHERE           id      = {$this->id};
        ";
        $db->setIgnoreErrors(TRUE);
        return $db->q($sql);
    }
    // --------------------------------------------------------------------

    /**
     * Get user's nickname
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }
    // --------------------------------------------------------------------


    /**
     * Set user's nickname
     *
     * @param string $val
     * @return boolean
     */
    public function setLogin($val)
    {
        $res = $this->save('login', $val);
        if ($res) {
            $this->login = $val;
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Set user's password
     *
     * @param string $val
     * @return type
     */
    public function setPassword($val)
    {
        return $this->save('password_hash', md5($val));
    }
    // --------------------------------------------------------------------

    /**
     * Get user's language code
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
    // --------------------------------------------------------------------

    /**
     * Set user's language code
     *
     * @param string $val
     * @return boolean TRUE on success
     */
    public function setLanguage($val)
    {
        if (!$val || !in_array($val, Config::get('languages'))) {
            $val = Config::get('defaultLanguage');
        }
        $res = $this->save('language', $val);
        if ($res) {
            $this->language = $val;
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    // --------------------------------------------------------------------


    /**
     * Set user's arbitrary attribute
     *
     * @param string $code
     * @param string $value
     * @return boolean TRUE on success
     */
    public function setAttr($code, $value)
    {
        $db    = new DB();
        $code  = $db->escape($code);
        $value = $db->escape($value);
        $sql   = "
            INSERT INTO zfx_userattribute SET
                id_user = {$this->id},
                code = '$code',
                value = '$value'
            ON DUPLICATE KEY UPDATE
                value = '$value';
        ";
        return $db->q($sql);
    }
    // --------------------------------------------------------------------

    /**
     * Get user's arbitrary attribute
     *
     * @param string $code
     * @return string
     */
    public function getAttr($code)
    {
        $db   = new DB();
        $code = $db->escape($code);
        $sql  = "
            SELECT          value
            FROM            zfx_userattribute
            WHERE           id_user = {$this->id}
            AND             code    = '$code';
        ";
        return (string)$db->qr($sql, 'value');
    }
    // --------------------------------------------------------------------

    /**
     * Set user's arbitrary list of attributes
     *
     * @param array $attList Map of code=>values
     * @return TRUE on success
     */
    public function setAttrs(array $attList)
    {
        $db  = new DB();
        $sql = '';
        if (va($attList)) {
            foreach ($attList as $k => $v) {
                $code  = $db->escape($k);
                $value = $db->escape($v);
                $sql   .= "
                    INSERT INTO zfx_userattribute SET
                        id_user = {$this->id},
                        code = '$code',
                        value = '$value'
                    ON DUPLICATE KEY UPDATE
                        value = '$value';
                ";
            }
        }
        return $db->qm($sql);
    }
    // --------------------------------------------------------------------

    /**
     * Get all user's arbitrary attributes
     *
     * @return array
     */
    public function getAttrs()
    {
        $db  = new DB();
        $sql = "
            SELECT          value
            FROM            zfx_userattribute
            WHERE           id_user = {$this->id};
        ";
        return $db->qa($sql, 'code', 'value');
    }
    // --------------------------------------------------------------------

    /**
     * Test if the user has a certain permission granted.
     *
     * @param string $code
     * @return boolean TRUE if granted
     */
    public function hasPermission($code = '')
    {
        // Por defecto el permiso vacío se considera que se tiene
        if ($code == '' || $code == '|') {
            return TRUE;
        }
        // @@@ Esto se podría cachear para no hacer tantas consultas.
        $db = new DB();
        // Voy a ver si se han especificado varios permisos separados por una barra horizontal (|)
        $codes = explode('|', $code);
        if (is_array($codes) && count($codes) > 1) {
            $codeList = array();
            foreach ($codes as $c) {
                if (\zfx\trueEmpty($c)) {
                    continue;
                }
                $codeList[] = "'" . $db->escape($c) . "'";
            }
            if (count($codes) != count($codeList)) {
                return FALSE;
            }
            $codeListTxt = implode(',', $codeList);
            $cond        = "AND             zfx_permission.code IN ($codeListTxt)";
        }
        else {
            $code = $db->escape($code);
            $cond = "AND             zfx_permission.code = '$code'";
        }
        $sql = "
            SELECT          zfx_permission.code
            FROM            zfx_permission, zfx_group_permission, zfx_user_group
            WHERE           zfx_permission.id = zfx_group_permission.id_permission
            AND             zfx_user_group.id_group = zfx_group_permission.id_group
            AND             zfx_user_group.id_user = {$this->id}
            $cond;
        ";
        return (bool)$db->qr($sql, 'code');
    }
    // --------------------------------------------------------------------

    /**
     * Get complete user's permission list
     *
     * @return array
     */
    public function getPermissions()
    {
        $db  = new DB();
        $sql = "
            SELECT          zfx_permission.code
            FROM            zfx_permission, zfx_group_permission, zfx_user_group
            WHERE           zfx_permission.id = zfx_group_permission.id_permission
            AND             zfx_user_group.id_group = zfx_group_permission.id_group
            AND             zfx_user_group.id_user = {$this->id}
        ";
        return array_keys($db->qa($sql, 'code'));
    }
    // --------------------------------------------------------------------

    /**
     * Clear and write a new set of groups
     *
     * @param array $groups
     */
    public function setAllGroups($groups = NULL)
    {
        $db  = new DB();
        $sql = "DELETE FROM zfx_user_group WHERE id_user={$this->id}";
        $db->q($sql);
        $num = nwcount($groups);
        if ($num > 0) {
            $values = '';
            $i      = 1;
            foreach ($groups as $p) {
                $values .= '(' . $this->id . ',' . (int)$p . ')';
                $i++;
                if ($i <= $num) {
                    $values .= ',';
                }
            }
            $sql = "
                INSERT INTO         zfx_user_group
                                    (
                                        id_user,
                                        id_group
                                    )
                VALUES              $values;
            ";
            $db->setIgnoreErrors(TRUE);
            $db->q($sql);
        }
    }
    // --------------------------------------------------------------------
}
