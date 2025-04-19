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

namespace zfx;

/**
 * Class FastInserter
 *
 * Esta clase ahora mismo no es más que un mero contenedor, pero esto podría
 * cambiar en un futuro.
 *
 * @package zfx
 */
class FastInserter
{
    /**
     * @var string Título, algo así como "Buscar y añadir"
     */
    protected $title;

    /**
     * @var int Número de parámetros. Por defecto es 0.
     */
    protected $paramNumber;

    /**
     * @var array Las etiquetas de los parámetros
     */
    protected $paramLabels;

    /**
     * @var array Los valores por defecto de los parámetros
     */
    protected $paramDefaultValues;

    /**
     * @var string La URL de un backend AJAX donde obtener los resultados de la búsqueda (a elegir uno).
     * Si está vacío no se hace ninguna búsqueda y se envía el texto de la búsqueda tal cual,
     * dejando a cargo del backend de inserción qué hacer con dicho texto.
     * Se espera que devuelva un <ul> a elegir un <li>.
     */
    protected $axSearchUrl;

    /**
     * @var string la URL de un backend AJAX donde realizar la inserción. El resultado del backend
     * será colocado en el target. Normalmente será una acción del CRUD; hará el insert y luego llamará a lst() del CRUD.
     */
    protected $axInsertUrl;

    /**
     * @var string el target donde el resultado del backend  axAddUrl aparecerá. Normamente será un div del tipo lstXXXX del crud.
     */
    protected $target;

    /**
     * @var false Si es verdadero se piden primero los parámetros y después se busca.
     */
    protected $paramsFirst;

    public function __construct()
    {
        $this->title              = "Buscar y añadir";
        $this->paramNumber        = 0;
        $this->paramLabels        = [];
        $this->paramDefaultValues = [];
        $this->axSearchUrl        = '';
        $this->axInsertUrl        = '';
        $this->target             = '';
        $this->paramsFirst        = FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    // --------------------------------------------------------------------

    /**
     * @return int
     */
    public function getParamNumber(): int
    {
        return $this->paramNumber;
    }

    // --------------------------------------------------------------------

    /**
     * @param int $paramNumber
     */
    public function setParamNumber(int $paramNumber): void
    {
        $this->paramNumber = $paramNumber;
    }

    // --------------------------------------------------------------------

    /**
     * @return array
     */
    public function getParamLabels(): array
    {
        return $this->paramLabels;
    }

    // --------------------------------------------------------------------

    /**
     * @param array $paramLabels
     */
    public function setParamLabels(array $paramLabels): void
    {
        $this->paramLabels = $paramLabels;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getAxSearchUrl(): string
    {
        return $this->axSearchUrl;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $axSearchUrl
     */
    public function setAxSearchUrl(string $axSearchUrl): void
    {
        $this->axSearchUrl = $axSearchUrl;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getAxInsertUrl(): string
    {
        return $this->axInsertUrl;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $axInsertUrl
     */
    public function setAxInsertUrl(string $axInsertUrl): void
    {
        $this->axInsertUrl = $axInsertUrl;
    }

    // --------------------------------------------------------------------

    public function render()
    {
        if (!trueEmpty($this->axInsertUrl)) {
            View::direct('zaf/' . Config::get('admTheme') . '/fast-inserter', ['me' => $this]);
        }
    }

    // --------------------------------------------------------------------

    public function renderResults($results)
    {
        View::direct('zaf/' . Config::get('admTheme') . '/fast-inserter-results', ['res' => $results]);
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $target
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    // --------------------------------------------------------------------

    /**
     * @return array
     */
    public function getParamDefaultValues(): array
    {
        return $this->paramDefaultValues;
    }

    // --------------------------------------------------------------------

    /**
     * @param array $paramDefaultValues
     */
    public function setParamDefaultValues(array $paramDefaultValues): void
    {
        $this->paramDefaultValues = $paramDefaultValues;
    }

    // --------------------------------------------------------------------

    /**
     * @return false
     */
    public function getParamsFirst(): bool
    {
        return $this->paramsFirst;
    }

    // --------------------------------------------------------------------

    /**
     * @param false $paramsFirst
     */
    public function setParamsFirst($paramsFirst): void
    {
        $this->paramsFirst = (bool)$paramsFirst;
    }

    // --------------------------------------------------------------------


}
