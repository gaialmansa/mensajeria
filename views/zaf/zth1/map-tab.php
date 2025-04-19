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
<div id="<?php echo $idMap; ?>" class="zjMap"
     style="height: 100%; width: 100%;"></div>
<script type="text/javascript">
    var idMap = "<?php echo $idMap; ?>";
    var center = [<?php echo \zfx\Config::get('appMapDefaultCenter'); ?>];
    var zoom = <?php echo \zfx\Config::get('appMapDefaultZoom'); ?>;
    var tileUrl = "<?php echo \zfx\Config::Get('appMapDefaultTileUrl'); ?>";
    var saveUrl = "<?php echo $saveUrl; ?>";
    zfx.Map.<?php echo $idMap; ?> = new ZafMap(idMap, true, center, zoom, tileUrl, saveUrl);
</script>
