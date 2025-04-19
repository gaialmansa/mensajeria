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

class CrudFieldDef
{
    // Se usan para SQL y también CRUD
    public $id;
    public $dataType;
    public $canBeNull    = TRUE;
    public $index        = FALSE;
    public $unique       = FALSE;
    public $primaryKey   = FALSE;
    public $fkTable      = '';
    public $fkCol        = '';
    public $fkRestrict   = TRUE;
    public $fkSuffix     = '';
    public $fkDesc       = '';
    public $fkPicker     = FALSE;
    public $notRelName   = FALSE;
    public $forceRelName = FALSE;

    // Se usan para el controlador CRUD
    public $label;
    public $group             = '';
    public $stringMap         = [];
    public $stringMapModel    = '';
    public $stringMapFunction = '';
    public $isImage           = FALSE;
    public $isFile            = FALSE;


    // --------------------------------------------------------------------

    protected function check()
    {
        if (trueEmpty($this->id)) {
            return "Necesito un [id].";

        }
        if (trueEmpty($this->id)) {
            return "Necesito un [id].";
        }

        return '';
    }

    // --------------------------------------------------------------------

    protected function getFkName($tableName)
    {
        return $tableName . '_rel_' . $this->fkTable . $this->fkSuffix;
    }

    // --------------------------------------------------------------------

    public function getColSQL($tableName)
    {
        $error = $this->check();
        if (!trueEmpty($error)) {
            \zfx\Debug::show($error);
            \zfx\Debug::show($this);
            die;
        }
        // nombre y tipo
        $sql = $this->id . " " . StrFilter::upperCase($this->dataType);

        // restricciones
        if ($this->primaryKey) {
            $sql .= " PRIMARY KEY";
        }
        if (!$this->canBeNull) {
            $sql .= " NOT NULL";
        }
        if ($this->unique) {
            $sql .= " UNIQUE";
        }

        // clave foránea
        if (!trueEmpty($this->fkTable) && !trueEmpty($this->fkCol) && !trueEmpty($this->fkDesc)) {
            $nombre = $this->getFkName($tableName);
            $sql    .= " CONSTRAINT $nombre REFERENCES {$this->fkTable} ({$this->fkCol}) ON UPDATE CASCADE";
            if ($this->fkRestrict) {
                $sql .= " ON DELETE RESTRICT";
            }
            else {
                $sql .= " ON DELETE SET NULL";
            }
        }

        return $sql;
    }

    // --------------------------------------------------------------------

    public function getIndexSQL($tableName)
    {
        $sql = "CREATE INDEX ON $tableName ({$this->id});";
        return $sql;
    }

    // --------------------------------------------------------------------

    public function getRelName($tableName)
    {
        $nombre = $this->getFkName($tableName);
        $code   = "\$this->relName('{$nombre}', '{$this->fkDesc}');";
        return $code;
    }

    // --------------------------------------------------------------------


}