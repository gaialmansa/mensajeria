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
 * The GroupList class
 *
 * Utility class for group list management
 */
class GroupList
{

    /**
     * Change group
     * @param type $oldGroup
     * @param type $newGroup
     */
    public static function changeGroup($oldGroup, $newGroup)
    {
        $oldGroup = (int)$oldGroup;
        $newGroup = (int)$newGroup;
        $db       = new DB();
        $query    = "
            UPDATE      zfx_user_group
            SET         id_group = $newGroup
            WHERE       id_group = $oldGroup
            AND         id_user NOT IN
                        (
                            SELECT      id_user
                            FROM        zfx_user_group
                            WHERE       id_group = $newGroup
                        );

            DELETE FROM zfx_user_group
            WHERE       id_group = $oldGroup;
        ";
        $db->setIgnoreErrors(TRUE);
        $db->qm($query);
    }
    // --------------------------------------------------------------------
}
