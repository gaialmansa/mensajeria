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

class LatexTools
{

    /**
     * Filtra la cadena de entrada devolviendo una cadena para LaTeX correctamente escapada
     *
     * @param string $txt Cadena a escapar
     * @return string|null Cadena escapada
     */
    static function text($txt)
    {
        $patterns = array(
            '/\\\\/u',
            '/\\{/u',
            '/\\}/u',
            '/€/u',
            '/#/u',
            '/\\$/u',
            '/%/u',
            '/&/u',
            '/_/u',
            '/~/u',
            '/\\^/u',
            '/</u',
            '/>/u'
        );

        $replacements = array(
            '\\textbackslash ',
            '\\\\{',
            '\\\\}',
            '\\euro ',
            '\\#',
            '\\\\$',
            '\\%',
            '\\&',
            '\\_',
            '\\textasciitilde ',
            '\\textasciicircum ',
            '\\textless ',
            '\\textgreater '
        );
        return preg_replace($patterns, $replacements, $txt);
    }

    // --------------------------------------------------------------------

}
