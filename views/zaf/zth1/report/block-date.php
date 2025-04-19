<?php
$_loc = new \zfx\Localizer();
?>
<div class="zbfCard">
    <div class="zbfCardTitle">Fecha</div>
    <div class="zbfCardBody">
        <div class="zbfCardContent">
            Fecha:
            <input class="zjDatePicker" type="text" name="<?php echo $_varDate; ?>" value="<?php echo $_loc->getDate($_valDate); ?>">
        </div>
    </div>
</div>
