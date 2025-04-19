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
 * @var string $cssClass
 */

/**
 * @var string $visibleValue
 */

/**
 * @var array $actions
 */


use zfx\StrFilter;
use function zfx\trueEmpty;

?>
    <div class="_actionButtons <?php echo $cssClass; ?>"><?php
if ($actions) {
    foreach ($actions as $action) {
        if ($action['type'] == 'custom') {
            echo $action['html'];
        }
        elseif ($action['type'] == 'disabled') {
            ?><a href="javascript:void(0)"
                 class="zjFvAction _fvActionDisabled <?php echo $action['params']['class']; ?>"
                 style="<?php echo $action['params']['style']; ?>" <?php echo $action['params']['attr']; ?>><?php echo $action['params']['text']; ?></a><?php
        }
        elseif ($action['type'] == 'enabled') {
            ?><a href="<?php echo $action['params']['href']; ?>"
                 class="zjFvAction <?php echo $action['params']['class']; ?>" <?php echo $action['params']['attrData']; ?><?php echo $action['params']['attrTarget']; ?>
                 style="<?php echo $action['params']['style']; ?>" <?php echo $action['params']['attr']; ?>><?php echo $action['params']['text']; ?></a><?php
        }
    }
}
?></div><?php if (!trueEmpty($visibleValue)) {
    echo '&nbsp;' . StrFilter::HTMLencode($visibleValue);
}
