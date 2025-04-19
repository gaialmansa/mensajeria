<?php

use zfx\HtmlTools;

/** @var bool $_showDevMode */
/** @var bool $_showTranspose */
?>
<div class="zbfCard" xmlns="http://www.w3.org/1999/html">
    <div class="zbfCardTitle">Opciones</div>
    <div class="zbfCardBody">
        <div class="zbfCardContent">
            Formato: <?php echo HtmlTools::selectElement($_formats, '_format', $_format); ?>
        </div>
        <div class="zbfCardContent">
            Acción: <?php echo HtmlTools::selectElement($_actions, '_action', $_action); ?>
        </div>
        <?php if ($_showTrans) { ?>
            <div class="zbfCardContent">
                <input type="checkbox" name="_trans" value="checked">Transponer</input>
            </div>
        <?php } ?>
        <?php if ($_showDevMode) { ?>
            <div class="zbfCardContent">
                <input type="checkbox" name="_dev" value="checked">Modo desarrollo</input>
            </div>
        <?php } ?>
    </div>
</div>