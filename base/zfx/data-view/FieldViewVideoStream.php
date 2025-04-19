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
 * Class FieldViewVideoStream
 *
 * Representa: Un campo con la URL de un stream MP4 que se puede representar en HTML5 directamente con
 * el tag <video>.
 *
 * @package zfx
 */
class FieldViewVideoStream extends FieldViewString
{

    /**
     *
     * @var string $prefix
     */
    private $prefix;

    /**
     *
     * @var string $suffix
     */
    private $suffix;

    /**
     * @var $height Altura del control de video en pixels
     */
    protected $height;

    /**
     * @var $width Ancho del control de video, en pixels
     */
    protected $width;

    // --------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->prefix = '';
        $this->suffix = '';
        $this->height = 120;
        $this->width  = 160;
    }

    // --------------------------------------------------------------------

    /**
     *
     * @param FieldViewString $old
     * @return FieldViewImageUrl
     */
    public static function promote(FieldViewString $old)
    {
        // This is really weird. PHP hasn't class type casting...
        $new = new self();
        $new->setCssClass($old->getCssClass());
        $new->setEditable($old->getEditable());
        $new->setElementID($old->getElementID());
        $new->setElementName($old->getElementName());
        $new->setField($old->getField());
        $new->setLabel($old->getLabel());
        $new->set_localizer($old->getLocalizer());
        $new->setMaxLength($old->getMaxLength());
        $new->setMinLength($old->getMinLength());
        $new->setSortable($old->getSortable());
        $new->setDisplayOnly($old->getDisplayOnly());
        return $new;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $this->renderView('videostream', $value);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvVideoStream';
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $value
     */
    public function setPrefix($value)
    {
        $this->prefix = (string)$value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $value
     */
    public function setSuffix($value)
    {
        $this->suffix = (string)$value;
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 's';
    }
    // --------------------------------------------------------------------

    /**
     * @return Altura
     */
    public function getHeight()
    {
        return $this->height;
    }

    // --------------------------------------------------------------------

    /**
     * @param Altura $height
     */
    public function setHeight($height)
    {
        $this->height = (int)$height;
    }

    // --------------------------------------------------------------------

    /**
     * @return Ancho
     */
    public function getWidth()
    {
        return $this->width;
    }

    // --------------------------------------------------------------------

    /**
     * @param Ancho $width
     */
    public function setWidth($width)
    {
        $this->width = (int)$width;
    }

    // --------------------------------------------------------------------

}
