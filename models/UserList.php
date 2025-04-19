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

use zfx\DB;

/**
 * The UserList class
 *
 * Utility class for user list management
 */
class UserList
{

    /**
     * Check if an email address is in use
     * @return integer|NULL Numeric ID of user or NULL if not used
     */
    public static function getUserIDByEmail($email)
    {
        $db    = new DB();
        $email = $db->escape($email);
        $sql   = "
            SELECT          id
            FROM            zfx_user
            WHERE           email = '$email'
        ";
        $id    = $db->qr($sql, 'id');
        return ($id ? (int)$id : NULL);
    }
    // --------------------------------------------------------------------
}
