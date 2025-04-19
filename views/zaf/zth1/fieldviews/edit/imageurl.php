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
use zfx\HtmlTools;
use function zfx\trueEmpty;

/**
 * @var string $value
 */

/**
 * @var \zfx\FieldViewImageUrl $fv
 */

$attrData = HtmlTools::dataAttr($fv->getData());
$attrId   = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrName = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '" ');

if (!$fv->getEditable()) {
    // No es editable
    // @@@ ¿Hace falta todo este follón para cuando no es editable? Hay que investigar esto.
    if ($fv->getDisplayLength() == 0) {
        ?>
        <div
                class="zjRoElement <?php echo $fv->getOwnCssClass(); ?>"><?php echo $value; ?></div>
    <?php }
    else {
        $textoAMostrar = mb_substr($value, 0, $fv->getDisplayLength(), 'UTF-8') . (mb_strlen($value, 'UTF-8') > $fv->getDisplayLength() ? '...' : '');
        echo $textoAMostrar;
    } ?><input type="hidden"
               value="<?php echo $value; ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> />
<?php }
else {
    // Es editable
    $maxSize = Config::get('data-view_maxSize');
    $size    = $fv->getMaxLength();
    if ($size > $maxSize) {
        $size = $maxSize;
    }
    ?><input type="text" value="<?php echo $value; ?>"
             size="<?php echo $size; ?>"
             maxlength="<?php echo $fv->getMaxLength(); ?>"
             class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> />
<?php } ?>
