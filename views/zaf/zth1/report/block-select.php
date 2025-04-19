<div class="zbfCard">
    <div class="zbfCardTitle"><?php echo $_selTitle; ?></div>
    <div class="zbfCardBody">
        <div class="zbfCardContent"><?php echo $_selLabel; ?>&nbsp;<?php
            $class = '';
            if (is_countable($_valList) && count($_valList) > 8) $class = 'zjExtSelect';
            echo \zfx\HtmlTools::selectElement($_valList, $_varValue, $_value, FALSE, '', $class);
            ?>
        </div>
    </div>
</div>
