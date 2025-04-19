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
 * @var string $value
 */

/**
 * @var \zfx\FieldViewString $fv
 */

if ($fv->getDisplayLength() == 0) {
    echo $value;
}
else {
    echo mb_substr($value, 0, $fv->getDisplayLength(), 'UTF-8') . (mb_strlen($value, 'UTF-8') > $fv->getDisplayLength() ? '...' : '');
}