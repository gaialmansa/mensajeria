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

use zfx\Config;
use zfx\Report;
use function zfx\a;
use function zfx\aa;
use function zfx\trueEmpty;
use function zfx\va;

include_once('Abs_AppController.php');

abstract class Abs_ZafReportForm extends Abs_AppController
{

    /**
     * @array $data Colección de datos (nativos de PHP) que enviaremos tanto al formulario como al informe.
     */
    protected $data;

    protected $format;

    protected $action;

    protected $showTrans;

    protected $trans;

    protected $showDevMode;

    protected $dev;

    protected $reportType;

    // --------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        // Datos que se usarán para realizar el informe
        $this->data = [
            '_title'  => $this->getTitle(),
            '_target' => $this->_urlController()
        ];

        // Formato y acción por defecto
        $this->format = $this->getDefaultFormat();
        $this->action = $this->getDefaultAction();

        // En principio no se muestra el checkbox para trasponer
        $this->showTrans = FALSE;

        // No se hará transposición
        $this->trans = FALSE;

        // En principio no se muestra el checkbox para activar el modo desarrollo.
        $this->showDevMode = FALSE;

        // El modo desarrollo no estará activo de forma predeterminada.
        $this->dev = FALSE;

        // El tipo de informe por defecto va a ser documento de texto
        $this->reportType = \zfx\Report::TYPE_DOC;
    }

    // --------------------------------------------------------------------

    /**
     * Obtener el tiulo a mostrar
     *
     * @return string
     */
    protected function getTitle(): string
    {
        return '';
    }

    // --------------------------------------------------------------------

    /**
     * Obtener el formato predeterminado
     *
     * @return int
     */
    protected function getDefaultFormat(): int
    {
        return Report::FORMAT_PDF;
    }

    // --------------------------------------------------------------------

    /**
     * Obtener la acción predeterminada
     *
     * @return int
     */
    protected function getDefaultAction(): int
    {
        return Report::ACTION_DOWNLOAD;
    }

    // --------------------------------------------------------------------

    public function _main()
    {
        // Si nos vienen datos por POST, los procesamos y tratamos de generar el informe
        if (array_key_exists('_post', $_SESSION)) {

            // Estos son los datos del formato del informe, como el formato de salida o el tipo de salida (descargar, enviar PDF, etc.)
            $this->procPostFormat();

            // Aquí calculamos los datos del formulario y los guardamos en $this->data
            $this->procPost();

            // Limpiamos POST
            unset($_SESSION['_post']);

            // Vamos a instanciar nuestro informe
            $report = $this->instReport();

            // Calculamos, colocaremos los datos necesarios en $report->data,
            // Devolvemos los errores como string o una cadena vacía si no hubo ningún error
            $errorsTxt = $this->calcReport($report);

            // Establecemos la plantilla a usar
            $template = $this->getTemplate();
            $report->setTemplatePath($template);
            if (\zfx\trueEmpty($template) && $report->templateRequired) {
                $errorsTxt .= "\nNo había una plantilla disponible.";
            }

            // En caso de errores, se muestran y se vuelve a mostrar el formulario.
            if (!trueEmpty($errorsTxt)) {
                $this->_view->addSection('body', 'zaf/' . Config::get('admTheme') . '/report/exec-errors', ['errors' => $errorsTxt]);
                $this->constructForm();
                $this->_view->show();
            }
            // Si no hubo errores, ejecutamos el informe
            else {
                if ($this->dev) {
                    \zfx\Debug::show($report);
                }
                else {
                    // Establecemos el formato deseado
                    $report->setOutputFormat($this->format);
                    // Lo ejecutamos
                    $report->templateOpen();
                    $report->templateWrite();
                    $report->templateClose();
                    // Lo mostramos, descargamos, etc.
                    if ($this->action == Report::ACTION_VIEW) {
                        $report->setForceDownload(FALSE);
                    }
                    else {
                        $report->setForceDownload(TRUE);
                    }
                    $report->render();
                }
            }
        }
        // Si no hay POST, mostramos un formulario con los datos por defecto
        else {
            // Calculamos datos por defecto. Si nos devuelve un error, es que el sistema todavía no está en condiciones
            // de emitir el informe ni siquiera con los datos por defecto.
            $errorsTxt = $this->calcDefaultData();
            if (!trueEmpty($errorsTxt)) {
                $this->_view->addSection('body', 'zaf/' . Config::get('admTheme') . '/report/pre-errors', ['errors' => $errorsTxt]);
            }
            // Si no hubo errores iniciales, mostramos el formulario con los datos predeterminados
            else {
                $this->constructForm();
            }
            $this->_view->show();
        }
    }

    // --------------------------------------------------------------------

    /**
     * Construye el formulario, bien usando un formulario completamente personalizado,
     * o bien usando uno semi-personalizado.
     * @return void
     */
    protected function constructForm()
    {
        // Si hay un formulario personalizado lo usamos.
        if ($this->getFormViewName() != '') {
            $this->_view->addSection('body', $this->getFormViewName(), $this->data);
        }
        // Si no hay un formulario personalizado, usaremos un contenedor de bloques estándar vacío.
        else {
            $this->_view->addSection('body', 'zaf/' . Config::get('admTheme') . '/report/container', $this->data);
        }

        // En la sección de bloques colocamos todos los bloques que haya definidos.
        if ($blocks = $this->getBlocks()) {
            foreach ($blocks as $block) {
                $block->addToSection($this->_view, 'blocks', $this->data);
            }
        }

        // En cualquier caso proporcionamos la parte de opciones.
        $this->_view->addSection('options', 'zaf/' . Config::get('admTheme') . '/report/options', [
            '_format'      => $this->format,
            '_action'      => $this->action,
            '_formats'     => $this->getFormats(),
            '_actions'     => $this->getActions(),
            '_showDevMode' => $this->showDevMode,
            '_showTrans'   => $this->showTrans,
        ]);

    }

    // --------------------------------------------------------------------

    /**
     * Toma los datos POST obtenidos desde el formulario de opciones,
     * como el formato o la acción a realizar con el informe, y los almacena
     * en la instancia como $this->format, $this->action, etc.
     *
     * @return void
     */
    protected function procPostFormat()
    {
        $this->format = (int)aa($_SESSION, '_post', '_format');
        if ($this->format < Report::FORMAT_min || $this->format > Report::FORMAT_max) {
            $this->format = $this->getDefaultFormat();
        }
        $this->action = (int)aa($_SESSION, '_post', '_action');
        if ($this->action < Report::ACTION_min || $this->action > Report::ACTION_max) {
            $this->action = $this->getDefaultAction();
        }
        $this->dev   = (aa($_SESSION, '_post', '_dev') === 'checked');
        $this->trans = (aa($_SESSION, '_post', '_trans') === 'checked');
    }

    // --------------------------------------------------------------------

    /**
     * Toma los datos POST obtenidos desde el formulario y los almacena en
     * $this->data.
     * Esta función a veces puede necesitar ser redefinida en una clase
     * extendida, pero la funcionalidad por defecto basta en la mayoría de
     * los casos.
     *
     * @return void
     */
    protected function procPost()
    {
        $post = a($_SESSION, '_post');
        if (va($post)) {
            foreach ($post as $key => $value) {
                if (substr($key, 0, 1) != '_') {
                    $this->data[$key] = $value;
                }
            }
        }
    }

    // --------------------------------------------------------------------
    // FUNCIONES A IMPLEMENTAR PARA CUBRIR LA FUNCIONALIDAD DE INFORME
    // --------------------------------------------------------------------

    /**
     * Obtener una instancia del tipo \zfx\Report (normalmente un derivado)
     * @return Report
     */
    abstract protected function instReport(): Report;

    // --------------------------------------------------------------------

    /**
     * Obtener la ruta completa de la plantilla a usar
     * @return string La ruta completa de la plantilla a usar
     */
    abstract protected function getTemplate(): string;

    // --------------------------------------------------------------------

    /**
     * Calcular los datos del informe con los resultados del formulario
     * que tenemos en $this->data. Normalmente los resultados los colocaremos en
     * $report->data.
     *
     * @param Report $report
     * @return string Si hubo errores se devuelven, en caso contrario se devuelve una cadena vacía.
     */
    abstract protected function calcReport(Report $report): string;

    // --------------------------------------------------------------------

    /**
     * Obtener el nombre de la vista que contiene el formulario personalizado, si lo hay.
     *
     * Un formulario personalizado debe tener como mínimo:
     * - un elemento <form> cuya action sea la variable $_target
     * - una sección options: $this->section('options')
     * - un botón submit.
     * Opcionalmente, también puede tener una sección blocks: $this->section('blocks') donde colocar bloques.
     *
     * @return string Si es una cadena en blanco, no hay formulario personalizado.
     */
    protected function getFormViewName(): string
    {
        return '';
    }

    // --------------------------------------------------------------------

    /**
     * Obtener la lista de formatos. Por defecto devolvemos todos los que
     * Report permita.
     *
     * @return array
     */
    protected function getFormats(): array
    {
        return Report::getFormats($this->reportType);
    }

    // --------------------------------------------------------------------

    /**
     * Obtener la lista de acciones. Por defecto devolvemos todas las implementadas.
     *
     * @return array
     */
    protected function getActions(): array
    {
        return Report::getActions();
    }

    // --------------------------------------------------------------------

    /**
     * Calcular los datos iniciales a mostrar en el formulario.
     * Se deben dejar en $this->data.
     *
     * @return string Si hubo errores se devuelven como cadena, en caso contrario se devuelve una cadena vacía.
     */
    abstract public function calcDefaultData(): string;

    // --------------------------------------------------------------------

    /**
     * Obtener una lista de objetos del tipo ReportBlock que se van a incorporar al formulario.
     * Esto es opcional y por defecto devuelve una lista vacía.
     *
     * @return array
     */
    protected function getBlocks(): array
    {
        return [];
    }

    // --------------------------------------------------------------------
    // FUNCIONES A IMPLEMENTAR O REDEFINIR POR SER UN CONTROLADOR DEL TIPO ADMCONTROLLER
    // --------------------------------------------------------------------

    public abstract function _getCurrentSection();

    // --------------------------------------------------------------------

    public abstract function _getCurrentSubSection();

    // --------------------------------------------------------------------

}
