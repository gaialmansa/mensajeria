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

use zfx\Config;

if (isset($varList)) { ?>
    <script src="<?php echo Config::get('rootUrl'); ?>res/zaf/share/js/Vars.js"
            type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        <?php foreach ($varList as $name => $value) { ?>
        zfx.Vars.<?php echo $name; ?> = <?php echo json_encode($value); ?>;
        <?php } ?>
    </script>
<?php }