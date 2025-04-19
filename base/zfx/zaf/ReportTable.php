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

/*
 * En un servidor Debian el usuario www-data que es el que habitualmente ejecuta Apache2 debe tener en su $HOME
 * los directorios .cache y .config con los permisos adecuados para que se pueda ejecutar libreoffice.
 */

namespace zfx;

class ReportTable extends LatexReport
{

    public function templateWrite()
    {
        $iterator      = $this->data['iterator'];
        $tableView     = $this->data['tableView'];
        $allowedFields = (array)$this->data['allowedFields'];

        /*
         * Vamos primero con la cabecera, aunque usaré la primera fila para obtener los campos que tenemos
         */
        $row = $iterator->next();


        $cols = array();
        if ($row) {
            foreach ($row as $k => $v) {
                if ($allowedFields && !in_array($k, $allowedFields)) {
                    continue;
                }
                $cols[] = $k;
            }
        }


        if ($row) {
            $header = array();
            $titles = array();
            foreach ($cols as $k) {
                $v  = a($row, $k);
                $fv = $tableView->getFieldView($k);
                if ($fv) {
                    $size = (int)$fv->getField()->getSize();
                    if ($size < 0 || ($size >= 128 && $size < 256)) {
                        $header[] = 'p{30mm}';
                    }
                    elseif ($size >= 256) {
                        $header[] = 'p{50mm}';
                    }
                    else {
                        $header[] = 'l';
                    }
                    $titles[] = '\\textbf{' . LatexTools::text($fv->getLabel()) . '}';
                }
                else {
                    return;
                }
            }

            $ltxHeader = implode(' ', $header);
            $ltxTitles = implode(' & ', $titles);
            $table     = //'{\footnotesize
                '\\begin{longtable}[l]{' . $ltxHeader . '}
\\toprule
' . $ltxTitles . ' \\\\
\\midrule
\\endhead
';
            fwrite($this->file, $table);
        }
        else {
            return FALSE;
        }


        /*
         * Ahora las filas
         */
        $body = '';
        while ($row) {
            $rs       = RecordSet::processRSTableViewString($row, $tableView);
            $contents = array();
            foreach ($cols as $k) {
                $content    = LatexTools::text(a($rs, $k));
                $contents[] = $content;
            }
            $body .= implode(' & ', $contents);
            $body .= ' \\\\' . "\n" . '\\midrule' . "\n";
            $row  = $iterator->next();
        }
        if ($body != '') {
            fwrite($this->file, $body);
        }


        fwrite($this->file, "\n" . '\\end{longtable}' . "\n");
        return TRUE;
    }

    // --------------------------------------------------------------------

}