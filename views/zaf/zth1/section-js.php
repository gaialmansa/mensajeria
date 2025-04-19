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

if (!isset($_lang)) {
    $_lang = Config::get('defaultLanguage');
} ?>
    <script src="<?php echo Config::get('rootUrl'); ?>vres/jquery/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>vres/jquery-ui/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
    <script>$.widget.bridge('uitooltip', $.ui.tooltip);</script>
    <script src="<?php echo Config::get('rootUrl'); ?>vres/jquery/plugins/datepicker-i18n/datepicker-<?php echo $_lang; ?>.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>vres/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>vres/jquery/plugins/timepicker/i18n/jquery-ui-timepicker-<?php echo $_lang; ?>.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>vres/jquery/plugins/jquery.ba-throttle-debounce.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>vres/magnify/dist/jquery.magnify.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>vres/select2/js/select2.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>res/zaf/share/js/StrTools.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>res/zaf/share/js/FlowTools.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>res/zaf/share/js/Zaf.js?<?php echo uniqid(); ?>" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>res/zaf/<?php echo Config::get('admTheme'); ?>/js/Zaf-zth1.js?<?php echo uniqid(); ?>" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>res/js/app.js?<?php echo uniqid(); ?>" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo Config::get('rootUrl'); ?>vres/sketchpad/sketchpad.js" type="text/javascript" charset="utf-8"></script>
<?php if (va($jsList)) {
    foreach ($jsList as $js) { ?>
        <script src="<?php echo $js; ?>" type="text/javascript" charset="utf-8"></script>
        <?php
    }
}
