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
use function zfx\trueEmpty;

/**
 * @var string $value
 */

/**
 * @var \zfx\FieldViewText $fv
 */

$attrId   = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrName = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '" ');
$attrData = HtmlTools::dataAttr($fv->getData());

if (!$fv->getEditable()) {
    // No es editable
    ?>
    <div
            class="zjRoElement <?php echo $fv->getOwnCssClass(); ?>"><?php echo $value; ?></div>
    <input type="hidden"
           value="<?php echo $value; ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> /><?php
}
else {
    // Es editable
    ?><textarea cols="60" rows="4"
                class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?>><?php echo $value; ?></textarea><?php
}
