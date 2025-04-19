<?php

use zfx\StrFilter;

/** @var $errors string */

?>
<div class="zbfBox zbfBlockBox">
    <h1>No se puede generar el informe</h1>
    <h2>Problemas detectados:</h2>
    <div class="_msg _log"><?php echo StrFilter::HTMLencode($errors); ?></div>
</div>