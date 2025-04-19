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


/**
 * @package data-view
 */

namespace zfx;

class TableActions
{

    const SHOW_ALWAYS           = 0;
    const SHOW_NO_DATA_REQUIRED = 1;
    const SHOW_DATA_REQUIRED    = 2;

    public static function renderAction(array $action, $language, $moreData, $options = NULL)
    {
        if (a($action, 'show') == TableActions::SHOW_DATA_REQUIRED && !$moreData) {
            return;
        }
        if (a($action, 'show') == TableActions::SHOW_NO_DATA_REQUIRED && $moreData) {
            return;
        }
        $viewData = array(
            'action'   => $action,
            'language' => $language,
            'moreData' => $moreData,
            'options'  => $options,
        );
        View::direct('zaf/' . Config::get('admTheme') . '/tableaction', $viewData);
    }
    // --------------------------------------------------------------------
}
