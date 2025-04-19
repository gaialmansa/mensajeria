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
 * @package data-source
 */

namespace zfx;

/**
 * AutoSchemaBase is the abstract base class to AutoSchema
 *
 * Provides an Schema class that can build itself from database catalog.
 */
abstract class AutoSchemaBase extends Schema
{

    /**
     * DB connection profile
     * @var string $profile
     */
    protected $profile;

    /**
     * Almacenar la info en bruto aquí, cuando se pide
     * @var
     */
    public $raw;

    // --------------------------------------------------------------------

    /**
     * Contructor
     *
     * @param string $relationName Table name
     * @param string $profile Database connection profile
     */
    public function __construct($relationName, $profile = NULL, $raw = FALSE)
    {
        parent::__construct();
        $this->setRelationName($relationName);
        if (((string)$profile) == '') {
            $this->profile = Config::get('dbProfile');
        }
        else {
            $this->profile = $profile;
        }
        $this->autoConstruct($raw);
    }
    // --------------------------------------------------------------------

    /**
     * Automatic self construction
     *
     * Initializes object and builds from the system catalog by loading
     * the table definition.
     */
    protected abstract function autoConstruct($raw = FALSE);

    // --------------------------------------------------------------------

    /**
     * @return mixed|string|null
     */
    public function getProfile()
    {
        return $this->profile;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed|string|null $profile
     */
    public function setProfile($profile)
    {
        $this->profile = (string)$profile;
    }

    // --------------------------------------------------------------------


}
