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
 * FieldView
 *
 * Base class for field wrappers designed for drawing and editing fields
 */
abstract class FieldView extends Localized
{

    /**
     * Reference of the field
     *
     * @var Field $field
     */
    protected $field;

    /**
     * Label or title
     *
     * @var string $label
     */
    protected $label;

    /**
     * Field is editable true/false
     *
     * @var boolean $editable
     */
    protected $editable;
    /**
     *
     * @var bool $displayOnly
     */
    protected $displayOnly;
    /**
     *
     * @var string $cssClass
     */
    private $cssClass;
    /**
     *
     * @var boolean $sortable
     */
    private $sortable;
    /**
     *
     * @var array $data
     */
    private $data;

    /**
     * @var callable
     */
    private $customColClasser = NULL;

    // --------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->field       = NULL;
        $this->label       = NULL;
        $this->editable    = FALSE;
        $this->cssClass    = '';
        $this->sortable    = TRUE;
        $this->data        = array();
        $this->displayOnly = FALSE;
    }
    // --------------------------------------------------------------------

    /**
     * Generate HTML code for drawing or editing a field.
     *
     * @param mixed $value The current field value is specified
     * @return string HTML code
     */
    public abstract function render($value, $packedPK = '');

    /**
     * Return field fixed own css class
     */
    public abstract function getOwnCssClass();


    /**
     * Obtener el valor PHP de un dato obtenido desde SQL
     *
     * @param $data
     */
    public function value($data)
    {
        /*
         * De forma predeterminada lo obtenemos a través de su field, pero en algunos FieldView lo cambiaremos.
         */
        $field = $this->field;
        if ($field) {
            return $field::value($data);
        }
        else {
            return $data;
        }
    }

    // --------------------------------------------------------------------

    public function toString($data)
    {
        /*
         * De forma predeterminada lo obtenemos a través de su field, pero en algunos FieldView lo cambiaremos.
         */
        $field = $this->field;
        if ($field) {
            return $field::toString($data, $this->getLocalizer());
        }
        else {
            return $data;
        }
    }

    // --------------------------------------------------------------------


    // --------------------------------------------------------------------
    // Access methods
    // --------------------------------------------------------------------

    /**
     * Get field reference
     *
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }
    // --------------------------------------------------------------------

    /**
     * Set field reference
     *
     * @param Field $value
     */
    public function setField(Field $value = NULL)
    {
        $this->field = $value;
    }
    // --------------------------------------------------------------------

    /**
     * Get field label (title)
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
    // --------------------------------------------------------------------

    /**
     * Set field label (title)
     *
     * @param string $value
     */
    public function setLabel($value)
    {
        $this->label = (string)$value;
    }
    // --------------------------------------------------------------------

    /**
     * Check if field is editable
     *
     * @return boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }
    // --------------------------------------------------------------------

    /**
     * Set field editability
     *
     * @param boolean $value
     */
    public function setEditable($value)
    {
        $this->editable = (boolean)$value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return string
     */
    public function getCssClass($req = FALSE)
    {
        if ($this->field && $this->field->getRequired() && $req) {
            $reqClass = 'zjFvReq ';
        }
        else {
            $reqClass = '';
        }
        return $reqClass . $this->cssClass;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $value
     */
    public function setCssClass($value)
    {
        $this->cssClass = (string)$value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return bool
     */
    public function getSortable()
    {
        return $this->sortable;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param bool $value
     */
    public function setSortable($value)
    {
        $this->sortable = (bool)$value;
    }

    // --------------------------------------------------------------------

    public function getData()
    {
        return $this->data;
    }

    // --------------------------------------------------------------------

    public function setData(array $data = NULL)
    {
        $this->data = $data;
    }

    // --------------------------------------------------------------------

    public function getDisplayOnly()
    {
        return $this->displayOnly;
    }

    // --------------------------------------------------------------------

    public function setDisplayOnly($value)
    {
        $this->displayOnly = (bool)$value;
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return '';
    }

    // --------------------------------------------------------------------

    /**
     * @return callable|null
     */
    public function getCustomColClasser(): ?callable
    {
        return $this->customColClasser;
    }

    // --------------------------------------------------------------------

    /**
     * @param callable|null $customColClasser
     */
    public function setCustomColClasser(?callable $customColClasser): void
    {
        $this->customColClasser = $customColClasser;
    }

    // --------------------------------------------------------------------

    public function isHidden()
    {
        return FALSE;
    }

}
