<?php

/**
 * Calendar common tools
 */
class CalTools
{

    public static function daysOfMonth($month, $year)
    {
        if ($month < 1 || $month > 12) {
            return 0;
        }
        if ($month == 2) {
            if (((fmod($year, 4) == 0) && (fmod($year, 100) != 0)) || (fmod($year, 400) == 0)) {
                return 29;
            }
            else {
                return 28;
            }
        }
        else {
            if (in_array($month, array(1, 3, 5, 7, 8, 10, 12))) {
                return 31;
            }
            else {
                return 30;
            }
        }
    }


    // --------------------------------------------------------------------

    public static function monthName($num = 0, $lang = 'es')
    {
        $months = array(
            1 => 'enero',
            'febrero',
            'marzo',
            'abril',
            'mayo',
            'junio',
            'julio',
            'agosto',
            'septiembre',
            'octubre',
            'noviembre',
            'diciembre'
        );
        if ($num > 0) {
            if ($num > 12) {
                $num = 12;
            }
            return $months[$num];
        }
        else {
            return $months;
        }
    }

    // --------------------------------------------------------------------

    public static function dayName($num = NULL, $lang = 'es')
    {
        $days = array(
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miércoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sábado',
            7 => 'domingo'
        );
        if (is_null($num)) {
            return $days;
        }
        else {
            if ($num > 7) {
                $num = 7;
            }
            if ($num < 0) {
                $num = 0;
            }
            return $days[$num];
        }
    }

    // --------------------------------------------------------------------

    /**
     * @param DateTime|null $date
     * @param false $ampliado
     * @return array
     */
    public static function expand(\DateTime $date = NULL, $ampliado = FALSE)
    {
        if ($date) {
            $y = $date->format('Y');
            $m = $date->format('n');
            $d = $date->format('j');
            $w = $date->format('W');
            if ($ampliado) {
                $t = ceil($m / 3);
                $c = ceil($m / 4);
                $s = ceil($m / 6);
                return [$y, $m, $d, $w, $t, $c, $s];
            }
            else {
                return [$y, $m, $d, $w];
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * @param DateTime|null $date
     * @return array
     */
    public static function expandTime(\DateTime $time = NULL)
    {
        if ($time) {
            $h = $time->format('H');
            $m = $time->format('i');
            $s = $time->format('s');
            return [$h, $m, $s];
        }
    }

    // --------------------------------------------------------------------

    /**
     * Calcula la diferencia en segundos entre dos fechas en valor absoluto
     * @param DateTime $date1
     * @param DateTime $date2
     * @return float|int
     */
    public static function secsBetweenDates(\DateTime $date1, \DateTime $date2)
    {
        return (int)(abs($date2->getTimestamp() - $date1->getTimestamp()));
    }

    // --------------------------------------------------------------------

    /**
     * Calcula la diferencia en minutos entre dos fechas en valor absoluto
     * @param DateTime $date1
     * @param DateTime $date2
     * @return float|int
     */
    public static function minsBetweenDates(\DateTime $date1, \DateTime $date2)
    {
        return (int)(abs($date2->getTimestamp() - $date1->getTimestamp()) / 60);
    }

    // --------------------------------------------------------------------

    /**
     * Devuelve un array cuya clave es el último mes del trimestre elegido
     * @param string $lang
     * @return string[]
     */
    public static function quarterMonths($lang = 'es')
    {
        $months = array(
            3  => 'T1',
            6  => 'T2',
            9  => 'T3',
            12 => 'T4',
        );
        return $months;
    }

    // --------------------------------------------------------------------

    /**
     * Convierte cierta cantidad de segundos a  nD hh:mm:ss siendo n los días.
     * @param $seconds
     * @param $returnText
     * @return array|string
     */
    public static function secs2DHMS($seconds, $returnText = FALSE)
    {
        $d = (int)($seconds / 86400);
        $h = (int)(($seconds - $d * 86400) / 3600);
        $m = (int)(($seconds - $d * 86400 - $h * 3600) / 60);
        $s = $seconds - $d * 86400 - $h * 3600 - $m * 60;
        if ($returnText) {
            return sprintf("%03uD %02u:%02u:%02u", $d, $h, $m, $s);
        }
        else {
            return ['d' => $d, 'h' => $h, 'm' => $m, 's' => $s];
        }
    }

    // --------------------------------------------------------------------


}
