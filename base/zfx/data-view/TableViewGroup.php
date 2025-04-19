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


/**
 * @package data-view
 */

namespace zfx;

class TableViewGroup
{
    /**
     * Field names of this group
     * @var array $fields
     */
    private $fields;

    public    $id;
    public    $expanded;
    public    $minHeight;
    public    $title;
    public    $info;
    protected $cols;

    // --------------------------------------------------------------------


    public function __construct(array $fields = NULL, $expanded = FALSE, $minHeight = '', $title = '', $cols = 0, $info = '', $id = '')
    {
        $this->setFields($fields);
        $this->expanded  = $expanded;
        $this->minHeight = $minHeight;
        $this->title     = $title;
        $this->info      = $info;
        $this->cols      = (int)$cols;
        $this->id        = $id;
    }

    // --------------------------------------------------------------------


    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    // --------------------------------------------------------------------

    /**
     * @param array $fields
     */
    public function setFields(array $fields = NULL)
    {
        if ($fields === NULL) {
            $this->fields = array();
        }
        else {
            $this->fields = $fields;
        }
    }

    // --------------------------------------------------------------------

    /**
     * @return mixed|null
     */
    public function getCols()
    {
        return $this->cols;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed|null $cols
     */
    public function setCols($cols)
    {
        $this->cols = (int)$cols;
    }

    // --------------------------------------------------------------------


}
