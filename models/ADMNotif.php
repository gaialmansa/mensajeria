<?php

use zfx\Config;

/**
 * Notificaciones
 *
 * Class Notif
 */
class ADMNotif
{

    // --------------------------------------------------------------------

    /**
     * @param $address Dirección (string) o lista de direcciones (array)
     * @param $subject Asunto
     * @param $text Cuerpo del mensaje (Unicode, no HTML)
     * @param string $attachment Ruta de un fichero a adjuntar
     * @return bool Si todo sale bien.
     */
    public static function sendTXT($address, $subject, $text, $attachment = '')
    {
        if ($attachment != '') {
            if (!file_exists($attachment) || !is_readable($attachment)) {
                return FALSE;
            }
        }
        $smtp = new PHPMailer(FALSE);
        $smtp->isSMTP();
        $smtp->SMTPAuth   = TRUE;
        $smtp->Host       = Config::get('notifSMTP');
        $smtp->Username   = Config::get('notifSMTPUser');
        $smtp->Password   = Config::get('notifSMTPPass');
        $smtp->SMTPSecure = Config::get('notifSMTPSecure');
        $smtp->Port       = Config::get('notifSMTPPort');
        $smtp->setFrom(Config::get('notifEmail'), Config::get('notifFromName'));
        $smtp->isHTML(FALSE);
        $smtp->CharSet = 'UTF-8';
        if ($attachment != '') {
            $smtp->addAttachment($attachment);
        }
        if (is_array($address) && $address) {
            foreach ($address as $d) {
                $smtp->addAddress(\zfx\StrFilter::spaceClearAll($d));
            }
        }
        else {
            $smtp->addAddress($address);
        }
        $smtp->Subject = $subject;
        $smtp->Body    = $text;
        $res           = $smtp->send();
        return $res;
    }


    // --------------------------------------------------------------------


}
