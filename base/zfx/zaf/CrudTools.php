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

class CrudTools
{

    /**
     * Recibe un array con datos POST tal cual vienen de los formularios CRUD
     * y devuelve un array con las claves decodificadas y la clave primaria
     * (si existe) convertida correctamente.
     *
     * @param array|null $postData
     * @return array Datos decodificados
     * @see \Abs_AdmCrudController::getPostData()
     */
    public static function decodePostData(array $postData = NULL)
    {
        $res = [];
        if ($postData) {
            foreach ($postData as $key => $value) {
                if ($key === '_id') {
                    $res['_id'] = PKView::unpack($value);
                }
                else {
                    $res[StrFilter::safeDecode(substr($key, 1))] = $value;
                }
            }
        }
        return $res;
    }

    // --------------------------------------------------------------------

    public static function importFile($file, $type, \zfx\TableView $tableView)
    {
        // Algunas comprobaciones sencillas
        if (!($type == 'excsv' || $type == 'csv')) {
            return "ERROR\nNo se pudo realizar la importación\nTipo de fichero incorrecto";
        }

        // Importamos los datos.
        if ($type == 'excsv') {
            $p = new \SParser($file, "", 'Windows-1252', ';', '"', FALSE, TRUE);
        }
        else {
            $p = new \SParser($file, "", 'UTF-8', ',', '"', FALSE);
        }
        $p->firstLineFields();
        $num       = 0;
        $l         = 1;
        $problemas = '';
        while ($row = $p->getNextRecord()) {

            // Le quitamos la clave primaria si es automática
            if ($tableView->getTable()->getSchema()->pkIsAuto()) {
                $row = $tableView->getTable()->getSchema()->removeKeys($row);
            }
            $resultado = $tableView->getTable()->insertR($row);
            if ($resultado === FALSE) {
                $problemas .= "Problema en linea $l: Error indefinido.\n";
            }
            else {
                if (is_array($resultado)) {
                    $problemas .= "Problema en linea $l: Faltan las siguientes columnas: [" . implode(', ', $resultado) . "]\n";
                }
                else {
                    $num++;
                }
            }
            $l++;
        }
        if ($num > 0) {
            return "OK\nSe importaron $num filas\nErrores:\n" . $problemas;
        }
        else {
            return "ERROR\nSe importaron $num filas\nErrores:\n" . $problemas;
        }
    }


}
