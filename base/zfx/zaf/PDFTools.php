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

/**
 * Class PDFTools
 *
 * Herramientas para realizar diferentes operaciones con ficheros PDF
 *
 * @package zfx
 */
class PDFTools
{
    /**
     * Convierte el fichero PDF a un grupo de imágenes JPG
     *
     * @param $pdfFile
     * @returns  array Lista ordenada de las imágenes resultantes.
     */
    static function pdf2jpg($pdfFile)
    {
        // Busquemos un directorio
        do {
            $destDir = '/tmp/' . 'zfx-pdf2jpg-' . uniqid() . '/';
        } while (file_exists($destDir));
        mkdir($destDir);
        if (!is_writable($destDir)) {
            return;
        }

        if (!is_readable($pdfFile)) {
            return;
        }


        @ob_start();
        $command = "nice -n 15 convert -density 200 -antialias -quality 80 {$pdfFile} {$destDir}pag-%03d.jpg";
        exec($command);
        @ob_end_clean();

        $sd     = scandir($destDir);
        $result = array();
        foreach ($sd as $d) {
            if ($d == '.') {
                continue;
            }
            if ($d == '..') {
                continue;
            }
            $result[] = $destDir . $d;
        }

        return $result;
    }

    // --------------------------------------------------------------------

    /**
     * Une varios ficheros en un único PDF
     *
     * Requiere 'pdfunite' proporcionado por Poppler
     *
     * @param $firstPdf El primer PDF a unir
     * @param array $arrayPdf El resto de PDF a unir
     * @param $output El nombre de fichero final
     * @return void
     */
    public static function pdfUnite($firstPdf, array $arrayPdf, $output)
    {
        $command = "nice -n 15 pdfunite $firstPdf " . implode(' ', $arrayPdf) . " $output";
        @ob_start();
        exec($command);
        @ob_end_clean();
    }

    // --------------------------------------------------------------------


}
