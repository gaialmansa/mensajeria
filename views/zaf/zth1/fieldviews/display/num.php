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

use zfx\StrFilter;

/**
 * @var \zfx\Num $value
 */

/**
 * @var \zfx\FieldViewNum $fv
 */

if (!is_a($value, '\zfx\Num')) {
    $value = new \zfx\Num($value);
}
echo StrFilter::HTMLencode($fv->getLocalizer()->getNum($value, $fv->getPrecision(), $fv->getSeparation()));