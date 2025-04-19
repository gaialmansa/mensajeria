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

class Res
{

    /**
     * Obtener rápidamente la URL completa de un icono que está
     * dentro del directorio de recursos compartidos de ZAF, en su tema
     * correspondiente.
     *
     * Son ficheros .svg, pero esto podría cambiar en un futuro, por eso
     * merece la pena usar este wrapper.
     *
     * @param $name
     * @return string
     */
    public static function ZafIcon($name)
    {
        return \zfx\Config::get('zafResUrl') . 'icon/' . $name . '.svg';
    }

    // --------------------------------------------------------------------

    /**
     * Obtener rápidamente el tag IMG completa de un icono para el menu.
     *
     * Son ficheros .svg, pero esto podría cambiar en un futuro, por eso
     * merece la pena usar este wrapper.
     *
     * @param $name
     * @return string
     */
    public static function MenuIcon($name)
    {
        $src = \zfx\Config::get('rootUrl') . 'res/img/icons/menu/' . $name . '.svg';
        return "<img class=\"zbfMenuIcon\" src=\"$src\">";
    }

    // --------------------------------------------------------------------

}

