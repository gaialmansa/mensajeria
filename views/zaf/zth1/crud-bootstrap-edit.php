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
    $_url = Config::getModRootUrl() . StrFilter::dashes($_controller) . '/edt/' . $_packedId;
}
if (!isset($_id)) {
    $_id = 'edt_' . Config::get('controllerPrefix') . $_controller;
}
if (!isset($_selector)) {
    $_selector = '#' . $_id;
}

?>
<div class="zbfBox"><?php
    if (isset($_title) && !trueEmpty($_title)) {

        ?><h2><?php echo $_title; ?></h2><?php } ?><?php
    if (isset($_subtitle) && !trueEmpty($_subtitle)) {

        ?><h3><?php echo $_subtitle; ?></h3><?php } ?>
    <div class="_edtPanel" id="<?php echo $_id; ?>"></div>
</div>
<script>
    actionLoad('<?php echo $_url; ?>', '<?php echo $_selector; ?>');
</script>
