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

use zfx\FieldViewBoolean;
use zfx\HtmlTools;
use function zfx\trueEmpty;

/**
 * @var boolean $value
 */

/**
 * @var FieldViewBoolean $fv
 */
/**
 * @var string $pk
 */

$attrId   = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrName = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '" ');
$dataList = $fv->getData();
if ($fv->isInlineEdit()) {
    $dataList['zaf-beh'] = 'inlineEdit';
    $dataList['zaf-pk']  = $pk;
    $dataList['zaf-inl'] = $fv->getInlineBackend();
}
$attrData = HtmlTools::dataAttr($dataList);

$hiddenValue = '';
$checked     = '';
if ($value) {
    $checked = ' checked="checked" ';
    if (!$fv->getEditable()) {
        $hiddenValue = 'checked';
    }
}
if (!$fv->getEditable() && !$fv->isInlineEdit()) {
    // No es editable
    $disabled = ' disabled ';
}
else {
    // Es editable
    $disabled = '';
}
?><input type="hidden"
         value="<?php echo $hiddenValue; ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> />
<input type="checkbox"
       value="checked" <?php echo $checked; ?> <?php echo $disabled; ?>
       class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> />
