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
 * @var \zfx\FieldViewFile $fv
 */


// Construir preview o no dependiendo de si existe o está en blanco
if (!trueEmpty($value)) {
    $attrData = HtmlTools::dataAttr($fv->getData());
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
        href="<?php echo $src; ?>" target="_blank" <?php echo $attrData; ?>><i
                class="fas fa-file-import"></i></a><?php
}
