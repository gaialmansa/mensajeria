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
use zfx\StrFilter;
use function zfx\trueEmpty;

if (!isset($_url)) {
    $_url = Config::getModRootUrl() . StrFilter::dashes($_controller) . '/lst/';
}
if (!isset($_cfg)) {
    $_cfg = Config::getModRootUrl() . StrFilter::dashes($_controller) . '/cfg/';
}
if (!isset($_id)) {
    $_id = 'lst_' . Config::get('controllerPrefix') . $_controller;
}
if (!isset($_selector)) {
    $_selector = '#' . $_id;
}
if (isset($_cssClass)) {
    $classTag = "class=\"{$_cssClass}\"";
}
else {
    $classTag = "";
}
if (!isset($_boxClass)) {
    $_boxClass = 'zbfBlockBox'; // En lugar de zbfExpandBox
}
?>
<div class="<?php echo $_boxClass; ?> zbfBox"><?php
    if (isset($_title) && !trueEmpty($_title)) {
        ?><h2><?php echo $_title; ?></h2><?php } ?><?php
    if (isset($_subtitle) && !trueEmpty($_subtitle)) {
        ?><h3><?php echo $_subtitle; ?></h3><?php } ?>
    <div class="zjLstPanel zbfBlockBox"
         id="<?php echo $_id; ?>" <?php echo $classTag; ?>
         data-adm-url="<?php echo $_url; ?>"
         data-adm-cfg="<?php echo $_cfg; ?>"></div>
</div>
<script>
    var _param = '';
    <?php if (isset($_getValFromSelector)) { ?>
    _param = $('<?php echo $_getValFromSelector; ?>').val();
    <?php } ?>
    actionLoad('<?php echo $_url; ?>' + _param, '<?php echo $_selector; ?>');
    <?php
    // Si se ha recibido la variable $_extraAction como un array, hacemos otro actionLoad.
    // Esto se usa para editar inmediatamente cierto registro (por ejemplo cuando venimos
    // de un enlace externo). Siempre hacemos autofocus.
    if (isset($_extraAction) && \zfx\va($_extraAction)) { ?>
    actionLoad('<?php echo \zfx\a($_extraAction, 'source'); ?>', '<?php echo \zfx\a($_extraAction, 'target'); ?>', true);
    <?php } ?>
</script>
