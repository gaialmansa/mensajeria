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

abstract class FieldViewFormElement extends FieldView
{

    /**
     *
     * @var string $elementID
     */
    private $elementID;

    /**
     *
     * @var string $elementName
     */
    private $elementName;


    /**
     * @var bool
     */
    protected $inlineEdit;

    /**
     * @var string
     */
    protected $inlineBackend;

    // --------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->inlineEdit    = FALSE;
        $this->inlineBackend = '';
        $this->elementID     = NULL;
        $this->elementName   = NULL;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return string
     */
    public function getElementID()
    {
        return $this->elementID;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $value
     */
    public function setElementID($value)
    {
        $this->elementID = (string)$value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return string
     */
    public function getElementName()
    {
        return $this->elementName;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $value
     */
    public function setElementName($value)
    {
        $this->elementName = (string)$value;
    }
    // --------------------------------------------------------------------

    /**
     * Lanza una vista dentro de /zaf/XXXX/fieldviews/display|edit/NombreClase para mostrar el fieldview,
     * obteniendo el tema de ZAF y dependiendo si es solo mostrar o editar
     * @param $viewName string Normalmente es el nombre de la clase, pero no conviene autocalcularlo porque no siempre es así
     * @param $value
     * @param array|null $data
     */
    public function renderView($viewName, $value, array $data = NULL)
    {
        // Preparo los datos para la vista
        $minData = array(
            'value' => $value,
            'fv'    => $this
        );
        if (is_array($data)) {
            $viewData = array_merge($minData, $data);
        }
        else {
            $viewData = $minData;
        }

        // Lanzo vista
        if ($this->getDisplayOnly() && !$this->isInlineEdit()) {
            View::direct('zaf/' . Config::get('admTheme') . '/fieldviews/display/' . $viewName, $viewData);
        }
        else {
            View::direct('zaf/' . Config::get('admTheme') . '/fieldviews/edit/' . $viewName, $viewData);
        }

    }

    // --------------------------------------------------------------------

    public function attrRequired()
    {
        if ($this->field && $this->field->getRequired()) {
            return 'required';
        }
    }

    // --------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isInlineEdit(): bool
    {
        return (bool)$this->inlineEdit;
    }

    // --------------------------------------------------------------------

    /**
     * @param bool $inlineEdit
     */
    public function setInlineEdit($inlineEdit): void
    {
        $this->inlineEdit = (bool)$inlineEdit;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getInlineBackend(): string
    {
        return $this->inlineBackend;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $inlineBackend
     */
    public function setInlineBackend(string $inlineBackend): void
    {
        $this->inlineBackend = $inlineBackend;
    }

    // --------------------------------------------------------------------


}
