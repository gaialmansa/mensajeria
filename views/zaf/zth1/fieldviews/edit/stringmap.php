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
use zfx\StrFilter;
use function zfx\trueEmpty;
use function zfx\a;

/**
 * @var string $value
 */

/**
 * @var \zfx\FieldViewStringMap $fv
 */

$dataList = $fv->getData();
if ($fv->isInlineEdit()) {
    $dataList['zaf-beh'] = 'inlineEdit';
    $dataList['zaf-pk']  = $pk;
    $dataList['zaf-inl'] = $fv->getInlineBackend();
}
$attrData   = HtmlTools::dataAttr($dataList);
$attrId     = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrName   = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '" ');
$groupId    = $fv->getGroupId();
$lenGroupId = strlen($groupId);


if (!$fv->getEditable() && !$fv->isInlineEdit()) {
    // No es editable
    ?>
    <div class="zjRoElement <?php echo $fv->getOwnCssClass(); ?>"><?php echo StrFilter::HTMLencode($fv->value($value)); ?></div>
    <input type="hidden"
           value="<?php echo StrFilter::HTMLencode($value); ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?> /><?php
}
else {
    // Es editable
    if (count($fv->getMap()) >= 10 && !$fv->isDisableExt()) {
        $selectClass = 'zjExtSelect';
    }
    else {
        $selectClass = '';
    } ?><select readonly
                class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE) . ' ' . $selectClass; ?>" <?php echo $attrId; ?><?php echo $attrName; ?><?php echo $attrData; ?>>
    <?php
    if (!$fv->forceNotNull() && $fv->getMap()) if ($fv->getField() && (!$fv->getField()->getRequired() || $fv->getForceNull())) {
        echo '<option value="">' . StrFilter::titleCase($fv->getLocalizer()->getNull()) . '</option>';
    }
    $optionOpened = FALSE;
    if ($fv->getMap()) foreach ($fv->getMap() as $k => $v) {
        // ¿Es un optgroup?
        if ($groupId != '' && substr($k, 0, $lenGroupId) == $groupId) {
            if ($optionOpened) {
                echo "</optgroup>";
            }
            $optGroupLabel = StrFilter::HTMLencode($v);
            echo "<optgroup label=\"$optGroupLabel\">";
            $optionOpened = TRUE;
        }
        // Es una opción normal
        else {
            if ($k == $value) {
                $selected   = 'selected="selected"';
                $valueShown = StrFilter::HTMLencode($value);
            }
            else {
                $selected   = '';
                $valueShown = StrFilter::HTMLencode($k);
            }
            if ($fv->hasExtras()) {
                $e = $fv->getExtra($k);
                echo "<option data-extra=\"$e\" $selected value=\"$valueShown\">";
            }
            else {
                echo "<option $selected value=\"$valueShown\">";
            }
            echo StrFilter::HTMLencode($v);
            echo '</option>';
        }
    }

    // Por último, si había una optionGroup abierta, la cerramos
    if ($optionOpened) {
        echo "</optgroup>";
    }
    ?>
    </select><?php
}
