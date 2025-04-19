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
 * Class ReportBlock
 *
 * Clase abstracta que sirve de base para bloques estándar que sirven como componentes en un controlador del tipo ZafReportForm.
 * @package zfx
 */
abstract class ReportBlock
{
    protected $data = [];

    // --------------------------------------------------------------------

    /**
     * Añade a la instancia de View proporcionada y en la sección especificada
     * la vista que nos representa como bloque.
     *
     * Acepta unos datos que se mezclarán con los del propio bloque.
     *
     * @param View $view
     * @param string $section
     * @param array $data
     * @return void
     */
    public function addToSection(View $view, $section, array $data = NULL)
    {
        if (!is_array($data)) {
            $data = [];
        }
        $this->mapVars($data);
        $view->addSection($section, $this->getViewFile(), array_merge($this->data, $data));
    }

    // --------------------------------------------------------------------

    /**
     * Esta función es llamada para transferir los nombres de variables del usuario
     * a los nombres propios usados en la vista.
     *
     * @param array $data
     * @return mixed
     */
    abstract public function mapVars(array $data);

    // --------------------------------------------------------------------

    /**
     * Devuelve el nombre de vista. Ojo, no es una ruta del sistema de archivos.
     *
     * @return mixed
     */
    abstract public function getViewFile();

    // --------------------------------------------------------------------
}
