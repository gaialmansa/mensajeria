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
use zfx\StrFilter;
use function zfx\trueEmpty;

/**
 * @var string $value
 */

/**
 * @var \zfx\FieldViewDateTime $fv
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

if (!$fv->getEditable() && !$fv->isInlineEdit()) {
    // No es editable
    ?>
    <div
            class="zjRoElement <?php echo $fv->getOwnCssClass(); ?>"><?php echo StrFilter::HTMLencode($value); ?></div>
    <input type="hidden"
           value="<?php echo StrFilter::HTMLencode($value); ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> /><?php
}
else {
    // Es editable
    $maxSize = Config::get('data-view_maxSize');
    $size    = $fv->getMaxLength();
    if ($size > $maxSize) {
        $size = $maxSize;
    }
    ?><input type="text" value="<?php echo StrFilter::HTMLencode($value); ?>"
             size="<?php echo $size; ?>"
             maxlength="<?php echo $fv->getMaxLength(); ?>"
             class="zjDateTimePicker <?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> /><?php
}