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


// Construir preview o no dependiendo de si existe o está en blanco
if (!trueEmpty($value)) {
    if (preg_match('%^\[([0-9a-zA-Z]+)\](.+)%siu', $value, $res)) {
        $download = \zfx\StrFilter::safeDecode($res[1]);
        $src      = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $res[2];
    }
    else {
        $download = '';
        $src      = $fv->getBaseUrl() . $fv->getTable() . '/' . $fv->getColumn() . '/' . $value;
    }
    $datas            = $fv->getData();
    $datas['magnify'] = "gallery";
    $datas['src']     = $src;
    $fv->setData($datas);
    $attrData = HtmlTools::dataAttr($fv->getData());
    ?>
    <img class="_preview" src="<?php echo $src; ?>" <?php echo $attrData; ?>><?php
    if ($download) { ?><a href="<?php echo $src; ?>"
                          download="<?php echo $download; ?>"><i
                    class="fas fa-file-download"></i></a><?php }
}
