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
use function zfx\va;

$this->section('header');
$_submitFormSelector = '#' . $_submitFormID;
if (!isset($_closeTargetSelector)) {
    $_closeTargetSelector = $_submitTargetSelector;
}
if ($_formExpanded) {
    $_formExpandedClass = "zbfFormContainerExpanded";
}
else {
    $_formExpandedClass = "";
}
?>
<div class="zbfFormContainer <?php echo $_formExpandedClass; ?>">
    <form autocomplete="off" id="<?php echo $_submitFormID; ?>"
          class="zjNoSendKey _crud">
        <?php
        if (va($_formHidden)) {
            foreach ($_formHidden as $name => $value) {

                ?><input type="hidden" name="<?php echo $name; ?>"
                         value="<?php echo $value; ?>" /><?php
            }
        }
        $this->section('body');
        ?>
        <div class="_fieldGroup">
            <?php
            if ($_showSaveButton) {
                $dataOptionsArray = array();
                if ($_disableOnSave) {
                    $dataOptionsArray[] = 'disable';
                }
                if ($_feedbackOnSave) {
                    $dataOptionsArray[] = 'feedback';
                }
                if ($dataOptionsArray) {
                    $dataOptions = 'data-adm-options="' . implode(' ', $dataOptionsArray) . '"';
                }
                else {
                    $dataOptions = '';
                }
                ?>
                <input type="button"
                       value="<?php echo $_loc->getString('adm', 'button-save'); ?>"
                       class="zjFormSaveButton"
                       data-adm-action="submit"
                       data-adm-submit-form="<?php echo $_submitFormSelector; ?>"
                       data-adm-submit-action="<?php echo $_submitAction; ?>"
                    <?php echo HtmlTools::dataAttr($_saveDatas); ?>
                    <?php if ($_noTarget === FALSE) { ?>
                        data-adm-submit-target="<?php echo $_submitTargetSelector; ?>"
                    <?php } ?>
                    <?php echo $dataOptions; ?>/>
            <?php } ?>
            <?php
            if ($_showCloseButton) { ?>
                <input type="button"
                       value="<?php echo $_loc->getString('adm', 'button-cancel'); ?>"
                       class="zjFormCancelButton"
                    <?php echo HtmlTools::dataAttr($_cancelDatas); ?>
                       data-adm-action="close"
                       data-adm-close-target="<?php echo $_closeTargetSelector; ?>"/>
            <?php } ?>
        </div>
    </form>
</div>
<?php $this->section('footer'); ?>
