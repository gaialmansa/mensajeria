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
 * @var array $action
 */

/**
 * @var string $language
 */

/**
 * @var mixed $moreData ;
 */

use zfx\HtmlTools;
use zfx\TableActions;
use function zfx\a;
use function zfx\aa;

if (array_key_exists('target', $action)) {
    $attrTarget = "target=\"{$action['target']}\"";
}
else {
    $attrTarget = '';
}

$class    = a($action, 'class');
$attrData = HtmlTools::dataAttr(a($action, 'data'));
$url      = a($action, 'url');

?>
<div class="_action <?php echo $class; ?>">
    <a <?php echo $attrData; ?> <?php echo $attrTarget; ?> class="_actionButton"
                                                           href="<?php echo $url; ?>"><?php
        echo aa($action, $language, 1) . aa($action, $language, 0);
        ?></a><?php
    if (a($action, 'menu')) {
        ?>
        <div class="_subactions"><?php
            foreach (a($action, 'menu') as $subAction) {
                TableActions::renderAction($subAction, $language, $moreData);
            } ?></div>
    <?php } ?></div>
