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
use function zfx\trueEmpty;

if (!isset($_url)) {
    $_url = Config::getModRootUrl() . zfx\StrFilter::dashes($_controller) . '/sch/';
}
if (!isset($_id)) {
    $_id = 'sch_' . Config::get('controllerPrefix') . $_controller;
}
if (!isset($_selector)) {
    $_selector = '#' . $_id;
}

if (isset($_primary) && $_primary) {
    if (!isset($_url2)) {
        $_url2 = Config::getModRootUrl() . zfx\StrFilter::dashes($_controller) . '/schs/';
    }
    if (!isset($_id2)) {
        $_id2 = 'schs_' . Config::get('controllerPrefix') . $_controller;
    }
    if (!isset($_selector2)) {
        $_selector2 = '#' . $_id2;
    }
}
else {
    $_url2      = $_url;
    $_id2       = $_id;
    $_selector2 = $_selector;
    $_primary   = FALSE;
}

?>

<div class="zbfFixedBox zbfBoxThin <?php if (isset($_boxClass)) echo $_boxClass; ?>">
    <div class="zjPanelDrop">
        <div class="zjPanelDropHeader">
            <div class="zjPanelDropControl">
                <a href="javascript:void(0)" data-adm-load-source="<?php echo $_url2; ?>" data-adm-load-target="<?php echo $_selector2; ?>" data-adm-options="once">⏵</a>
            </div>
            <div class="zjPanelDropLabel">
                <?php if (isset($_title) && !trueEmpty($_title)) echo $_title; ?>
            </div>
            <?php if ($_primary) { ?>
                <div class="zjPanelDropInline" id="<?php echo $_id; ?>">
                </div>
            <?php } ?>
        </div>
        <div class="zjPanelDropContent zjPanelDropClosed" id="<?php echo $_id2; ?>"></div>
    </div>
</div>
<?php if ($_primary) ?>
<script>
    actionLoad('<?php echo $_url; ?>', '<?php echo $_selector; ?>', <?php echo(isset($_autoFocus) && $_autoFocus ? 'true' : 'false'); ?>);
</script>