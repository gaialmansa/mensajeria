<?php

include_once('Abs_AppAjaxController.php');

class Ctrl_ZfxAx extends Abs_AppAjaxController
{
    // --------------------------------------------------------------------

    public function sidepanel($hidden)
    {
        $_SESSION['_zfx_sidePanelHidden'] = !(boolean)$hidden;
    }

    // --------------------------------------------------------------------


}
