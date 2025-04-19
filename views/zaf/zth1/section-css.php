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
use function zfx\va;

?>
    <link href="<?php echo Config::get('rootUrl'); ?>vres/jquery-ui/jquery-ui.min.css" type="text/css" rel="stylesheet"/>
    <link href="<?php echo Config::get('rootUrl'); ?>vres/jquery/plugins/timepicker/jquery-ui-timepicker-addon.css" rel="stylesheet"/>
    <link href="<?php echo Config::get('rootUrl'); ?>vres/fontawesome/css/solid.css" rel="stylesheet">
    <link href="<?php echo Config::get('rootUrl'); ?>vres/fontawesome/css/fontawesome.css" rel="stylesheet">
    <link href="<?php echo Config::get('rootUrl'); ?>vres/magnify/dist/jquery.magnify.min.css" rel="stylesheet">
    <link href="<?php echo Config::get('rootUrl'); ?>vres/select2/css/select2.min.css" rel="stylesheet">
    <link href="<?php echo Config::get('rootUrl'); ?>res/zaf/<?php echo Config::get('admTheme'); ?>/css/zaf.css?<?php echo uniqid(); ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo Config::get('rootUrl'); ?>res/css/app.css?<?php echo uniqid(); ?>" rel="stylesheet">
<?php if (va($cssList)) {
    foreach ($cssList as $css) { ?>
        <link href="<?php echo $css; ?>" type="text/css" rel="stylesheet"
              media="screen" charset="utf-8"/>
        <?php
    }
}
