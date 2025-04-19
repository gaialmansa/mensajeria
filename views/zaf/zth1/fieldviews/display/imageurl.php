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

/**
 * @var string $value
 */

/**
 * @var \zfx\FieldViewImageUrl $fv
 */

$value           = $fv->getPrefix() . $value . $fv->getSuffix();
$data            = $fv->getData();
$data['magnify'] = "gallery";
$data['src']     = $value;
$fv->setData($data);
$attrData = HtmlTools::dataAttr($fv->getData());
?><img src="<?php echo $value; ?>"
       class="<?php echo $fv->getOwnCssClass() . ' ' . $fv->getCssClass(TRUE); ?>"
       $attrData>