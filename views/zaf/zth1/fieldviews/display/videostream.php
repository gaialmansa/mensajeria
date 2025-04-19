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

use zfx\HtmlTools;

/**
 * @var string $value
 */

/**
 * @var \zfx\FieldViewVideoStream $fv
 */

$value    = $fv->getPrefix() . $value . $fv->getSuffix();
$attrId   = (\zfx\trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrData = HtmlTools::dataAttr($fv->getData());
?>
<video id="<?php echo $attrId; ?>" controls preload="auto" data-setup="{}"
       class="video-js  <?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>"
    <?php echo $attrData; ?>
       width="<?php echo $fv->getWidth(); ?>"
       height="<?php echo $fv->getHeight(); ?>">
    <source src="<?php echo $value; ?>">
</video>
