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
?>
<div class="zbfBox">
    <?php if ($err != '') { ?>
        <h2 class="_error"><?php echo $err; ?></h2>
    <?php } ?>
    <?php if ($msg != '') { ?>
        <h2 class="_info"><?php echo $msg; ?></h2>
    <?php } else { ?>
        <h1>Cambiar contraseña</h1>
        <form method="post">
            <table class="struct">
                <tr>
                    <td><label for="c1">Por seguridad, introducir contraseña
                            actual:</label></td>
                    <td><input class="_xfvReq" id="c1"
                               name="c1"
                               type="password"></td>
                </tr>

                <tr>
                    <td><label for="c2">Introducir <b>nueva</b>
                            contraseña:</label></td>
                    <td><input autocomplete="off"
                               class="_xmirrorgroup_nc _xfvReq"
                               name="c2"
                               type="password"></td>
                </tr>
                <tr>
                    <td><label for="c3">Repetir <b>nueva</b> contraseña:</label>
                    </td>
                    <td><input autocomplete="off"
                               class="_xmirrorgroup_nc _xfvReq"
                               name="c3"
                               type="password"></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Cambiar"></td>
                    <td></td>
                </tr>
            </table>
            <script type="application/javascript">
                $(document).ready(function () {
                    $("#c1").focus();
                });
            </script>
        </form>
    <?php } ?>
</div>
