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

?>
<!DOCTYPE html>
<html lang="<?php echo $_lang; ?>">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no"/>
    <link rel="apple-touch-icon" sizes="180x180" href="/res/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/res/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/res/favicon/favicon-16x16.png">
    <link rel="manifest" href="/res/favicon/site.webmanifest">
    <link href="<?php echo Config::get('rootUrl'); ?>res/zaf/<?php echo Config::get('admTheme'); ?>/css/login.css" type="text/css" rel="stylesheet">
    <?php if (isset($_title)) { ?>
        <title><?php echo $_title; ?></title>
    <?php } ?>
    <script>
        function setFocus() {
            document.getElementById("login").focus();
        }
    </script>
</head>
<body onload="setFocus()">
<form method="post">
    <div class="_loginwrapper1">
        <div class="_loginwrapper2">
            <div class="_login">
                <div class="_loginLogoContainer">
                    <img class="zbfLoginLogo" src="<?php echo Config::get('appLoginLogoUrl'); ?>" alt="Logo">
                </div>
                <div class="_loginLogoSeparator"></div>
                <div class="_loginFormContainer">
                    <h1>Por favor identifíquese</h1>
                    <div class="_element">
                        <div class="_label">Nombre</div>
                        <div class="_field"><input id="login" type="text"
                                                   name="login"/></div>
                    </div>
                    <div class="_element">
                        <div class="_label">Contraseña</div>
                        <div class="_field"><input type="password"
                                                   name="password"/></div>
                    </div>
                    <br/>
                    <button type="submit">Entrar</button>
                </div>
            </div>
            Powered by<br>
            <img class="zbfLoginBottomLogo" style=" "
                 src="<?php echo Config::get('appLoginLogoBottomUrl'); ?>"
                 alt="Logo">
        </div>
    </div>
</form>
</body>
<!-- Entorno: [<?php echo \zfx\Config::get('entorno'); ?>] -->
<!-- Instalación: [<?php echo \zfx\Config::get('inst'); ?>] -->
</html>
