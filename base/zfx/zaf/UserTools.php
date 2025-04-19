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

namespace zfx;

class UserTools
{
    /**
     * Busca una determinada propiedad y valor y en caso de existir devuelve los usuarios.
     * @param $code
     * @param $value
     * @return void
     */
    public static function findUsersByAttribute($code, $value, $profile = NULL)
    {
        $db    = new \zfx\DB($profile);
        $code  = $db->escape($code);
        $value = $db->escape($value);
        $sql   =
            "
                SELECT      id_user
                FROM        zfx_userattribute
                WHERE       code = '$code'
                AND         value = '$value'
            ";
        return $db->qa($sql, 'id_user', 'id_user');
    }

    // --------------------------------------------------------------------
}
