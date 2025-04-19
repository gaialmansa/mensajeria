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

class Menu
{
    /**
     * Recorrer el menú completo pasado como un array, recopilando los permisos
     * de las subsecciones y aplicándolos como requisitos a las secciones y
     * a los grupos para evitar ver menús vacíos por no tener permisos
     * @param array $menu
     * @return array
     */
    public static function propagatePerm(array $menu = NULL)
    {
        if (!$menu) return [];
        foreach ($menu as &$group) {
            $groupPerms = [];
            if (array_key_exists('sections', $group)) {
                foreach ($group['sections'] as &$section) {
                    if (array_key_exists('perm', $section)) {
                        continue;
                    }
                    if (array_key_exists('subsections', $section)) {
                        $sectionPerms = [];
                        foreach ($section['subsections'] as $subsection) {
                            if (array_key_exists('perm', $subsection) && !trueEmpty($subsection['perm'])) {
                                $sectionPerms[] = $subsection['perm'];
                                $groupPerms[]   = $subsection['perm'];
                            }
                        }
                        if ($sectionPerms) {
                            $section['perm'] = implode('|', array_unique($sectionPerms));
                        }
                    }
                }
            }
            if (!array_key_exists('perm', $group)) {
                if ($groupPerms) {
                    $group['perm'] = implode('|', array_unique($groupPerms));
                }
            }
        }
        return $menu;
    }

    // --------------------------------------------------------------------

}
