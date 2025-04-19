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

use zfx\Context;
use function zfx\a;

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
    <?php $this->section('css'); ?>
    <?php $this->section('js'); ?>

    <?php if (isset($_title)) { ?>
        <title><?php echo $_title; ?></title>
    <?php } ?>
</head>
<body>
<?php if (a($_SESSION, '_zfx_sidePanelHidden') === TRUE || \zfx\Config::get('zafMobile')) {
    $panelClass = 'hidden';
}
else {
    $panelClass = '';
} ?>
<div class="zbfOverwrapper">
    <div class="zjNav <?php echo $panelClass; ?>">
        <?php $this->section('menu'); ?>
    </div>
    <div class="zjMain">
        <div class="zbfMainHeader">
            <div class="zbfIcons">
                <a class="zjToogleNavButton zbfIcon"
                   href="javascript:void(0)"><span
                            class="fas fa-bars"></span></a>
                <?php if (isset($_homeUrl)) { ?>
                    <a class="zbfIcon" href="<?php echo $_homeUrl; ?>"><span
                                class="fas fa-home"></span></a>
                <?php } ?>
            </div>
            <?php
            $ctxts = Context::getAll();
            if ($ctxts) {
                foreach ($ctxts as $c) {
                    $c->render();
                }
            }
            ?>
        </div>
        <div class="zbfBlockDesktop">
            <?php $this->section('body'); ?>
        </div>
    </div>
</div>
<div id="zjM"></div>
<script>

    $(document).ready(function () {
        $('.zjToogleNavButton').on('click', function () {
            $.ajax({
                url: zfx.Vars.rootUrl + 'zfx-ax/sidepanel/' + Number($('.zjNav').hasClass('hidden')),
                success: function () {
                    $('.zjNav').toggleClass('hidden');
                }
            });
        });
        $(document).on('mouseenter', '.zjNav',
            function () {
                if ($(this).hasClass('hidden')) {
                    $(this).removeClass('hidden');
                    $(this).addClass('stickout');
                }
            }
        );
        $(document).on('mouseleave', '.zjNav',
            function () {
                if ($(this).hasClass('stickout')) {
                    $(this).addClass('hidden').removeClass('stickout');
                }
            }
        );
    });
</script>
</body>
<!-- Entorno: [<?php echo \zfx\Config::get('entorno'); ?>] -->
<!-- Instalación: [<?php echo \zfx\Config::get('inst'); ?>] -->
</html>