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

class GisPos
{
    // --------------------------------------------------------------------

    protected $lat;
    protected $lng;

    // --------------------------------------------------------------------

    /**
     * Constructor. Requiere coordenadas en decimal
     *
     * @param Num $lat Latitud
     * @param Num $lng Longitud
     */
    public function __construct($lat, $lng)
    {
        $this->lat = new Num($lat);
        $this->lng = new Num($lng);
    }

    // --------------------------------------------------------------------

//    /**
//     * Constructor estático en grados
//     *
//     * Devuelve una instancia de GisPos especificando la latitud y longitud en grados sexagesimales, minutos y segundos.
//     *
//     * @param $latD Latitud: grados
//     * @param $latM Latitud: minutos
//     * @param $latS Latitud: segundos
//     * @param $lngD Longitud: grados
//     * @param $lngM Longitud: minutos
//     * @param $lngS Longitud: segundos
//     * @return GisPos
//     */
//    public static function constructDMS(
//        $latD,
//        $latM,
//        $latS,
//        $lngD,
//        $lngM,
//        $lngS
//    ) {
//        $lat = MMat::dms2dec($latD, $latM, $latS);
//        $long = MMat::dms2dec($lngD, $lngM, $lngS);
//        return new MPunto($lat, $long);
//    }
//
//    // --------------------------------------------------------------------
//
//    /**
//     * Constructor estático en grados y puntos cardinales.
//     *
//     * Devuelve una instancia de Punto especificando la latitud y longitud en grados sexagesimales,
//     * minutos, segundos y la orientación mediante un punto cardinal.
//     *
//     * @param $latD Latitud: grados
//     * @param $latM Latitud: minutos
//     * @param $latS Latitud: segundos
//     * @param $latC Latitud: 'S' o 's' para sur; en cualquier otro caso es norte.
//     * @param $longD Longitud: grados
//     * @param $longM Longitud: minutos
//     * @param $longS Longitud: segundos
//     * @param $longC Longitud: 'O', 'o', 'W', 'w' para oeste; en cualquier otro caso es este.
//     * @return MPunto
//     */
//    public static function constructDMSC(
//        $latD,
//        $latM,
//        $latS,
//        $latC,
//        $longD,
//        $longM,
//        $longS,
//        $longC
//    ) {
//        $factorLat = 1.0;
//        if (strpos('Ss', $latC) !== false) {
//            $factorLat = -1.0;
//        }
//        $lat = MMat::dms2dec(abs($latD), $latM, $latS) * $factorLat;
//
//        $factorLong = 1.0;
//        if (in_array(strtoupper($longC),['O', 'W'])) {
//            $factorLong = -1.0;
//        }
//        $long = MMat::dms2dec(abs($longD), $longM, $longS) * $factorLong;
//        return new MPunto($lat, $long);
//    }

    // --------------------------------------------------------------------

    /**
     * Obtener la latitud en decimal
     *
     * @return Num
     */
    public function getLat()
    {
        return $this->lat;
    }

    // --------------------------------------------------------------------

    /**
     * Obtener la latitud en decimal expresada en radianes.
     *
     * @return Num
     */
    public function getLatR()
    {
        return (($this->lat->mul(M_PI))->div(180));
    }

    // --------------------------------------------------------------------

    /**
     * Obtener la longitud en decimal
     *
     * @return Num
     */
    public function getLng()
    {
        return $this->lng;
    }

    // --------------------------------------------------------------------

    /**
     * Obtener la longitud en decimal expresada en radianes
     *
     * @return Num
     */
    public function getLngR()
    {
        return (($this->lng->mul(M_PI))->div(180));
    }

    // --------------------------------------------------------------------

    /**
     * Establecer la latitud (en decimal)
     */
    public function setLat($lat)
    {
        $this->lat->setVal($lat);
    }

    // --------------------------------------------------------------------

    /**
     * Establecer la longitud (en decimal)
     */
    public function setLng($lng)
    {
        $this->lng->setVal($lng);
    }

    // --------------------------------------------------------------------

    public static function createFromGeoJSON($obj)
    {
        if (isset($obj->type) && $obj->type == 'Point') {
            $lat = new \zfx\Num($obj->coordinates[1]);
            $lng = new \zfx\Num($obj->coordinates[0]);
            return new GisPos($lat, $lng);
        }
    }

    // --------------------------------------------------------------------

}
