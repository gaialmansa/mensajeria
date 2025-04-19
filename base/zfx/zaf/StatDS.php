<?php
/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2024 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */

namespace zfx;

class StatDS
{
    public $maxPrefix = '';
    public $minPrefix = '';
    public $medPrefix = '';
    public $maxSuffix = 'max';
    public $minSuffix = 'min';
    public $medSuffix = 'med';


    public $num;

    const MIN = '-999999999999';
    const MAX = '999999999999';


    // --------------------------------------------------------------------

    public function __construct()
    {
        $this->max = [];
        $this->sum = [];
        $this->min = [];
        $this->med = [];
        $this->cnt = [];
        $this->num = 0;
    }

    // --------------------------------------------------------------------


    public function processRecord(array $r = NULL)
    {
        if (!$r) return;
        $this->num++;
        foreach ($r as $field => $value) {
            if (is_null($value)) continue;
            if (!array_key_exists($field, $this->max)) {
                $this->max[$field] = new Num(self::MIN);
                $this->min[$field] = new Num(self::MAX);
                $this->sum[$field] = new Num(0);
                $this->med[$field] = new Num(0);
                $this->cnt[$field] = new Num(1);
            }
            else {
                if ($this->max[$field]->lt($value)) {
                    $this->max[$field]->setVal($value);
                }
                if ($this->min[$field]->gt($value)) {
                    $this->min[$field]->setVal($value);
                }
                $this->sum[$field]->addMe($value);
                $this->cnt[$field]->addMe(1);
                $this->med[$field]->setVal($this->sum[$field]->div($this->cnt[$field]));
            }
        }
    }

    // --------------------------------------------------------------------

    public function getMmmRecord()
    {
        $res = [];
        foreach ($this->min as $field => $value) {
            if ($value->eq(self::MIN) || $value->eq(self::MAX))
                $value = NULL;
            $res[$this->minPrefix . $field . $this->minSuffix] = $value;
        }
        foreach ($this->med as $field => $value) {
            if ($value->eq(self::MIN) || $value->eq(self::MAX))
                $value = NULL;
            $res[$this->medPrefix . $field . $this->medSuffix] = $value;
        }
        foreach ($this->max as $field => $value) {
            if ($value->eq(self::MIN) || $value->eq(self::MAX))
                $value = NULL;
            $res[$this->maxPrefix . $field . $this->maxSuffix] = $value;
        }
        return $res;
    }

    // --------------------------------------------------------------------


}