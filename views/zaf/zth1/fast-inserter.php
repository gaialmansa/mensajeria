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

/** @var $me \zfx\FastInserter */

$formId = '_f_' . md5(uniqid('form_', TRUE));
$divId  = '_d_' . md5(uniqid('div_', TRUE));

?>
<div class="zafFastInserter">
    <form method="POST" autocomplete="off" class="zafFastInserterForm zjNoSendKey" id="<?php echo $formId; ?>">
        <input type="hidden" name="id" value=""></input>
        <?php if (!$me->getParamsFirst()) {
            $tabindexlist = 2;
            ?>
            <div class="zafFastInserterTitle zafFastInserterItem">
                <?php echo $me->getTitle(); ?>
            </div>
            <div class="zafFastInserterSearch zafFastInserterItem">
                <input class="zjFastInserterSearchBox"
                       type="text"
                       name="search"
                       data-adm-fi-axsearch="<?php echo $me->getAxSearchUrl(); ?>"
                       data-adm-fi-form="#<?php echo $formId; ?>"
                       data-adm-fi-div="#<?php echo $divId; ?>"
                       tabindex="1">
            </div>
        <?php }
        if ($me->getParamNumber()) {
            for ($i = 1; $i <= $me->getParamNumber(); $i++) { ?>
                <div class="zafFastInserterLabel zafFastInserterItem">
                    <?php echo \zfx\a($me->getParamLabels(), $i); ?>
                </div>
                <div class="zafFastInserterInput zafFastInserterItem">
                    <input type="text" class="zafFastInserterParam" name="param<?php echo $i; ?>" value="<?php echo \zfx\a($me->getParamDefaultValues(), $i); ?>" tabindex="<?php echo $i + 100; ?>">
                </div>
            <?php }
        }
        if ($me->getParamsFirst()) {
            $tabindexlist = 201;
            ?>
            <div class="zafFastInserterTitle zafFastInserterItem">
                <?php echo $me->getTitle(); ?>
            </div>
            <div class="zafFastInserterSearch zafFastInserterItem">
                <input class="zjFastInserterSearchBox"
                       type="text"
                       name="search"
                       data-adm-fi-axsearch="<?php echo $me->getAxSearchUrl(); ?>"
                       data-adm-fi-form="#<?php echo $formId; ?>"
                       data-adm-fi-div="#<?php echo $divId; ?>"
                       tabindex="200">
            </div>
        <?php } ?>
        <div class="zafFastInserterButton zafFastInserterItem">
            <button data-adm-action="submit"
                    data-adm-submit-form="#<?php echo $formId; ?>"
                    data-adm-submit-action="<?php echo $me->getAxInsertUrl(); ?>"
                    data-adm-submit-target="<?php echo $me->getTarget(); ?>"
                    tabindex="300">Añadir
            </button>
        </div>
    </form>
    <?php if (!\zfx\trueEmpty($me->getAxSearchUrl())) { ?>
        <div class="zafFastInserterList" id="<?php echo $divId; ?>"></div>
    <?php } ?>
</div>





