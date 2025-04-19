<div class="zbfBox">
    <h1><?php if (isset($_title)) echo $_title; ?></h1>
    <form target="_blank" class=_submitOnce" method="post" action="<?php echo $_target; ?>">
        <?php $this->section('blocks'); ?>
        <?php $this->section('options'); ?>
        <button class="zbfCardButton" type="submit">Generar</button>
    </form>
</div>