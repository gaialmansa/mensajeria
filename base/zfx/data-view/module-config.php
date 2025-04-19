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


/*
 * ------------------------------------------------------------------------
 * App defaults
 * ------------------------------------------------------------------------
 */

use zfx\Config;

$cfg['data-view_maxSize']           = 40;
$cfg['data-view_currencySymbol']    = '';
$cfg['data-view_currencySeparator'] = '';
$cfg['data-view_currencyPrecision'] = 0;


/*
 * Images and other files viewers and handlers
 */
$cfg['data-view_filePath']    = Config::get('appPath') . 'res/file-db/';
$cfg['data-view_fileUrlPath'] = 'res/file-db/';

