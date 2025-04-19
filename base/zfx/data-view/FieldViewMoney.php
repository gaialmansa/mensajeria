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
class FieldViewMoney extends FieldViewString
{

    /**
     *
     * @var string $currencySymbol
     */
    private $currencySymbol;

    /**
     *
     * @var string $currencySeparator
     */
    private $currencySeparator;

    /**
     *
     * @var int $currencyPrecision ;
     */
    private $currencyPrecision;

    // --------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->currencySymbol    = Config::get('data-view_currencySymbol');
        $this->currencySeparator = Config::get('data-view_currencySeparator');
        $this->currencyPrecision = Config::get('data-view_currencyPrecision');
    }

    // --------------------------------------------------------------------

    /**
     *
     * @return string
     *
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $value
     */
    public function setCurrencySymbol($value)
    {
        $this->currencySymbol = (string)$value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return string
     *
     */
    public function getCurrencySeparator()
    {
        return $this->currencySeparator;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $value
     */
    public function setCurrencySeparator($value)
    {
        $this->currencySeparator = (string)$value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return integer
     */
    public function getCurrencyPrecision()
    {
        return $this->currencyPrecision;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param integer $value
     */
    public function setCurrencyPrecision($value)
    {
        $this->currencyPrecision = (int)$value;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $this->renderView('money', $value, ['pk' => $packedPK]);
    }

    // --------------------------------------------------------------------

    public function printCurrency()
    {
        if (!trueEmpty($this->currencySymbol)) {
            return $this->currencySeparator . $this->currencySymbol;
        }
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvMoney';
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 'f';
    }
    // --------------------------------------------------------------------
}
