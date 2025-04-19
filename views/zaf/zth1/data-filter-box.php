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

// Plantilla para BUSCAR _____________ en  [          v ]


use zfx\StrFilter;
use function zfx\av;
use function zfx\trueEmpty;

/**
 * @var \zfx\DataFilterBox $df
 */

/**
 * @var string $formID
 */

/**
 * @var string $attrData
 */

/**
 * @var string $classActive
 */

if (trueEmpty($df->selected)) {
    $attrSel = 'selected="true"';
}
else {
    $attrSel = '';
}

?>
<div class="_dhfb_form zjDhContainer <?php echo $df->getClass(); ?> <?php echo $classActive; ?>">
    <form action="<?php $df->getAction(); ?>" id="<?php echo $formID; ?>"
          class="<?php echo $df->getClass(); ?>">
        <input type="hidden" name="id" value="<?php echo $df->getId(); ?>">
        <div class="_dhfb_labelIn"><?php echo $df->getInLabel(); ?></div>
        <div class="_dhfb_dropdown">
            <select name="col"
                    class="zjFilterBox_select" <?php echo $attrData; ?>>
                <option value="" <?php echo $attrSel; ?>
                        data-jsh="null"></option><?php
                /*
                 * Construimos la lista de campos
                 */
                foreach ($df->fields as $value => $label) {
                    $sValue = StrFilter::safeEncode($value);
                    if (!trueEmpty($df->selected) && $df->selected == $value) {
                        $attrSel = 'selected="true"';
                    }
                    else {
                        $attrSel = '';
                    }
                    if (av($df->jsh, $value)) {
                        $attrJsh = "data-jsh=\"{$df->jsh[$value]}\"";
                    }
                    else {
                        $attrJsh = '';
                    }
                    ?>
                    <option
                    value="<?php echo $sValue; ?>" <?php echo $attrSel; ?><?php echo $attrJsh; ?>><?php echo StrFilter::HTMLencode($label); ?></option><?php
                }
                ?></select></div>
        <div class="_dhfb_box">
            <input type="text"
                   placeholder="<?php echo $df->getSearchLabel(); ?>"
                   name="text" class="zjFilterBox_input"
                   value="<?php echo $df->getSearchValue(); ?>" <?php echo $attrData; ?>>
        </div>
    </form>
</div>