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

class TableExport
{
    public static function html($iterator, TableView $tableView, array $allowedFields = NULL, $view = FALSE)
    {
        if ($view) {
            $filepath = 'php://output';
        }
        else {
            $filepath = Config::get('downloadPath') . 'export-html-' . uniqid() . '.html';
        }
        $f = fopen($filepath, 'wb');
        fwrite($f, '<html><head><style>table,th,td {border: 1px solid black;border-collapse: collapse; font-family: sans-serif;}td,th{padding: 0.5em;}</style></head><body>');
        $row = $iterator->next();
        if ($row) {
            fwrite($f, '<table border="1"><thead><tr>');
            foreach ($row as $k => $v) {
                if ($allowedFields && !in_array($k, $allowedFields)) {
                    continue;
                }
                fwrite($f, '<th>');
                $fv = $tableView->getFieldView($k);
                if ($fv) {
                    fwrite($f, StrFilter::HTMLencode($fv->getLabel()));
                }
                fwrite($f, '</th>');
            }
            fwrite($f, '</tr></thead><tbody>');
        }
        while ($row) {
            fwrite($f, '<tr>');
            $rs = RecordSet::processRSTableViewString($row, $tableView);
            foreach ($rs as $k => $data) {
                if ($allowedFields && !in_array($k, $allowedFields)) {
                    continue;
                }
                fwrite($f, '<td>' . StrFilter::HTMLencode(print_r($data, TRUE)) . '</td>');
            }
            fwrite($f, '</tr>');
            $row = $iterator->next();
        }
        fwrite($f, '</body>');
        fclose($f);
        if ($view) {
            HttpTools::outputBinFile($filepath, 'text/html');
        }
        else {
            HttpTools::downloadBinFile($filepath, 'text/html');
        }

    }

    // --------------------------------------------------------------------

    public static function csv($iterator, TableView $tableView = NULL, array $allowedFields = NULL, $type = '', $importable = FALSE, $view = FALSE, $mapper = NULL)
    {
        $separator = ',';
        if ($type == 'excel') {
            $separator = ';';
        }

        if ($view) {
            if ($type == 'excel') {
                \zfx\HttpTools::csvHeaders('windows-1252');
            }
            else {
                \zfx\HttpTools::csvHeaders();
            }
            $filepath = 'php://output';
        }
        else {
            $filepath = Config::get('downloadPath') . 'export-csv-' . uniqid() . '.csv';
        }
        $f = fopen($filepath, 'wb');

        $row = $iterator->next();
        if ($row) {
            $header = array();
            foreach ($row as $k => $v) {
                if ($allowedFields && !in_array($k, $allowedFields)) {
                    continue;
                }
                // Aquí obtenemos las etiquetas o bien dejamos los IDs de los campos
                if (!$importable && $tableView) {
                    $fv = $tableView->getFieldView($k);
                    if ($fv) {
                        $header[] = $fv->getLabel();
                    }
                    else {
                        $header[] = '';
                    }
                }
                else {
                    if ($mapper && array_key_exists($k, $mapper)) {
                        $header[] = $mapper[$k];
                    }
                    else {
                        $header[] = $k;
                    }
                }
            }
            if ($type == 'excel') {
                $header = \zfx\ArrayTools::transcode('Windows-1252', $header);
            }
            self::_fputcsv($f, $header, $separator);
        }
        while ($row) {
            if (!$importable && $tableView) {
                $rs = RecordSet::processRSTableViewString($row, $tableView);
            }
            else {
                $rs = $row;
            }
            if ($allowedFields) {
                $rs = array_intersect_key($rs, array_flip($allowedFields));
            }
            if ($type == 'excel') {
                $rs = \zfx\ArrayTools::transcode('Windows-1252', $rs);
            }
            self::_fputcsv($f, $rs, $separator);
            $row = $iterator->next();
        }
        fclose($f);
        HttpTools::downloadBinFile($filepath, 'text/csv');

    }

    // --------------------------------------------------------------------

    public static function pdf($iterator, TableView $tableView, array $allowedFields = NULL, $view = FALSE)
    {
        $rep = new ReportTable();
        $rep->setOutputFormat($rep::FORMAT_PDF);
        $rep->setOutputName('export-pdf-' . uniqid());
        if ($view) {
            $rep->setForceDownload(FALSE);
        }
        else {
            $rep->setForceDownload(TRUE);
        }
        $rep->data['iterator']      = $iterator;
        $rep->data['tableView']     = $tableView;
        $rep->data['allowedFields'] = $allowedFields;
        $rep->iterations            = 2;
        $rep->landscape             = TRUE;
        $rep->templateOpen();
        if ($rep->templateWrite()) {
            $rep->templateClose();
            $rep->render();
        }
    }

    // --------------------------------------------------------------------

    public static function _fputcsv($handle, $fields, $delimiter = ",", $enclosure = '"', $escape_char = "\\", $record_seperator = "\r\n")
    {
        $result = [];
        foreach ($fields as $field) {
            $result[] = $enclosure . str_replace($enclosure, $escape_char . $enclosure, $field) . $enclosure;
        }
        return fwrite($handle, implode($delimiter, $result) . $record_seperator);
    }

    // --------------------------------------------------------------------

    /**
     * Exportación Excel nativa
     *
     * Confeccionamos un JSON que luego pasaremos a nuestro J2XLS hecho en python
     *
     * @param $iterator
     * @param TableView $tableView
     * @param array|NULL $allowedFields
     * @param TableView $tableViewImages
     * @return void
     */
    public static function exn($iterator, TableView $tableView, array $allowedFields = NULL, TableView $tableViewImages = NULL)
    {
        if (!$tableViewImages) $tableViewImages = $tableView;
        $filepath = Config::get('downloadPath') . 'export-exn-' . uniqid() . '.json';
        $f        = fopen($filepath, 'wb');
        $datos    = new \stdClass();
        $row      = $iterator->next();
        $sketches = [];
        if ($row) {
            // Estructura
            $datos->struct = [];
            foreach ($row as $k => $v) {
                if ($allowedFields && !in_array($k, $allowedFields)) {
                    continue;
                }
                $fv = $tableView->getFieldView($k);
                if ($fv) {
                    $field             = new \stdClass();
                    $field->label      = $fv->getLabel();
                    $pos               = (int)strrpos($class = get_class($fv->getField()), '\\');
                    $field->type       = substr($class, $pos + 1);
                    $pos               = (int)strrpos($class = get_class($fv), '\\');
                    $field->viewer     = substr($class, $pos + 1);
                    $datos->struct[$k] = $field;
                    // Si es una imagen, además, añadimos un nuevo campo con su URL
                    if ($field->viewer == 'FieldViewImage') {
                        $field2                          = clone $field;
                        $field2->label                   = 'URL ' . $field2->label;
                        $field2->type                    = '_url_img';
                        $field2->viewer                  = '_url_img';
                        $datos->struct['_url_img_' . $k] = $field2;
                    }
                    // Si es un sketch nos lo anotamos
                    if ($field->viewer == 'FieldViewSketch') {
                        $sketches[] = $k;
                    }
                }
            }
        }
        // Ahora los datos
        $datos->data = [];
        while ($row) {
            $record = [];
            $rs     = RecordSet::processRSTableViewString($row, $tableView);
            foreach ($rs as $k => $data) {
                if ($allowedFields && !in_array($k, $allowedFields)) {
                    continue;
                }
                // Si es una sketch, directamente damos su ruta completa
                if (in_array($k, $sketches)) {
                    $data = FieldViewSketch::getLocalFile($tableViewImages->getTable()->getSchema()->getRelationName(), $k, $data);
                }
                else {
                    // Si es una imagen, vamos a dar su ruta completa, y además una URL.
                    $hayImagen = FALSE;
                    if (array_key_exists('_url_img_' . $k, $datos->struct)) {
                        $hayImagen = TRUE;
                        $url       = FieldViewImage::getLocalUrl($tableViewImages->getTable()->getSchema()->getRelationName(), $k, $data);
                        $data      = FieldViewImage::getLocalFile($tableViewImages->getTable()->getSchema()->getRelationName(), $k, $data);
                    }
                }
                // Finalmente lo añadimos al registro
                $record[$k] = $data;
                if ($hayImagen) $record['_url_img_' . $k] = $url;
            }
            $datos->data[] = $record;
            $row           = $iterator->next();
        }
        fwrite($f, json_encode($datos, JSON_PRETTY_PRINT));
        fclose($f);

        // Convertir a excel
        $filepath2 = Config::get('downloadPath') . 'export-exn-' . uniqid() . '.xlsx';
        @ob_start();
        $command = "nice -n 15 json2xlsx.py '$filepath' '$filepath2'";
        exec($command);
        @ob_end_clean();
        HttpTools::downloadBinFile($filepath2, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    }

    // --------------------------------------------------------------------


}
