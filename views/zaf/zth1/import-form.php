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

// Convenience
if (!isset($_submitFormSelector) || !isset($_submitFormID)) {
    $_submitFormID       = '_' . md5(uniqid('', TRUE));
    $_submitFormSelector = '#' . $_submitFormID;
}
else {
    $_submitFormID = substr(1, $_submitFormSelector);
}
?>

<div class="zbfBox">
    <h1>Importar</h1>
    <?php if ($type == 'excsv') { ?>
        <!--
    <h2>Desde formato CSV de Excel</h2>
    <p>Permite la importación a partir de un fichero CSV generado con Excel
        instalado en un sistema Windows con
        alfabetización español de España: el delimitador es el punto y coma
        (;), las líneas terminan con \r\n, las cadenas están entrecomilladas
        (""), los decimales usan coma (,) y el formato de fecha es
        DD/MM/AAAA.</p>
        -->
        <h2>Desde formato CSV</h2>
        <p>Permite la importación a partir de un fichero CSV: el delimitador es el punto y coma
            (;), las líneas terminan con \r\n, las cadenas están entrecomilladas
            (""), los decimales usan punto (.) y el formato de fecha es AAAA-MM-DD.</p>
    <?php } ?>
    <p>Las columnas pueden ir en cualquier orden.</p>
    <h2>Columnas disponibles</h2>
    <div class="_msg _mono"><?php echo \zfx\StrFilter::HTMLencode($campos); ?></div>
    <h2>Columnas requeridas</h2>
    <div class="_msg _mono"><?php echo \zfx\StrFilter::HTMLencode($camposReq); ?></div>
    <form autocomplete="off" id="<?php echo $_submitFormID; ?>" class="_noSend">
        <div class="zbfCard">
            <div class="zbfCardTitle">Subir fichero</div>
            <div class="zbfCardBody">
                <div class="zbfCardContent">
                    <input class="_fvReq" type="file" name="upfile" id="upfile">
                </div>
            </div>
        </div>
        <button class="zbfCardButton _formButtonCancel" data-adm-action="close"
                data-adm-close-target="<?php echo $_closeTargetSelector; ?>"><?php echo $_loc->getString('adm', 'button-cancel'); ?></button>
        <button class="zbfCardButton _formButtonSave" data-adm-action="submit"
                data-adm-submit-form="<?php echo $_submitFormSelector; ?>"
                data-adm-submit-action="<?php echo $_submitAction; ?>"
                data-adm-submit-target="<?php echo $_submitTargetSelector; ?>"><?php echo $_loc->getString('adm', 'button-import'); ?></button>
    </form>
</div>

