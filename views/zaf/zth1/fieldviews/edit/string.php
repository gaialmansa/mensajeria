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
 * @var \zfx\FieldViewString $fv
 */

/**
 * @var string $pk
 */
$dataList = $fv->getData();
if ($fv->isInlineEdit()) {
    $dataList['zaf-beh'] = 'inlineEdit';
    $dataList['zaf-pk']  = $pk;
    $dataList['zaf-inl'] = $fv->getInlineBackend();
}
$attrData = HtmlTools::dataAttr($dataList);
$attrId   = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrName = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '" ');

$maxSize = Config::get('data-view_maxSize');
$size    = $fv->getMaxLength();
if ($size > $maxSize) {
    $size = $maxSize;
}
if ($fv->isInlineEdit()) $size = (int)$size / 2;
if (!$fv->getEditable() && !$fv->isInlineEdit()) {
    // No es editable
    // @@@ ¿Hace falta todo este follón para cuando no es editable? Hay que investigar esto.
    if ($fv->getDisplayLength() == 0) {
        $textoAMostrar = $value;
    }
    else {
        $textoAMostrar = mb_substr($value, 0, $fv->getDisplayLength(), 'UTF-8') . (mb_strlen($value, 'UTF-8') > $fv->getDisplayLength() ? '...' : '');
    } ?>
    <input readonly type="text" value="<?php echo $value; ?>"
           size="<?php echo $size; ?>"
           maxlength="<?php echo $fv->getMaxLength(); ?>"
           class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>"
        <?php echo $attrId; ?>
        <?php echo $attrName; ?>
        <?php echo $attrData; ?> />
<?php }
else {
    // Es editable
    ?><input type="text" value="<?php echo $value; ?>"
             size="<?php echo $size; ?>"
             maxlength="<?php echo $fv->getMaxLength(); ?>"
             class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> />
<?php } ?>
