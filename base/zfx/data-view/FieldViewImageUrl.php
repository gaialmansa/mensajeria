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
 * Image Url Field View
 *
 * This view assumes that a image url is contained
 */
class FieldViewImageUrl extends FieldViewString
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

    // --------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->prefix = '';
        $this->suffix = '';
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
        $new = new FieldViewImageUrl();
        $new->setDisplayOnly($old->getDisplayOnly());
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
        return $new;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $this->renderView('imageurl', $value);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvImageUrl';
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
}
