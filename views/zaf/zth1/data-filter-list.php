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
use function zfx\trueEmpty;

// Plantilla para filtrar usando  [x] campo [  valores v ]

/** @var string $attrData */
/** @var string $attrChecked */
/** @var string $selectedClass */
/** @var string $selectClass */
/** @var \zfx\DataFilterList $df */
$groupId    = $df->groupId;
$lenGroupId = strlen($groupId);
?>
<div class="_dhf_form zjDhContainer <?php echo $df->getClass(); ?> <?php echo $selectedClass; ?>">
    <form action="<?php echo $df->getAction(); ?>"
          id="<?php echo $df->getFormID(); ?>"
          class="<?php echo $df->getClass(); ?>">
        <input type="hidden" name="id" value="<?php echo $df->getId(); ?>"/>
        <div class="zjFilterList_check">
            <input type="checkbox" name="col"
                   id="zjFilterList_check_<?php echo StrFilter::safeEncode($df->getCol()); ?>"
                   value="<?php echo StrFilter::safeEncode($df->getCol()); ?>" <?php echo $attrData; ?>  <?php echo $attrChecked; ?>><?php echo $df->getLabel(); ?>
        </div>
        <div class="_dhf_dropdown">
            <select class="zjFilterList_select <?php echo $selectClass; ?>"
                    data-placeholder="Seleccionar" name="text"
                    id="zjFilterList_select_<?php echo StrFilter::safeEncode($df->getCol()); ?>" <?php echo $attrData; ?>><?php
                if (trueEmpty($df->getSelected())) {
                    echo "<option value=\"\" selected=\"true\"></option>";
                }
                else {
                    echo "<option value=\"\"></option>";
                }
                if ($df->getValues()) {
                    $optionOpened = FALSE;
                    foreach ($df->getValues() as $k => $v) {
                        // ¿Es un optgroup?
                        if ($groupId != '' && substr($k, 0, $lenGroupId) == $groupId) {
                            if ($optionOpened) {
                                echo "</optgroup>";
                            }
                            $optGroupLabel = StrFilter::HTMLencode($v);
                            echo "<optgroup label=\"$optGroupLabel\">";
                            $optionOpened = TRUE;
                        }
                        // Es una opción normal
                        else {
                            if (!trueEmpty($df->getSelected()) && (string)$df->getSelected() === (string)$k) {
                                $attrSelected = 'selected="true"';
                            }
                            else {
                                $attrSelected = '';
                            }
                            $sValue = StrFilter::HTMLencode($k);
                            echo "<option value=\"$sValue\" $attrSelected>$v</option>";
                        }
                    }
                }
                // Por último, si había una optionGroup abierta, la cerramos
                if ($optionOpened) {
                    echo "</optgroup>";
                }
                ?></select>
        </div>
    </form>
</div>