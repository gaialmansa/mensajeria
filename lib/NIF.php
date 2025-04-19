<?php

/**
 * Funciona con Unicode y sus subconjuntos como LATIN1 e incluso ASCII.
 */
class NIF
{

    private $numero;

    // --------------------------------------------------------------------

    public function __construct($num = '')
    {
        $this->numero = '';
        $this->est($num);
    }

    // --------------------------------------------------------------------

    public function est($num = '')
    {
        $num = (string)$num;

        // Solo se admiten letras y números
        $num = preg_replace(array('%[^a-z0-9]%siu'), array(''), $num);

        // Si está vacío, fin.
        if ($num == '') {
            $this->numero = '';
            return;
        }

        // Solo en mayúsculas
        $num = strtoupper($num);

        // Si la primera letra es un carácter, solo se comprobará la longitud máxima y el formato.
        if (preg_match('/^[A-Z]/', $num)) {
            if (strlen($num) == 9) {
                $this->numero = $num;
            }
            else {
                $this->numero = '';
            }
            return;
        }

        // Si no es un carácter lo tomaremos como número de DNI y calcularemos siempre su letra.
        preg_match('/([0-9]+)/', $num, $res);
        $valor        = intval($res[1], 10);
        $dc           = array(
            0 => 'T',
            'R',
            'W',
            'A',
            'G',
            'M',
            'Y',
            'F',
            'P',
            'D',
            'X',
            'B',
            'N',
            'J',
            'Z',
            'S',
            'Q',
            'V',
            'H',
            'L',
            'C',
            'K',
            'E'
        );
        $this->numero = sprintf("%08d%s", $valor, $dc[($valor % 23)]);
    }

    // --------------------------------------------------------------------

    public static function filtrar($numero)
    {
        $nif = new self($numero);
        return $nif->obt();
    }

    // --------------------------------------------------------------------

    public function obt()
    {
        return $this->numero;
    }
    // --------------------------------------------------------------------
}
