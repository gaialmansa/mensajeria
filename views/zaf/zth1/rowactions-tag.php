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

use zfx\HtmlTools;
use zfx\StrFilter;
use function zfx\a;
use function zfx\trueEmpty;
use function zfx\va;

/**
 * @var string $visibleValue
 */

?>
    <div class="_actionButtons <?php echo $cssClass; ?>"><?php
if (va($actions)) {
    foreach ($actions as $action) {
        $href  = sprintf($action['href'], $value);
        $class = a($action, 'class');
        $style = a($action, 'style');
        $data  = sprintf(HtmlTools::dataAttr(a($action, 'data')), $value);
        $text  = StrFilter::HTMLencode(a($action, 'text'));
        ?><a href="<?php echo $href; ?>"
             class="zjFvAction <?php echo $class; ?>"
             style="<?php echo $style; ?>" <?php echo $data; ?>><?php echo $text; ?></a>"<?php
    }
}
?></div><?php if (!trueEmpty($visibleValue)) {
    echo '&nbsp;' . StrFilter::HTMLencode($visibleValue);
}
