<?php
$_loc = new \zfx\Localizer();
?>
<div class="zbfCard">
    <div class="zbfCardTitle">Rango de fechas</div>
    <div class="zbfCardBody">
        <div class="zbfCardContent">
            Fecha inicial:
            <input class="zjDatePicker" type="text" name="<?php echo $_varStartDate; ?>" value="<?php echo $_loc->getDate($_valStartDate); ?>">
        </div>
        <div class="zbfCardContent">
            Fecha final:
            <input class="zjDatePicker" type="text" name="<?php echo $_varEndDate; ?>" value="<?php echo $_loc->getDate($_valEndDate); ?>">
        </div>
    </div>
</div>
