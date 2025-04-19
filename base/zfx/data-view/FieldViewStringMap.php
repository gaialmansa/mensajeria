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
 * String Map Field View
 */
class FieldViewStringMap extends FieldViewFormElement
{

    /**
     * Forzar que se vea NULL incluso si el campo es requerido.
     * @var bool $forceNull
     */
    protected $forceNull;


    /**
     * Forzar que no se vea NULL incluso si el campo no es requerido.
     * @var false $forceNotNull
     */
    protected $forceNotNull;
    /**
     *
     * @var array $prefix
     */
    private $map;
    private $filterMap;

    private $extras;

    protected $disableExt = FALSE;


    /**
     * @var string If found, generate an option group.
     */
    protected $groupId;


    // --------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->map          = [];
        $this->filterMap    = [];
        $this->extras       = NULL;
        $this->forceNull    = FALSE;
        $this->forceNotNull = FALSE;
        $this->groupId      = Config::get('zafCrudSelectOptgroupPrefix');
    }

    // --------------------------------------------------------------------

    /**
     * To be used in child classes
     * @param FieldViewString $old
     * @return FieldViewStringMap
     */
    public static function promote(FieldViewString $old)
    {
        // This is really weird. PHP hasn't class type casting...
        $new = new static();
        $new->setCssClass($old->getCssClass());
        $new->setEditable($old->getEditable());
        $new->setDisplayOnly($old->getDisplayOnly());
        $new->setElementID($old->getElementID());
        $new->setElementName($old->getElementName());
        $new->setField($old->getField());
        $new->setLabel($old->getLabel());
        $new->set_localizer($old->getLocalizer());
        $new->setSortable($old->getSortable());
        $new->setInlineBackend($old->getInlineBackend());
        $new->setInlineEdit($old->isInlineEdit());
        return $new;
    }

    // --------------------------------------------------------------------

    public function getForceNull()
    {
        return $this->forceNull;
    }

    // --------------------------------------------------------------------

    public function setForceNull($value)
    {
        $this->forceNull = (bool)$value;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $this->renderView('stringmap', $value, ['pk' => $packedPK]);
    }

    // --------------------------------------------------------------------

    public function value($data)
    {
        return (string)a($this->map, $data);
    }

    // --------------------------------------------------------------------

    public function toString($data)
    {
        return (string)a($this->map, $data);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvStringMap';
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param array $value
     */
    public function setMap(array $value = NULL)
    {
        if ($value) {
            $this->map = $value;
        }
        else {
            $this->map = array();
        }
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 'map';
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    // --------------------------------------------------------------------

    /**
     *
     * @return array
     */
    public function getFilterMap()
    {
        if (!$this->filterMap) {
            return $this->map;
        }
        else {
            return $this->filterMap;
        }
    }

    // --------------------------------------------------------------------

    /**
     *
     * @param array $value
     */
    public function setFilterMap(array $value = NULL)
    {
        if ($value) {
            $this->filterMap = $value;
        }
        else {
            $this->filterMap = array();
        }
    }

    /**
     * @return bool
     */
    public function isDisableExt(): bool
    {
        return $this->disableExt;
    }

    /**
     * @param bool $disableExt
     */
    public function setDisableExt(bool $disableExt): void
    {
        $this->disableExt = $disableExt;
    }

    // --------------------------------------------------------------------

    /**
     * @return false
     */
    public function forceNotNull(): bool
    {
        return $this->forceNotNull;
    }

    // --------------------------------------------------------------------

    /**
     * @param false $forceNotNull
     */
    public function setForceNotNull(bool $forceNotNull): void
    {
        $this->forceNotNull = $forceNotNull;
    }

    // --------------------------------------------------------------------

    public function hasExtras()
    {
        return (boolean)$this->extras;
    }

    // --------------------------------------------------------------------

    public function setExtras(array $e = NULL)
    {
        $this->extras = $e;
    }

    // --------------------------------------------------------------------

    public function getExtras()
    {
        return $this->extras;
    }

    // --------------------------------------------------------------------

    public function getExtra($key)
    {
        return a($this->extras, $key);
    }

    // --------------------------------------------------------------------

}
