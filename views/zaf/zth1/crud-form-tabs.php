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

if (isset($_tabList) && is_array($_tabList) && count($_tabList)) { ?>
    <div class="_tabBar">
        <?php foreach ($_tabList as $_tab => $_tabTitle) {
            $_classSelected = '';
            if (isset($_tabSelected) && $_tab == $_tabSelected) {
                $_classSelected = 'zjTabSelected';
            }
            ?>
            <a class="zjTabButton <?php echo $_classSelected; ?>"
               href="javascript:void(0)"
               data-tab="<?php echo $_tab; ?>"><?php echo $_tabTitle; ?></a>
        <?php } ?>
    </div>
    <?php foreach ($_tabList as $_tab => $_tabTitle) {
        $_classSelected = '';
        if (isset($_tabSelected) && $_tab == $_tabSelected) {
            $_classSelected = 'zjTabSelected';
        }
        ?>
        <div class="zjTabPanel <?php echo $_classSelected; ?>"
             id="_tab_<?php echo $_tab; ?>">
            <?php if (isset($_tabForm) && $_tab != $_tabForm) {
                $this->section($_tab . '-header');
                $this->section($_tab);
                $this->section($_tab . '-footer');
            }
            else {
                $_submitFormSelector = '#' . $_submitFormID;
                if (!isset($_closeTargetSelector)) {
                    $_closeTargetSelector = $_submitTargetSelector;
                }
                $this->section($_tab . '-header');
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

                                ?><input type="hidden"
                                         name="<?php echo $name; ?>"
                                         value="<?php echo $value; ?>" /><?php
                            }
                        }
                        $this->section($_tab);
                        ?>
                        <div class="_fieldGroup"><?php
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
                                       data-adm-submit-target="<?php echo $_submitTargetSelector; ?>" <?php echo $dataOptions; ?>/>
                            <?php } ?>
                            <?php
                            if ($_showCloseButton) {
                                ?>
                                <input type="button"
                                       value="<?php echo $_loc->getString('adm', 'button-cancel'); ?>"
                                       class="zjFormCancelButton"
                                       data-adm-action="close"
                                    <?php echo HtmlTools::dataAttr($_cancelDatas); ?>
                                       data-adm-close-target="<?php echo $_closeTargetSelector; ?>"/>
                            <?php } ?>
                        </div>
                    </form>
                </div>

                <?php $this->section($_tab . '-footer'); ?>
            <?php } ?></div>
    <?php }
} ?>
