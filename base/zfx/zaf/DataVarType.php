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

class DataVarType
{
    const TYPE_STRING  = 1;
    const TYPE_INTEGER = 2;
    const TYPE_FLOAT   = 3;
    const TYPE_DATE    = 4;
    const TYPE_TIME    = 5;
    const TYPE_BOOL    = 6;
    const TYPE_FILE    = 7;
    const TYPE_IMAGE   = 8;
    const TYPE_SELECT  = 9;

    public static function getTypes()
    {
        return array(
            self::TYPE_STRING  => 'Texto',
            self::TYPE_INTEGER => 'Entero',
            self::TYPE_FLOAT   => 'Real',
            self::TYPE_DATE    => 'Fecha',
            self::TYPE_TIME    => 'Hora',
            self::TYPE_BOOL    => 'Sí/No',
            self::TYPE_FILE    => 'Subir fichero',
            self::TYPE_IMAGE   => 'Subir/tomar imagen',
            self::TYPE_SELECT  => 'Selección'
        );
    }

    // --------------------------------------------------------------------

    public static function optionsToArray($options)
    {
        $chunks = explode(',', $options);
        $output = [];
        foreach ($chunks as $chunk) {
            if (preg_match('%([^=]+)\s*=\s*([^=]+)%siu', StrFilter::spaceClear($chunk), $res)) {
                $output[$res[1]] = $res[2];
            }
        }
        return $output;
    }

    // --------------------------------------------------------------------

    public static function localizeChunk(Localizer $loc, $tipo, $opciones, $valor, $ruta = '')
    {
        switch ($tipo) {
            case self::TYPE_STRING:
            {
                $v = $valor;
                break;
            }
            case self::TYPE_FILE:
            {
                $n = FieldViewFile::extractNames($valor);
                $v = $ruta . a($n, 'stored');
                break;
            }
            case self::TYPE_IMAGE:
            {
                $n = FieldViewImage::extractNames($valor);
                $v = $ruta . a($n, 'stored');
                break;
            }
            case self::TYPE_INTEGER:
            {
                $n = new Num($valor);
                $v = $loc->getInteger($n->getVal());
                break;
            }
            case self::TYPE_FLOAT:
            {
                $n         = new Num($valor);
                $decimales = (int)$opciones;
                if ($decimales == 0) {
                    $decimales = 2;
                }
                $v = $loc->getNum($n, $decimales);
                break;
            }
            case self::TYPE_DATE:
            {
                $f = $loc->interpretDate($valor, TRUE);
                $v = $loc->getDate($f);
                break;
            }
            case self::TYPE_TIME:
            {
                $t = $loc->interpretTime($valor, TRUE);
                $v = $loc->getTime($t);
                break;
            }
            case self::TYPE_BOOL:
            {
                if (StrFilter::spaceClearAll($opciones) == 'si-no') {
                    $v = $loc->getBoolean((int)$valor);
                }
                else {
                    $v = ((int)$valor ? '☑' : '☐');
                }
                break;
            }
            case self::TYPE_SELECT:
            {
                $valores = self::optionsToArray($opciones);
                $v       = a($valores, $valor);
                break;
            }
        }
        return $v;
    }

    // --------------------------------------------------------------------

    public static function varTypeChange(\zfx\TableView $tableView, $varTypeField, $dbFileUrl, $loc, $varType, $options = '')
    {
        if (!$varType) {
            return;
        }
        switch ($varType) {
            case self::TYPE_FLOAT:
            {
                $newFieldView = new \zfx\FieldViewNum();
                $tableView->promote($varTypeField, $newFieldView);
                break;
            }
            case \zfx\DataVarType::TYPE_DATE:
            {
                $newFieldView = new \zfx\FieldViewDate();
                $tableView->promote($varTypeField, $newFieldView);
                break;
            }
            case \zfx\DataVarType::TYPE_TIME:
            {
                $newFieldView = new \zfx\FieldViewTime();
                $tableView->promote($varTypeField, $newFieldView);
                break;
            }
            case \zfx\DataVarType::TYPE_BOOL:
            {
                $tableView->stringMapFromArray($varTypeField, [0 => $loc->getBoolean(FALSE), 1 => $loc->getBoolean(TRUE)]);
                break;
            }
            case \zfx\DataVarType::TYPE_FILE:
            {
                $tableView->toFileField($varTypeField, $dbFileUrl);
                $tableView->getFieldView($varTypeField)->setKeepOriginalName(TRUE);
                break;
            }
            case \zfx\DataVarType::TYPE_IMAGE:
            {
                $tableView->toImageField($varTypeField, $dbFileUrl);
                $tableView->getFieldView($varTypeField)->setKeepOriginalName(TRUE);
                break;
            }
            case \zfx\DataVarType::TYPE_SELECT:
            {
                $tableView->stringMapFromArray($varTypeField, self::optionsToArray($options));
                break;
            }
        }
    }


    // --------------------------------------------------------------------


}