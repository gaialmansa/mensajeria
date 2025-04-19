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
use function zfx\a;
use function zfx\av;
use function zfx\trueEmpty;

/** @var \zfx\DataFilterList $df */
/** @var string $classActive */
/** @var string $attrData */

// First element will be always blank
if (trueEmpty($df->getSelected())) {
    $attrSelected = 'selected="true"';
}
else {
    $attrSelected = '';
}

?>

<div class="_dhfr_form zjDhContainer <?php echo $df->getClass(); ?> <?php echo $classActive; ?>">
    <form action="<?php echo $df->getAction(); ?>"
          id="<?php echo $df->getFormID(); ?>"
          class="<?php echo $df->getClass(); ?>">
        <input type="hidden" name="id" value="<?php echo $df->getId(); ?>">
        <div class="_dhfr_labelIn"><?php echo $df->getInLabel(); ?></div>
        <div class="_dhfr_dropdown">
            <select name="col"
                    class="zjFilterRange_select" <?php echo $attrData; ?>>
                <option value="" <?php echo $attrSelected; ?>
                        data-jsh="null"></option><?php
                foreach ($df->getFields() as $value => $label) {
                    $sValue = StrFilter::safeEncode($value);
                    if (!trueEmpty($df->getSelected()) && $df->getSelected() == $value) {
                        $attrSelected = 'selected="true"';
                    }
                    else {
                        $attrSelected = '';
                    }
                    if (av($df->getJsh(), $value)) {
                        $attrJsh = 'data-jsh="' . a($df->getJsh(), $value) . '"';
                    }
                    else {
                        $attrJsh = '';
                    }
                    echo "<option value=\"$sValue\" $attrSelected $attrJsh>" . StrFilter::HTMLencode($label) . "</option>";
                }
                ?></select>
        </div>
        <div class="_dhfr_boxFrom"><input
                    placeholder="<?php echo $df->getFromLabel(); ?>" type="text"
                    name="textFrom"
                    class="zjFilterRange_input <?php echo $df->getEditClass(); ?>"
                    value="<?php echo $df->getSearchFromValue(); ?>" <?php echo $attrData; ?>>
        </div>
        <div class="_dhfr_boxTo"><input
                    placeholder="<?php echo $df->getToLabel(); ?>" type="text"
                    name="textTo"
                    class="zjFilterRange_input <?php echo $df->getEditClass(); ?>"
                    value="<?php echo $df->getSearchToValue(); ?>" <?php echo $attrData; ?>>
        </div>
    </form>
</div>

