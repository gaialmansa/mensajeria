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
use function zfx\a;
use function zfx\trueEmpty;

$id       = '';
$cssClass = '';
$datas    = HtmlTools::dataAttr($context->getDatas());
if (!trueEmpty($context->getId())) {
    $id = "id=\"{$context->getId()}\"";
}
if (!trueEmpty($context->getCssClass())) {
    $cssClass = "class=\"{$context->getCssClass()}\"";
}
?>
<div class="zbfContext zbfContext_<?php echo $context->getType(); ?>" <?php echo "$id $cssClass $datas"; ?>>
    <?php if ($context->hasRemove()) { ?>
        <a href="<?php echo $context->getRemove(); ?>"><span
                    class="fas fa-times"></span></a>
    <?php } ?>
    <span class="zbfContextTitle"><?php echo $context->getLabel(); ?></span>
    <?php if ($context->hasUrl()) { ?>
        <a href="<?php echo $context->getUrl(); ?>"><span
                    class="zbfContextValue"><?php echo $context->getValue(); ?></span></a>
    <?php } else { ?>
        <span class="zbfContextValue"><?php echo $context->getValue(); ?></span>
    <?php } ?>
    <?php if ($context->hasMenu()) { ?>
        <a class="zjContextMenuButton" href="javascript:void(0)"
           data-adm-ctxt="zjContextMenu_<?php echo $context->getType(); ?>"><span
                    class="fas fa-chevron-circle-down"></span></a>
    <?php } ?>
    <?php if ($context->hasMenu()) { ?>
        <div class="zjContextMenu zjContextMenuHidden zjContextMenu_<?php echo $context->getType(); ?>">
            <?php foreach ($context->getMenu() as $item) {
                $url    = a($item, 'url');
                $label  = a($item, 'label');
                $data2  = HtmlTools::dataAttr(a($item, 'data'));
                $target = '';
                if (a($item, 'target')) {
                    $target = "target=\"{$item['target']}\"";
                }
                echo "<div class=\"zjContextMenuItem\"><a class=\"zjContextMenuItem\" href=\"$url\" $data2 $target>$label</a></div>";
            } ?>
        </div>
    <?php } ?>
</div>
