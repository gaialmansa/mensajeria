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

/**
 * Table Viewer
 */
class RowViewer extends DataViewer
{

    private   $rs;
    protected $interDeps;

    // --------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->rs        = NULL;
        $this->interDeps = [];
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return array
     */
    public function getRS()
    {
        return $this->rs;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param array $value
     */
    public function setRS(array $value = NULL)
    {
        $this->rs = $value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return array
     */
    protected function calcViewData()
    {
        if ($this->tableView) {
            $data               = array();
            $data['_viewer']    = $this;
            $data['_data']      = $this->rs;
            $data['_interdeps'] = $this->interDeps;
            return $data;
        }
        else {
            return NULL;
        }
    }

    // --------------------------------------------------------------------

    public function setInterDeps(array $interdeps)
    {
        $this->interDeps = $interdeps;
    }
}
