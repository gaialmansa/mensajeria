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

/**
 * @var string $value
 */

/**
 * @var \zfx\FieldViewFile $fv
 */

$attrId      = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrName    = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '" ');
$attrIdOrg   = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '_org" ');
$attrNameOrg = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '_org" ');
$attrIdDel   = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '_del" ');
$attrNameDel = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '_del" ');
$attrData    = HtmlTools::dataAttr($fv->getData());
$src         = '';

// Si no es editable: si hay un fichero lo previsualizamos, en caso contrario simplemente no dibujaremos nada
if (!$fv->getEditable()) {
    if (!trueEmpty($value)) {
        if (preg_match('%^\[([0-9a-zA-Z]+)\](.+)%siu', $value, $res)) {
            $downloadAttr = 'download="' . \zfx\StrFilter::safeDecode($res[1]) . '"';
            $src          = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $res[2];
        }
        else {
            $downloadAttr = 'download';
            $src          = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $value;
        }
        ?><a
        href="<?php echo $src; ?>" <?php echo $downloadAttr; ?><?php echo $attrData; ?>>
            <i class="fas fa-file-download"></i></a>&nbsp;<a
            href="<?php echo $src; ?>" target="_blank" <?php echo $attrData; ?>>
            <i
                    class="fas fa-file-import"></i></a><?php
    }
}
else {
    // Si es editable
    ?>
    <div class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>">
        <?php
        if (!trueEmpty($value)) {
            // La previsualización otra vez
            if (preg_match('%^\[([0-9a-zA-Z]+)\](.+)%siu', $value, $res)) {
                $downloadAttr = 'download="' . \zfx\StrFilter::safeDecode($res[1]) . '"';
                $src          = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $res[2];
            }
            else {
                $downloadAttr = 'download';
                $src          = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $value;
            }
            ?>
            <a href="<?php echo $src; ?>" <?php echo $downloadAttr; ?> <?php echo $attrData; ?>><i
                        class="fas fa-file-download"></i></a>&nbsp;<a
                    href="<?php echo $src; ?>"
                    target="_blank" <?php echo $attrData; ?>><i
                        class="fas fa-file-import"></i></a>
            <?php
        }
        if (trueEmpty($value)) {
            $texto = 'Subir:';
        }
        else {
            $texto = 'Sustituir:';
        }
        ?>
        <div>
            <?php echo $texto; ?>&nbsp;<input
                    type="file" <?php echo $attrId; ?> <?php echo $attrName; ?>>
        </div>
        <?php if (!trueEmpty($value)) { ?>
            <br>
            <div>
                <input type="hidden"
                       value="<?php echo StrFilter::HTMLencode($value); ?>" <?php echo $attrIdOrg; ?> <?php echo $attrNameOrg; ?> >
                <?php if (!$fv->getField()->getRequired()) { ?>
                    <input type="checkbox"
                           value="checked" <?php echo $attrIdDel; ?><?php echo $attrNameDel; ?> > Borrar fichero
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php }