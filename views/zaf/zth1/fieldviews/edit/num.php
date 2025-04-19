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
 * @var \zfx\Num $value
 */

/**
 * @var \zfx\FieldViewNum $fv
 */

$dataList = $fv->getData();
if ($fv->isInlineEdit()) {
    $dataList['zaf-beh'] = 'inlineEdit';
    $dataList['zaf-pk']  = $pk;
    $dataList['zaf-inl'] = $fv->getInlineBackend();
}
$attrData = HtmlTools::dataAttr($dataList);
if (!is_a($value, '\zfx\Num')) {
    $value = new \zfx\Num($value);
}
$show = \zfx\StrFilter::HTMLencode($fv->getLocalizer()->getNum($value, $fv->getPrecision()));
if ($value->eq(0)) $show = '';
$attrId   = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrName = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '" ');

$attrReadOnly = '';

if (!$fv->getEditable() && !$fv->isInlineEdit()) {
    // No es editable
    $attrReadOnly = 'readonly';
}

$maxSize = Config::get('data-view_maxSize');
$size    = $fv->getMaxLength();
if ($size > $maxSize) {
    $size = $maxSize;
}
$size = (int)$size / 2;
if ($fv->isInlineEdit()) $size = (int)$size / 2;
?><input <?php echo $attrReadOnly ?> type="text" value="<?php echo $show; ?>"
                                     size="<?php echo $size; ?>"
                                     maxlength="<?php echo $fv->getMaxLength(); ?>"
                                     class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> />

