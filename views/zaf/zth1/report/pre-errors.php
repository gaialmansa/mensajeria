<?php

use zfx\StrFilter;

/** @var $errors string */

?>
<div class="zbfBox zbfBlockBox">
    <h1>Informe no disponible</h1>
    <h2>Motivos:</h2>
    <div class="_msg _log"><?php echo StrFilter::HTMLencode($errors); ?></div>
</div>