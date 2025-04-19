<?php
/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */

namespace zfx;

class GisTools
{

    public static function exif2Pos($rutaImagen)
    {
        if (is_readable($rutaImagen)) {
            $data = @exif_read_data($rutaImagen);
            if (is_array($data) && array_key_exists('GPSLatitude', $data)) {
                $pos = new GisPos(
                    self::exifcoords2float($data["GPSLatitude"], $data['GPSLatitudeRef']),
                    self::exifcoords2float($data["GPSLongitude"], $data['GPSLongitudeRef'])
                );
                return $pos;
            }
        }
    }


    // --------------------------------------------------------------------

    /**
     * Directo from StackOverflow!!!!!! No veas la prisa que llevo
     * @param $coordinate
     * @param $hemisphere
     * @return float|int
     */
    static function exifcoords2float($coordinate, $hemisphere)
    {
        if (is_string($coordinate)) {
            $coordinate = array_map("trim", explode(",", $coordinate));
        }
        for ($i = 0; $i < 3; $i++) {
            $part = explode('/', $coordinate[$i]);
            if (count($part) == 1) {
                $coordinate[$i] = $part[0];
            }
            else {
                if (count($part) == 2) {
                    $coordinate[$i] = floatval($part[0]) / floatval($part[1]);
                }
                else {
                    $coordinate[$i] = 0;
                }
            }
        }
        list($degrees, $minutes, $seconds) = $coordinate;
        $sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;
        return $sign * ($degrees + $minutes / 60 + $seconds / 3600);
    }

    // --------------------------------------------------------------------

}
