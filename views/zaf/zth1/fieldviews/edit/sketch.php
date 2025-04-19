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
 * @var \zfx\FieldViewSketch $fv
 */

/**
 * @var integer $width
 */

/**
 * @var integer $height
 */

$src         = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $value;
$attrData    = HtmlTools::dataAttr($fv->getData());
$attrId      = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrName    = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '" ');
$attrDataFor = (trueEmpty($fv->getElementName()) ? '' : 'data-for="' . $fv->getElementName() . '" ');
// Esto en realidad es siempre verdadero con la actual implementación.
$attrVec = '';
if (substr($fv->getElementName(), 0, 1) == '_') {
    $nombreElementoVec = '_' . \zfx\StrFilter::safeEncode(\zfx\StrFilter::safeDecode(substr($fv->getElementName(), 1)) . '_vec');
    $attrVec           = 'data-namevec = "' . $nombreElementoVec . '"';
}
?>
<div class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>"><?php
    // Si no es editable y hay un fichero lo mostramos
    if (!$fv->getEditable() && !trueEmpty($value)) { ?>
        <div class="zjSketch" <?php echo $attrId; ?> <?php echo $attrName; ?> <?php echo $attrData; ?> <?php echo $attrVec; ?> data-readonly="true" data-width="<?php echo $width; ?>" data-height="<?php echo $height; ?>"></div>
    <?php }
    // Si es editable la cargamos para ser editada.
    else { ?>
        <div class="zbfSketchButtonBar">
            <a class="zjSketchButton" data-func="undo" <?php echo $attrDataFor; ?>>
                <img src="<?php echo \zfx\Res::ZafIcon('arrow-go-back-line'); ?>">
            </a>
            <a class="zjSketchButton" data-func="redo" <?php echo $attrDataFor; ?>>
                <img src="<?php echo \zfx\Res::ZafIcon('arrow-go-forward-line'); ?>">
            </a>
            <a class="zjSketchButton _enabled" data-func="thin" <?php echo $attrDataFor; ?>>
                <img src="<?php echo \zfx\Res::ZafIcon('ball-pen-line'); ?>">
            </a>
            <a class="zjSketchButton" data-func="thick" <?php echo $attrDataFor; ?>>
                <img src="<?php echo \zfx\Res::ZafIcon('ball-pen-fill'); ?>">
            </a>
            <a class="zjSketchButton _color _enabled" data-func="black" <?php echo $attrDataFor; ?>>
                <img src="<?php echo \zfx\Res::ZafIcon('drop-fill'); ?>">
            </a>
            <a class="zjSketchButton _color" data-func="red" <?php echo $attrDataFor; ?>>
                <img style="fill: red" src="<?php echo \zfx\Res::ZafIcon('zfx-drop-fill-red'); ?>">
            </a>
            <a class="zjSketchButton _color" data-func="green" <?php echo $attrDataFor; ?>>
                <img style="fill: red" src="<?php echo \zfx\Res::ZafIcon('zfx-drop-fill-green'); ?>">
            </a>
            <a class="zjSketchButton _color" data-func="blue" <?php echo $attrDataFor; ?>>
                <img style="fill: red" src="<?php echo \zfx\Res::ZafIcon('zfx-drop-fill-blue'); ?>">
            </a>
            <a class="zjSketchButton" data-func="delete" <?php echo $attrDataFor; ?>>
                <img src="<?php echo \zfx\Res::ZafIcon('delete-bin-2-line'); ?>">
            </a>
        </div>
        <?php // Si hay un fichero subido habrá que cargarlo
        if (!trueEmpty($value)) { ?>
            <div class="zjSketch" <?php echo $attrId; ?> <?php echo $attrName; ?> <?php echo $attrData; ?> <?php echo $attrVec; ?> data-width="<?php echo $width; ?>" data-height="<?php echo $height; ?>"></div>
        <?php }
// Si no hay un fichero subido mostramos la pizarra en blanco
        else { ?>
            <div class="zjSketch" <?php echo $attrId; ?> <?php echo $attrName; ?> <?php echo $attrData; ?> <?php echo $attrVec; ?> data-width="<?php echo $width; ?>" data-height="<?php echo $height; ?>"></div>
        <?php }
    } ?>
</div>