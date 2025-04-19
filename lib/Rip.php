<?php
ini_set('mbstring.substitute_character', "none");

class Rip
{

    /**
     * Write binary data to file
     *
     * @param string $fileName
     * @param string $binData
     */
    static function saveBin($fileName, $binData)
    {
        $f = fopen($fileName, 'wb');
        fwrite($f, $binData);
        fclose($f);
    }

    /**
     * Read binary data from file
     *
     * @param string $fileName
     * @return string
     */
    static function loadBin($fileName)
    {
        $f       = fopen($fileName, 'rb');
        $binData = fread($f, filesize($fileName));
        fclose($f);
        return $binData;
    }

    /**
     * Get HTTP code by URL
     *
     * @param string $url
     * @param string $method
     * @param mixed $params
     * @param array $curlOptions
     * @return boolean
     */
    static function getHtml($url, $method = 'GET', $params = NULL, array $curlOptions = NULL)
    {

        if ($method == 'GET' && ($params !== '' || (is_array($params) && $params))) {
            $query = (preg_match('%\?%', $url) ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
        }
        else {
            $query = '';
        }
        $ch = curl_init($url . $query);
        if (!$ch) {
            return FALSE;
        }
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            if ($params !== '' || (is_array($params) && $params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
        }
        if ($curlOptions) {
            curl_setopt_array($ch, $curlOptions);
        }

        $code = curl_exec($ch);
        curl_close($ch);
        return $code;
    }

    // --------------------------------------------------------------------

    static function downloadFile($url, $filename)
    {
        file_put_contents($filename, fopen($url, 'rb'));
    }
}
