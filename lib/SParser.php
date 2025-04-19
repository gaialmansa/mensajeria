<?php

/**
 * Sequential Data File Parser
 *
 * @author jorge
 */
class SParser
{

    protected $fh;
    protected $encoding;
    protected $ending;
    protected $separator;
    protected $fieldDefs;
    protected $fieldSize;
    protected $quote;
    protected $strip;
    protected $seq;

    /**
     * Constructor
     *
     * @param string $pathOrResource Complete file path and name
     *
     * @param string $ending Register ending character(s). Use:
     * - "\n" for Unix systems
     * - "\r\n" for Windows, DOS
     * Si está en blanco, se intentará autodetectar.
     *
     * @param string $encoding Encoding name. Common values are:
     * - 'UTF-8' (Linux, Mac and some Windows software)
     * - 'Windows-1252' (Windows common european)
     * - 'ISO-8859-1' (Very old Linux systems)
     * - 'ISO-8859-15' (Old Linux systems)
     * Note that internally UTF-8 is used.
     *
     * @param string $separator Field separator character(s). PECL Regex for split lines into fields. Use:
     * - ',', ';' for CSV files
     * - '' (empty string) if is a fixed-width tabular file
     * Use always the native file encoding.
     * Assume 's' modifier.
     *
     * @param string $quote Unquote fields surrounded with this single character
     *
     * @param boolean $strip If TRUE apply strip slashes to every field
     *
     * @param boolean $seq if TRUE use sequential algorithm for double quotes and quoted field separators instead of escaped chars (Excel CSVs use this)
     */
    function __construct(
        $pathOrResource,
        $ending = "\n",
        $encoding = 'UTF-8',
        $separator = '',
        $quote = '"',
        $strip = FALSE,
        $seq = FALSE
    )
    {

        if ($ending == '') {
            $this->ending = self::detectEOL($pathOrResource);
        }
        else {
            $this->ending = $ending;
        }

        if (is_resource($pathOrResource)) {
            $this->fh = $pathOrResource;
        }
        else {
            $this->fh = @fopen($pathOrResource, 'rb');
            if (!$this->fh) {
                throw new Exception('Unable to open file ' . $pathOrResource . '.');
            }
        }
        $this->fieldDefs = array();
        $this->fieldSize = 0;


        $this->encoding  = $encoding;
        $this->separator = $separator;
        $this->quote     = $quote;
        $this->strip     = $strip;
        $this->seq       = $seq;

        if ($this->ending == '') {
            throw new Exception('A record separator is required.');
        }
    }

    // --------------------------------------------------------------------

    function __destruct()
    {
        if ($this->fh) {
            @fclose($this->fh);
        }
    }

    // --------------------------------------------------------------------

    function addField($name, $width = 0)
    {
        $this->fieldDefs[$name] = (int)$width;
        $this->fieldSize        += (int)$width;
    }

    // --------------------------------------------------------------------

    function addFields(array $fields = NULL)
    {
        if ($fields) {
            if ($this->separator != '') {
                foreach ($fields as $name) {
                    $this->addField($name);
                }
            }
            else {
                foreach ($fields as $name => $width) {
                    $this->addField($name, (int)$width);
                }
            }
        }
    }

    // --------------------------------------------------------------------

    public function getFields()
    {
        return $this->fieldDefs;
    }

    // --------------------------------------------------------------------

    public function setFields(array $f)
    {
        $this->fieldDefs = $f;
        if ($f) {
            $w               = 0;
            $this->fieldSize = 0;
            foreach ($f as $size) {
                $this->fieldSize += (int)$size;
            }
        }
    }

    // --------------------------------------------------------------------

    function skipLines($num)
    {
        for ($i = 1; $i <= $num; $i++) {
            $this->getRecord();
        }
    }

    // --------------------------------------------------------------------

    function getNextRecord()
    {
        $rs = array();

        if (feof($this->fh)) {
            return $rs;
        }

        if (!$this->fieldDefs) {
            return $rs;
        }

        // Read record: let's choose the best way

        if ($this->separator === '') // No field separator: fixed width expected
        {
            if ($this->fieldSize === 0) {
                throw new Exception('No separator nor field size specified.');
            }

            if (substr($this->encoding, 0, 8) == 'Windows-' || substr($this->encoding, 0, 10) == 'ISO-8859-') {
                // Fixed length encoding
                $record = fread($this->fh, $this->fieldSize);
                if ($record === FALSE) {
                    throw new Exception('Read error.');
                }
                // pass the terminator
                if (!feof($this->fh)) {
                    fread($this->fh, strlen($this->ending));
                }
            }
            else {
                $record = $this->getRecord();
            }
            if (!$record) {
                return $rs;
            }

            // Split record in fields
            $i = 0;
            foreach ($this->fieldDefs as $name => $size) {
                $rs[$name] = mb_convert_encoding(mb_substr($record, $i, $size, $this->encoding), 'UTF-8',
                    $this->encoding);
                $i         += mb_strlen($rs[$name], 'UTF-8');
            }
            return $rs;
        }
        else // CSV
        {
            $record = $this->getRecord();
            if (!$record) {
                return $rs;
            }

            $a1 = array_keys($this->fieldDefs);
            $a2 = array_map(array($this, 'prepareFieldValue'), $this->recordSplit($record));
            if (count($a1) != count($a2)) {
                echo '<pre>';
                print_r($a1);
                print_r($a2);
                die;
            }

            $res = array_combine(array_keys($this->fieldDefs),
                array_map(array($this, 'prepareFieldValue'), $this->recordSplit($record)));
            return $res;
        }
    }

    // --------------------------------------------------------------------

    protected function recordSplit($record)
    {
        if (!$this->seq) {
            return preg_split("%{$this->separator}%s", $record);
        }
        else {
            return str_getcsv($record, $this->separator, $this->quote);
        }
    }


    // --------------------------------------------------------------------

    public function unquote($txt)
    {
        if ($this->quote) {
            if (substr($txt, 0, 1) == $this->quote && substr($txt, -1) == $this->quote) {
                return trim(substr($txt, 1, strlen($txt) - 2));
            }
        }
        return $txt;
    }

    // --------------------------------------------------------------------

    public function prepareFieldName($v)
    {
        $v = $this->unquote($v);
        return mb_convert_encoding($v, 'UTF-8', $this->encoding);
    }

    // --------------------------------------------------------------------

    public function prepareFieldValue($v)
    {
        $v = $this->unquote($v);
        if ($this->strip) {
            $v = stripslashes($v);
        }
        return mb_convert_encoding($v, 'UTF-8', $this->encoding);
    }

    // --------------------------------------------------------------------

    public function firstLineFields()
    {
        if ($this->separator === '') {
            return;
        }
        $record = $this->getRecord();
        if (!$record) {
            return;
        }
        $fields = array_map(array($this, 'prepareFieldName'), preg_split("%{$this->separator}%s", $record));
        if ($fields) {
            $this->addFields($fields);
        }
    }

    // --------------------------------------------------------------------

    public function getRecord()
    {
        $record = '';
        $term   = '';
        do {
            $character = fgetc($this->fh);
            if ($character != '') {
                $pos    = strpos($this->ending, (string)$character);
                $record .= $character;
            }
            else {
                $pos = FALSE;
            }
            if ($pos !== FALSE) {
                if (strlen($term) === $pos) {
                    $term .= $character;
                }
            }
        } while ($term != $this->ending && !feof($this->fh));
        if (($pos = strpos($record, $this->ending)) !== FALSE) {
            return substr($record, 0, $pos);
        }
        else {
            return $record;
        }
    }

    // --------------------------------------------------------------------

    public function reset()
    {
        rewind($this->fh);
    }

    // --------------------------------------------------------------------

    /**
     * Detectar si un fichero (presumiblemente un CSV) tiene terminación:
     * Windows/DOS = CRLF, 0D 0A, 13 10, \r\n
     * Linux/OSX = LF, 0A, 10, \n
     * MacOS = CR, 0D, 13, \r
     * @param $pathOrResource Ruta del fichero o bien un handle
     * @return string|boolean Devuelve la propia terminación o FALSE si no
     * se pudo leer o estaba vacío
     */
    public function detectEOL($pathOrResource)
    {
        if (is_resource($pathOrResource)) {
            $f = $pathOrResource;
        }
        else {
            $f = @fopen($pathOrResource, 'rb');
            if (!$f) {
                return FALSE;
            }
        }
        $b = fgetc($f);
        if ($b === FALSE) {
            fclose($f);
            return FALSE;
        } // Fichero vacío
        do {
            // Hemos encontrado un carácter que es algún tipo de nueva línea?
            if ($b == "\n" || $b == "\r") {
                // Obtengamos el siguiente carácter
                $n = fgetc($f);
                // Si el siguiente no existía (EOF), devolvemos el caracter que encontramos
                if ($n === FALSE) {
                    fclose($f);
                    return $b;
                }
                // Si el siguiente carácter era también es algún tipo de nueva línea, devolvemos ambos.
                elseif ($n == "\n" || $n == "\r") {
                    fclose($f);
                    return $b . $n;
                }
                // Si el siguiente carácter no era una nueva línea, devolvemos el carácter que encontramos.
                else {
                    fclose($f);
                    return $b;
                }
            }
            // El carácter que encontramos no era un tipo de nueva línea. Probemos con el siguiente.
            else {
                $b = fgetc($f);
            }
        } while ($b !== FALSE); // Y así hasta recorrer el fichero entero.
        fclose($f);
        return FALSE;
    }

    // --------------------------------------------------------------------

}
