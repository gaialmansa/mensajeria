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

use function zfx\va;

?>
<form autocomplete="off" <?php
echo (isset($_formAction) ? " action=\"$_formAction\"" : '') .
     (isset($_formName) ? " name=\"$_formName\"" : '') .
     (isset($_formID) ? " id=\"$_formID\"" : '') .
     (isset($_formMethod) ? " method=\"$_formMethod\"" : ' method="post"') .
     (isset($_formEncType) ? " enctype=\"$_formEncType\"" : ''); ?>
>
    <?php if (va($_formHidden)) {
        foreach ($_formHidden as $name => $value) {
            ?>
            <input type="hidden" name="<?php echo $name; ?>"
                   value="<?php echo $value; ?>"/>
            <?php
        }
    }
    ?>
    <?php $this->section('form'); ?>
</form>