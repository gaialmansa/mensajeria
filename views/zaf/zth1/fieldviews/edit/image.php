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
 * @var \zfx\FieldViewImage $fv
 */

$src              = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $value;
$datas            = $fv->getData();
$datas['magnify'] = "gallery";
$datas['src']     = $src;
$fv->setData($datas);
$attrData = HtmlTools::dataAttr($fv->getData());


$attrId      = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '" ');
$attrName    = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '" ');
$attrIdOrg   = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '_org" ');
$attrNameOrg = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '_org" ');
$attrIdDel   = (trueEmpty($fv->getElementID()) ? '' : 'id="' . $fv->getElementID() . '_del" ');
$attrNameDel = (trueEmpty($fv->getElementName()) ? '' : 'name="' . $fv->getElementName() . '_del" ');


?>
<div class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>"><?php


    // Si no es editable: si hay un fichero lo previsualizamos, en caso contrario simplemente no dibujaremos nada
    if (!$fv->getEditable()) {
        if (!trueEmpty($value)) {
            // No es editable
            if (preg_match('%^\[([0-9a-zA-Z]+)\](.+)%siu', $value, $res)) {
                $download = \zfx\StrFilter::safeDecode($res[1]);
                $src      = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $res[2];
            }
            else {
                $download = '';
                $src      = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $value;
            }
            ?><img src="<?php echo $src; ?>" <?php echo $attrData; ?>><?php
            if ($download) { ?><a href="<?php echo $src; ?>"
                                  download="<?php echo $download; ?>"><i
                            class="fas fa-file-download"></i></a><?php }
        }
    }
    else {
        // Si es editable....
        ?>
        <?php
        if (!trueEmpty($value)) {
            // La previsualización otra vez
            if (preg_match('%^\[([0-9a-zA-Z]+)\](.+)%siu', $value, $res)) {
                $download = \zfx\StrFilter::safeDecode($res[1]);
                $src      = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $res[2];
            }
            else {
                $download = '';
                $src      = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $value;
            }
            ?><img src="<?php echo $src; ?>" <?php echo $attrData; ?>><?php
            if ($download) { ?><a href="<?php echo $src; ?>"
                                  download="<?php echo $download; ?>"><i
                            class="fas fa-file-download"></i></a><?php }
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
                    type="file" <?php echo $attrId; ?> <?php echo $attrName; ?>
                    accept="image/*;capture=camera">
        </div>
        <?php if (!trueEmpty($value)) { ?>
            <br>
            <div>
                <input type="hidden"
                       value="<?php echo StrFilter::HTMLencode($value); ?>" <?php echo $attrIdOrg; ?> <?php echo $attrNameOrg; ?> >
                <?php if (!$fv->getField()->getRequired()) { ?>
                    <input type="checkbox"
                           value="checked" <?php echo $attrIdDel; ?><?php echo $attrNameDel; ?> > Borrar imagen
                <?php } ?>
            </div>
            <?php
        }
    } ?></div>