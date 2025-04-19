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

/**
 * @var string $id
 */

/**
 * @var string $class
 */

/**
 * @var $data
 */

/**
 * @var $value
 */

/**
 * @var $attrChecked
 */

$attrData = \zfx\HtmlTools::dataAttr($data);
?><input data-adm-selector-id="<?php echo $id; ?>" type="checkbox"
         class="zjSelector <?php echo $class; ?>" name="__selector[]"
         value="<?php echo \zfx\StrFilter::HTMLencode($value); ?>" <?php echo $attrChecked; ?> <?php echo $attrData; ?>/>