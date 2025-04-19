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

use zfx\AutoSchema;
use zfx\AutoTable;
use zfx\Config;
use zfx\DataFilterBox;
use zfx\DataFilterList;
use zfx\DataFilterRange;
use zfx\DataVarType;
use zfx\DB;
use zfx\Debug;
use zfx\FastInserter;
use zfx\FieldViewStringMap;
use zfx\OrderedFieldSet;
use zfx\Paginator;
use zfx\PKView;
use zfx\RecordSet;
use zfx\RowViewer;
use zfx\Schema;
use zfx\SortButton;
use zfx\SQLGen;
use zfx\StrFilter;
use zfx\TableExport;
use zfx\TableView;
use zfx\TableViewer;
use zfx\View;
use function zfx\a;
use function zfx\aa;
use function zfx\nwcount;
use function zfx\trueEmpty;
use function zfx\va;

include_once('Abs_AdmAjaxController.php');

abstract class Abs_AdmCrudController extends Abs_AdmAjaxController
{
    // --------------------------------------------------------------------
    // CONSTANTES
    // --------------------------------------------------------------------

    const MODE_LST = 1; // Modo listar
    const MODE_ADD = 2; // Modo añadir
    const MODE_EDT = 3; // Modo editar


    // --------------------------------------------------------------------
    // PROPIEDADES
    // --------------------------------------------------------------------

    /**
     * @var AutoSchema Instancia del esquema ZFX a usar en el sistema CRUD
     */
    protected $schema;

    /**
     * @var AutoTable Instancia de la tabla ZFX
     */
    protected $table;

    /**
     * @var TableView Instancia del sistema de vista de tablas ZFX
     */
    protected $tableView;


    // FUENTES DE DATOS ALTERNATIVAS
    /**
     * @var $dsMode integer Define qué modo vamos a usar para orígenes de datos (datasources) alternativos.
     */
    protected $dsMode;
    /**
     * @var bool $dsAutoEnable Si es TRUE y hay fuentes de datos alternativas configuradas, dichas fuentes se activarán automáticamente al listar, editar, etc.
     */
    protected $dsAutoEnable;
    /**
     * @var $dsSchemas array Lista de Schemas alternativos para usar en algún modo concreto.
     */
    protected $dsSchemas;
    /**
     * @var $dsTables array Lista de Tables alternativos para usar en algún modo concreto.
     */
    protected $dsTables;
    /**
     * @var $dsTableViews array Lista de TableViews alternativos para usar en algún modo concreto.
     */
    protected $dsTableViews;

    /**
     * @var $dsModeExport integer El modo DS que se usará por defecto al exportar
     */
    protected $dsModeExport;

    /**
     * @var $filterUseQualifiers boolean Si se debe o no usar cualificadores (los nombres de las tablas) en la construcción de los filtros. Por defecto es TRUE.
     * Esta característica es experimental y solo debería cambiarse en CRUDs bajo observación
     */
    protected $filterUseQualifiers;


    /**
     * @var integer $rowsPerPage Cantidad de filas por página para paginar. Si no se quiere paginador, usar el valor 0
     */
    protected $rowsPerPage;

    /**
     * @var boolean $canAdd Activa/desactiva la creación de nuevas filas. TRUE para permitir.
     */
    protected $canAdd;

    /**
     * @var boolean $canLst Activa/desactiva el listado. TRUE para permitir.
     */
    protected $canLst;
    protected $canEdt;
    protected $canDel;
    protected $canSort;
    protected $canImport;


    /**
     * @var array $editableFields Usar este array en vez asignar directamente con setEditable() para un FieldView.
     */
    protected $editableFields;

    /**
     * @var array $nonEditableFields Usar este array en vez asignar directamente con setEditable() para un FieldView.
     */
    protected $nonEditableFields;

    protected $lstHideFields;
    protected $edtHideFields;
    protected $addHideFields;
    protected $ofsId;
    protected $pagId;
    protected $schId;
    protected $lasId;
    protected $cfgId;
    protected $locId;
    protected $clkOnRow;
    protected $sortByIDByDefault;
    protected $filterIndexedOnly;
    protected $hideFilRel;
    //
    protected $lstSelector;
    protected $addSelector;
    protected $edtSelector;
    protected $impSelector;

    protected $lstBootStrap;
    //
    /**
     * @var string $insertResult PKView empaquetado
     */
    protected $insertResult;
    //
    protected $vTemplateForm;
    protected $vTemplateFormID;
    protected $vTemplateFormSectionID;
    protected $vTemplateFormSection;
    protected $vSectionIDTable;
    protected $vTemplateTable;
    protected $vTemplateTableCSS;
    protected $schCompact;
    protected $edtExpand;
    protected $addExpand;
    protected $delAsk;
    protected $vImportForm;
    protected $vImportFormError;
    //

    /**
     * @var string|array
     */
    protected $simpleOrder;
    protected $simpleOrderAscending;
    protected $safeSorting;

    // --------------------------------------------------------------------
    // Tabs

    /**
     * @var array tabList List of tabs if using 'crud-form-tabs' in vTemplateForm
     */
    protected $tabList;

    /**
     * @var string $tabForm Key from $tabList to put the main form into
     */
    protected $tabForm;

    /**
     * @var string $tabSelected Key from $tabList initially selected
     */
    protected $tabSelected;

    // --------------------------------------------------------------------
    // Filter options

    /**
     * @var array filterBoxFields Array of fields (id=>label) to force to be used in the filterbox element.
     */
    protected $filterBoxFields;

    /**
     *
     * @var mixed Pre-selected field to be shown by default in the filterbox element.
     */
    protected $filterBoxDefault;

    /**
     * @var array filterRangeFields Array of fields (id=>label) to force to be used in the filterRange element.
     */
    protected $filterRangeFields;

    /**
     *
     * @var array filterDisabledFilters Array of forbidden filters.
     */
    protected $filterDisabledFilters;
    protected $filterBoxJSH;
    protected $filterRangeJSH;
    protected $filterPrimary;
    protected $filterPrimaryBox;
    protected $filterPrimaryRange;

    protected $filterListOrder;

    /**
     *
     * @var View View
     */
    protected $view;

    /**
     *
     * @var array View data
     */
    protected $viewData;

    /**
     * Recordset with first-time default values for row insertion
     * @var array
     */
    protected $defaultRS;

    // --------------------------------------------------------------------
    // Behavior

    protected $edtShowSaveButton;
    protected $edtShowCloseButton;
    protected $edtCloseOnSave;
    protected $addShowSaveButton;
    protected $addShowCloseButton;
    protected $addCloseOnSave;
    protected $editAfterInsert;
    protected $nextAfterInsert;
    protected $addAutoHideFields;
    protected $edtFeedbackOnSave;
    protected $addFeedbackOnSave;
    protected $lstFilterDisablesPaginator;
    protected $editIsView;
    protected $noTarget = FALSE;


    // --------------------------------------------------------------------
    // Use different controllers for listing, adding, editing.

    protected $lstOtherClass;
    protected $addOtherClass;
    protected $edtOtherClass;


    // --------------------------------------------------------------------
    // ???

    /**
     * RowViewer usado para mostrar un formulario de edición (o similar)
     * @var RowViewer $rvr
     */
    protected $rvr;


    // --------------------------------------------------------------------
    // Picker options

    /**
     * This is the target field, thus, the field we want to set its value
     *
     * @var string $pickerTargetField
     */
    protected $pickerTargetField;

    /**
     * These are the source fields: theirs values will be sent when doing a selection.
     * The common value is array('id', 'name')
     *
     * @var array $pickerSelectFields
     */
    protected $pickerSelectFields;

    /**
     * The sprintf format string for value 1: the hidden field.
     * The common value is '%1$s'
     * All fields in $pickerSelectFields will be sent.
     *
     * @var string $pickerSetValueFormat
     */
    protected $pickerSetValueFormat;

    /**
     * The sprintf format string for value 2: the display field.
     * The common value is '%2$s'
     * All fields in $pickerSelectFields will be sent.
     *
     * @var string $pickerSetDisplayFormat
     */
    protected $pickerSetDisplayFormat;

    // --------------------------------------------------------------------
    // Behavior

    /**
     * Force staying in the last page when inserting rows
     * @var bool $stayOnLast
     */
    protected $stayOnLast;

    // --------------------------------------------------------------------
    // Image and document file uploading

    protected $dbFilePath;
    protected $dbFileUrl;

    // --------------------------------------------------------------------
    // Opciones de clonado

    const CLONE_TYPE_NONE     = 0; // No clonado (predeterminado)
    const CLONE_TYPE_SINGLE   = 1; // Clonado de filas únicas sencillo automático
    const CLONE_TYPE_MANUAL   = 2; // Clonado no automático, hay que usar modelos
    const CLONE_TYPE_MULTIPLE = 3; // Clonado de filas y sus filas de tablas relacionadas (experimental)

    const CLONE_RESULT_NONE    = 0; // No hacer nada (se requiere cuando se usó CLONE_TYPE_MANUAL)
    const CLONE_RESULT_PREPARE = 1; // Para clonar, mostrar el formulario de creación
    const CLONE_RESULT_SAVE    = 2; // Simplemente clonar y guardar, sin mostrar formularios.

    /**
     * @var int $cloneType Establecer qué tipo de clonación se usará. Usar las constantes CLONE_TYPE_
     */
    protected $cloneType;

    /**
     * @var int $cloneResult Establecer el resultado de la clonación. Usar las constantes CLONE_RESULT_
     */
    protected $cloneResult;

    /**
     * Callable que se llamará para retocar los datos antes de la clonación automática. Se le pasa el defaultRS y debería poder cambiarlo por referencia.
     *
     * @var callable $cloneModel
     */
    protected $cloneRSModel;

    /**
     * Callable que se llamará para clonar. Si cloneType=MANUAL, solo se llamará a este callable y se le pasará como parámetro el PK origen.
     * Si está definida cuando cloneType = SINGLE, se llamará después de la clonación, y el segundo parámetro será el insertResult de la clonación.
     *
     * @var callable $cloneModel
     */
    protected $cloneModel;

    /**
     * La lista de campos a usar para una operación del tipo TYPE_SINGLE.
     * Nota: las claves primarias siempre son excluidas.
     *
     * @var array $cloneIncludeFields
     */
    protected $cloneIncludeFields;

    /**
     * La lista de campos a no usar para una operación del tipo TYPE_SINGLE.
     * Nota: las claves primarias nunca son usadas.
     *
     * @var array $cloneExcludeFields
     */
    protected $cloneExcludeFields;


    /**
     * Lista de tablas y/o campos a excluir de una clonación múltiple.
     * Es un mapa cuyas claves son nombres de tablas y los valores pueden ser:
     * - La cadena subrayado '_' para indicar que esta tabla entera será excluida;
     * - Un nombre de campo,
     * - Una lista de campos.
     * @var array
     */
    protected $cloneMultipleExcludeFields;


    /**
     * Lista de tablas y/o campos a incluir únicamente en una clonación múltiple.
     * Es un mapa cuyas claves son nombres de tablas y los valores pueden ser:
     * - La cadena subrayado '_' para indicar que la tabla entera será incluida;
     * - Un nombre de campo, y sólo ese campo será incluido;
     * - Una lista de campos, y solo esos campos serán incluídos.
     * @var array
     */
    protected $cloneMultipleIncludeFields;


    /**
     * Si se esta usando vistas y diferentes datasources, con esto definimos qué fuente de datos se va a usar.
     * @var bool
     */
    protected $cloneDsMode = FALSE;


    // --------------------------------------------------------------------

    // Contiene la PK (desempaquetada, o sea, es un array) del último registro editado o insertado
    protected $postDataPK;

    // --------------------------------------------------------------------
    // Low level settings

    /**
     * @var array $addSaveDatas Array of 'data-XXX' style to be put in form > save button when adding
     */
    protected $addSaveDatas;
    /**
     * @var array $addCancelDatas Array of 'data-XXX' style to be put in form > cancel button when adding
     */
    protected $addCancelDatas;
    /**
     * @var array $edtSaveDatas Array of 'data-XXX' style to be put in form > save button when editing
     */
    protected $edtSaveDatas;
    /**
     * @var array $edtCancelDatas Array of 'data-XXX' style to be put in form > cancel button when editing
     */
    protected $edtCancelDatas;

    /**
     * @var array $filRel Este array está vacío pero se rellenará con los datos de la tabla padre cuando el CRUD
     * está funcionando como tabla relacionada.
     */
    protected $filRel;
    // --------------------------------------------------------------------

    /**
     * Controla los campos a mostrar. Se hace por tipo, o sea:
     * $this->expFields['pdf'] = array(...),
     * $this->expFields['pdfv'] = array(...),
     * $this->expFields['csv'] = array(...),
     * Y así sucesivamente.
     *
     * Algo vacío significa "todos".
     *
     * @var array $expFields
     */
    protected $expFields;


    /**
     * Tabla de sustituciones usada al exportar con formato exportable
     * @var array
     */
    protected $expFieldMapper = [];


    /**
     * Si no es null, se usará una instancia de FastInserter para hacer inserciones a partir de otra tabla o similares.
     *
     * @var null|FastInserter
     */
    protected $fastInserter = NULL;


    // Interdependencias en el formulario de edición/creación
    protected $interDeps;

    // VarType es un campo cuyo tipo cambia según otro campo.
    // De momento la actual implementación solo soporta edición,
    // pero está previsto añadir soporte durante la creación del registro.
    /**
     * @var string Nombre del campo cuyo tipo varía
     */
    protected $varTypeField;

    /**
     * @var string Nombre del campo que almacena el tipo de dato
     */
    protected $varTypeOrigin;

    /**
     * @var string Opcional, el nombre del campo que almacena opciones.
     */
    protected $varTypeOptions;

    /**
     * @var TableView Si se define, se usará este TableView para obtener las rutas correctas de las imágenes. Por defecto es NULL y esto significa que se usará el tableview por defecto
     */
    protected $tableViewImages;


    // --------------------------------------------------------------------
    // END OF PROPERTIES
    // --------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        // Orígenes de datos alternativos: por defecto ninguno
        $this->dsMode              = NULL;
        $this->dsSchemas           = NULL;
        $this->dsTables            = NULL;
        $this->dsTableViews        = NULL;
        $this->dsAutoEnable        = FALSE;
        $this->dsModeExport        = self::MODE_LST;
        $this->filterUseQualifiers = TRUE;

        $this->canAdd                = TRUE;
        $this->canLst                = TRUE;
        $this->canEdt                = TRUE;
        $this->canDel                = TRUE;
        $this->canImport             = TRUE;
        $this->rowsPerPage           = Config::get('zafDefaultPageSize');
        $this->canSort               = TRUE;
        $this->editableFields        = NULL;
        $this->nonEditableFields     = NULL;
        $this->lstHideFields         = NULL;
        $this->edtHideFields         = NULL;
        $this->addHideFields         = NULL;
        $this->addAutoHideFields     = TRUE;
        $this->sortByIDByDefault     = TRUE;
        $this->filterIndexedOnly     = TRUE;
        $this->clkOnRow              = FALSE;
        $this->filterBoxFields       = [];
        $this->filterRangeFields     = [];
        $this->filterDisabledFilters = [];
        $this->filterPrimaryBox      = FALSE;
        $this->filterPrimaryRange    = FALSE;
        $this->filterPrimary         = [];
        $this->filterListOrder       = [];
        $this->simpleOrder           = '';
        $this->simpleOrderAscending  = FALSE;
        $this->stayOnLast            = FALSE;
        $this->filterBoxJSH          = [];
        $this->filterRangeJSH        = [];

        // We will not use other classes
        $this->lstOtherClass = '';
        $this->addOtherClass = '';
        $this->edtOtherClass = '';

        //
        $this->edtExpand = FALSE;
        $this->addExpand = FALSE;
        $this->delAsk    = TRUE;

        // ID for storing sorting data in session
        $this->ofsId = '_adm-ofs-' . get_class($this);
        // ID for storing page in session
        $this->pagId = '_adm-pag-' . get_class($this);
        // ID for the searcher module
        $this->schId = '_adm-sch-' . get_class($this);
        // ID for remember if we are in the last page
        $this->lasId = '_adm-las-' . get_class($this);
        // ID for configuration storing
        $this->cfgId = '_adm-cfg-' . get_class($this);
        // ID for local (volatile) settings
        $this->locId = '_adm-loc-' . get_class($this);

        $this->lstBootStrap = NULL;

        $this->lstSelector = "#lst_" . get_class($this);
        $this->addSelector = "#add_" . get_class($this);
        $this->edtSelector = "#edt_" . get_class($this);
        $this->impSelector = "#imp_" . get_class($this);

        $this->defaultRS = [];

        $this->addShowSaveButton  = TRUE;
        $this->addShowCloseButton = TRUE;
        $this->addCloseOnSave     = TRUE;
        $this->edtShowSaveButton  = TRUE;
        $this->edtShowCloseButton = TRUE;
        $this->edtCloseOnSave     = TRUE;
        $this->edtFeedbackOnSave  = FALSE;
        $this->addFeedbackOnSave  = FALSE;
        $this->editAfterInsert    = FALSE;
        $this->nextAfterInsert    = FALSE;
        $this->editIsView         = FALSE;
        $this->noTarget           = FALSE;

        $this->schCompact = TRUE;

        // En principio no hay interdependencias
        $this->interDeps = [];

        // If TRUE, will hide foreign keys fields when filter is set
        $this->hideFilRel = TRUE;

        // View template for all insert and editing forms
        $this->vTemplateForm          = 'zaf/' . Config::get('admTheme') . '/crud-form';
        $this->vTemplateFormSectionID = 'body';
        $this->vTemplateFormID        = '_' . md5(uniqid('', TRUE));

        // View for rowviewer
        $this->vTemplateFormSection = 'zaf/' . Config::get('admTheme') . '/rowviewer-form';
        $this->vSectionIDTable      = 'crud-simple-table';
        $this->vTemplateTable       = 'zaf/' . Config::get('admTheme') . '/tableviewer-table';
        $this->vTemplateTableCSS    = '';

        // View for import
        $this->vImportForm      = 'zaf/' . Config::get('admTheme') . '/import-form';
        $this->vImportFormError = 'zaf/' . Config::get('admTheme') . '/import-form-error';


        // Configuración por defecto para clonación
        $this->cloneType                  = self::CLONE_TYPE_NONE;
        $this->cloneResult                = self::CLONE_RESULT_NONE;
        $this->cloneModel                 = NULL;
        $this->cloneRSModel               = NULL;
        $this->cloneIncludeFields         = [];
        $this->cloneExcludeFields         = [];
        $this->cloneMultipleExcludeFields = [];
        $this->cloneMultipleIncludeFields = [];


        // Configuración VarType
        $this->varTypeField   = '';
        $this->varTypeOrigin  = '';
        $this->varTypeOptions = '';


        // Picker config
        $this->pickerTargetField      = '';
        $this->pickerSelectFields     = NULL;
        $this->pickerSetValueFormat   = '';
        $this->pickerSetDisplayFormat = '';

        //
        $this->lstFilterDisablesPaginator = FALSE;

        $this->safeSorting = TRUE;

        //
        $this->viewData = [];

        $this->tableViewImages = NULL;

        // Data View Image and Document file
        $this->dbFilePath = Config::Get('data-view_filePath');
        $this->dbFileUrl  = Config::get('rootUrl') . Config::Get('data-view_fileUrlPath');

        $this->addSaveDatas   = [];
        $this->addCancelDatas = [];
        $this->edtSaveDatas   = [];
        $this->edtCancelDatas = [];


        $this->expFields = [];

        $this->filRel = [];

        // DATA INITIALIZATION
        $this->initData();

        // Restore user preferences from SESSION or DB
        $this->restoreCfg();
        $this->applyCfg();

        // POST-INIT SETUP
        // In case of missing data we need to create an OrderedFieldSet
        if (!isset($_SESSION[$this->ofsId])) {
            $this->setupSessionSortInfo();
        }
    }

    // --------------------------------------------------------------------

    protected function restoreCfg()
    {
        if (!array_key_exists($this->cfgId, $_SESSION)) {
            $scfg                   = $this->_getUser()->getAttr($this->cfgId);
            $_SESSION[$this->cfgId] = json_decode($scfg);
        }
    }

    // --------------------------------------------------------------------

    protected function applyCfg()
    {
        if (!array_key_exists($this->cfgId, $_SESSION)) {
            return;
        }
        if (isset($_SESSION[$this->cfgId]->fieldOrder)) {
            $this->getTableView(self::MODE_LST)->setFieldOrder($_SESSION[$this->cfgId]->fieldOrder);
        }
    }

    // --------------------------------------------------------------------

    protected function saveCfg()
    {
        if (!array_key_exists($this->cfgId, $_SESSION)) {
            return;
        }
        $this->_getUser()->setAttr($this->cfgId, json_encode($_SESSION[$this->cfgId]));
    }

    // --------------------------------------------------------------------

    protected abstract function initData();

    // --------------------------------------------------------------------

    protected function setupSessionSortInfo()
    {
        // simpleOrder es un array
        if (is_array($this->simpleOrder) && nwcount($this->simpleOrder)) {
            $_SESSION[$this->ofsId] = new OrderedFieldSet(FALSE);
            $_SESSION[$this->ofsId]->setFieldSet($this->simpleOrder);
            $_SESSION[$this->ofsId]->setDefault($this->simpleOrder);
        } // simpleOrder no es un array, es una cadena
        elseif (!trueEmpty($this->simpleOrder)) {
            $_SESSION[$this->ofsId] = new OrderedFieldSet(TRUE);
            $_SESSION[$this->ofsId]->setFieldSet(array($this->simpleOrder => (bool)$this->simpleOrderAscending));
        } // simpleOrder está vacío
        else {
            $_SESSION[$this->ofsId] = new OrderedFieldSet();
            // If we must sort by ID then do it
            if ($this->sortByIDByDefault) {
                $sortFields = [];
                foreach ($this->getSchema()->getPrimaryKey() as $key) {
                    $sortFields[$key] = FALSE;
                }
                $_SESSION[$this->ofsId]->setFieldSet($sortFields);
                $_SESSION[$this->ofsId]->setDefault($sortFields);
            }
        }
    }

    // --------------------------------------------------------------------

    public function _main()
    {
        // Autoexec segment has the first priority
        if (!$this->_autoexec()) {
            // Fallback to table list (if allowed)
            $this->lstBootStrap = NULL;
            $this->processPOST($_POST); // this can modify $this->lstBootStrap
            if ($this->canLst) {
                if (trueEmpty($this->lstOtherClass)) {
                    $this->lst();
                }
                else {
                    echo $this->lstBootStrap;
                    $url = Config::getModRootUrl() . StrFilter::dashes($this->lstOtherClass);
                    echo "<script>actionLoad('{$url}', '{$this->lstSelector}');</script>";
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Process POST data and execute actions accordingly
     *
     * @param array $postData $_POST copy
     */
    protected function processPOST($postData)
    {
        // Si tenemos la funcionalidad varType que cambia el tipo de dato de
        // un campo en función del valor de otro campo del mismo registro,
        // la activamos, necesitando preservar, además, el campo original.
        if ($this->varTypeField && $postData) {
            $oldField = $this->getTableView()->getFieldView($this->varTypeField);
            $oldId    = PKView::unpack(a($postData, '_id'));
            if ($oldId) {
                $dataType = $this->getPostData($postData, $this->varTypeOrigin);
                $options  = $this->getPostData($postData, $this->varTypeOptions);
                $value    = $this->getPostData($postData, $this->varTypeField);
                DataVarType::varTypeChange($this->getTableView(), $this->varTypeField, $this->dbFileUrl, $this->loc, $dataType, $options);
                $this->setPostData($postData, $this->varTypeField, $this->varTypeCast($value, $dataType, $options));
            }
        }

        // Proceso de ficheros
        $this->processFILES($postData);

        // Proceso del resto de datos POST
        if ($postData) {
            // Tratamiento de borrado de los ficheros
            foreach ($postData as $key => $value) {
                if (substr($key, -4) == '_del') {
                    $fileName = a($postData, substr($key, 0, -4) . '_org');
                    if (preg_match('%^[-0-9a-z]+\.?[0-9a-z]{0,3}$%siu', $fileName)) {
                        $id        = StrFilter::safeDecode(substr($key, 1, strlen($key) - 4));
                        $directory = $this->dbFilePath . $this->getTableView()->getFieldView($id)->getTable() . DIRECTORY_SEPARATOR . $this->getTableView()->getFieldView($id)->getColumn();
                        $file      = $directory . DIRECTORY_SEPARATOR . $fileName;
                        if (file_exists($file) && is_writable($file)) {
                            unlink($file);
                        }
                        $postData[substr($key, 0, -4)] = '';
                    }
                }
            }
            $data  = RecordSet::processPOST($postData, $this->getSchema(), $this->loc);
            $oldId = PKView::unpack(a($postData, '_id'));
            if ($oldId) {
                $this->update($oldId, $data);
                $this->postDataPK = $oldId;
            }
            else {
                $this->insert($data);
                $this->postDataPK = PKView::unpack($this->insertResult);
            }
        }


        // Si tenemos la funcionalidad varType que cambia el tipo de dato de
        // un campo en función del valor de otro campo del mismo registro,
        // restauramos el campo original
        if ($this->varTypeField && $postData) {
            $this->getTableView()->setFieldView($this->varTypeField, $oldField);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Process $_FILES variable
     * @param array $postData
     */
    protected function processFILES(array &$postData)
    {
        if ($_FILES) {
            foreach ($_FILES as $id => $file) {
                if ($file['error'] == UPLOAD_ERR_OK) {
                    if (substr($id, 0, 1) == '_') {
                        // If there is a checkbox field name "$id_del", ignore the uploaded file (if any).
                        if (!array_key_exists($id . '_del', $postData)) {
                            $postData[$id] = $this->processDBFile(StrFilter::safeDecode(substr($id, 1)), $file['tmp_name'], $file['size'], $file['name']);
                        }
                    }
                    else {
                        $postData[$id] = $this->processFile($id, $file['tmp_name'], $file['size'], $file['name']);
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Process uploaded file
     *
     * @param string $id Name of input
     * @param string $tmpPath
     * @param int $size
     * @param string $originalName
     * @return mixed
     */
    protected function processFile($id, $tmpPath, $size, $originalName)
    {
        return NULL;
    }

    // --------------------------------------------------------------------

    /**
     * Process uploaded file using FieldViewImage or FieldViewFile
     * @param $id
     * @param $tmpPath
     * @param $size
     * @param $originalName
     * @return |null
     */
    protected function processDBFile($id, $tmpPath, $size, $originalName)
    {
        $ext       = pathinfo($originalName, PATHINFO_EXTENSION);
        $name      = StrFilter::lowerCase(substr($this->_getUser()->getId() . '-' . date('Ymd-His') . '-' . md5("$tmpPath,$originalName,$size") . '-' . uniqid(),
                0, 124) . substr('.' . StrFilter::getID($ext), 0, 4));
        $fieldView = $this->getTableView()->getFieldView($id);
        $directory = $this->dbFilePath . $fieldView->getTable() . DIRECTORY_SEPARATOR . $fieldView->getColumn();
        if (!is_dir($directory)) {
            mkdir($directory, 0777, TRUE);
        }
        move_uploaded_file($tmpPath, $directory . DIRECTORY_SEPARATOR . $name);
        // Si debíamos conservar el nombre original, será solo en la base de datos.
        if (is_a($fieldView, '\zfx\FieldViewFile') || is_a($fieldView, '\zfx\FieldViewImage')) {
            if ($fieldView->keepOriginalName()) {
                $name = '[' . \zfx\StrFilter::safeEncode($originalName) . ']' . $name;
            }
        }
        return $name;
    }

    // --------------------------------------------------------------------

    protected function update($oldId, $data)
    {
        // Calculamos los datos de la tabla padre. Solo para tenerlos, por si hiceran falta.
        $this->calcFilRel();

        // Hacemos la actualización propiamente dicha
        $this->getTable()->updateR($oldId, $data);
        $this->calcLstBootStrap(self::MODE_EDT);
    }

    // --------------------------------------------------------------------
    // Action methods
    // --------------------------------------------------------------------

    protected function calcLstBootStrap($mode)
    {
        switch ($mode) {
            case self::MODE_EDT:
            {
                if ($this->edtCloseOnSave) {
                    $this->lstBootStrap = "<script>actionClose('{$this->edtSelector}');</script>";
                }
                break;
            }
            case self::MODE_ADD:
            {
                $js = '';
                if ($this->addCloseOnSave) {
                    $js .= "actionClose('{$this->addSelector}');";
                }
                if ($this->editAfterInsert) {
                    if ($this->editAfterInsertUrl == '') {
                        $url = aa($this->lstGetActions(), 'edit', 'data', 'adm-load-source');
                    }
                    else {
                        $url = $this->editAfterInsertUrl;
                    }
                    if ($url) {
                        $url = sprintf($url, $this->insertResult);
                        $js  .= "actionLoad('$url','{$this->edtSelector}', true, " . ($this->edtExpand ? 'true' : 'false') . ");";
                    }
                }
                else {
                    if ($this->nextAfterInsert) {
                        $url = aa($this->lstGetTableActions(), 'add', 'data', 'adm-load-source');
                        if ($url) {
                            $js .= "actionLoad('$url','{$this->addSelector}', true, " . ($this->edtExpand ? 'true' : 'false') . ");";
                        }
                    }
                }
                if ($js) {
                    $this->lstBootStrap = "<script>$js</script>";
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Get Per-row actions used in listings.
     *
     * @return array List of actions
     */
    protected function lstGetActions()
    {
        $actions = [];

        if (!trueEmpty($this->pickerTargetField)) {
            $selector       = '#_DBPicker_' . StrFilter::safeEncode($this->pickerTargetField);
            $actions['sel'] = array(
                'href'   => 'javascript:void(0)',
                'text'   => '<i class="fas fa-hand-pointer"></i>',
                'fields' => $this->pickerSelectFields,
                'data'   => array(
                    'adm-action'          => 'setval',
                    'adm-setval-target-1' => $selector . '_hid',
                    'adm-setval-value-1'  => $this->pickerSetValueFormat,
                    'adm-setval-target-2' => $selector . '_disp',
                    'adm-setval-value-2'  => $this->pickerSetDisplayFormat,
                    'adm-setval-close'    => $selector . '_target'
                )
            );
        }

        if ($this->canEdt) {
            if (trueEmpty($this->edtOtherClass)) {
                $data = array(
                    'adm-action'      => 'load',
                    'adm-load-target' => $this->edtSelector,
                    'adm-load-source' => $this->_urlController() . 'edt/%1$s/',
                    'adm-options'     => 'autoFocus' . ($this->edtExpand ? ' expand' : '')
                );
            }
            else {
                $data = array(
                    'adm-action'      => 'load',
                    'adm-load-target' => $this->edtSelector,
                    'adm-load-source' => Config::getModRootUrl() . StrFilter::dashes($this->edtOtherClass) . '/edt/%1$s/',
                    'adm-options'     => 'autoFocus' . ($this->edtExpand ? ' expand' : '')
                );
            }
            $actions['edit'] = array(
                'href' => 'javascript:void(0)',
                'text' => '<i class="fas fa-pen"></i>',
                'data' => $data
            );
            if ($this->editIsView) {
                $key = (string)aa($_SESSION, $this->locId, 'editablePK');
                if ($key != '') {
                    $actions['edit']['var-text'] = function ($text, $row) use ($key) {
                        if ($key == PKView::pack($this->getSchema()->extractPk($row))) {
                            return '<i class="fas fa-pen"></i>';
                        }
                        else {
                            return '<i class="fas fa-eye"></i>';
                        }
                    };
                }
                else {
                    $actions['edit']['text'] = '<i class="fas fa-eye"></i>';
                }
            }
        }

        if ($this->canDel) {
            if (trueEmpty($this->edtOtherClass)) {
                $data = array(
                    'adm-action'      => 'load',
                    'adm-load-target' => $this->lstSelector,
                    'adm-load-source' => $this->_urlController() . 'del/%1$s/',
                    'adm-options'     => ($this->delAsk ? 'ask' : '')
                );
            }
            else {
                $data = array(
                    'adm-action'      => 'load',
                    'adm-load-target' => $this->lstSelector,
                    'adm-load-source' => Config::getModRootUrl() . StrFilter::dashes($this->edtOtherClass) . '/del/%1$s/',
                    'adm-options'     => ($this->delAsk ? 'ask' : '')
                );
            }
            $actions['del'] = array(
                'href' => 'javascript:void(0)',
                'text' => '<i class="fas fa-ban"></i>',
                'data' => $data
            );
        }

        // Clone
        if ($this->cloneType != self::CLONE_TYPE_NONE) {
            $data = array(
                'adm-action'      => 'load',
                'adm-load-source' => $this->_urlController() . 'clon/%1$s/',
                'adm-options'     => 'autoFocus' . ($this->edtExpand ? ' expand' : '')
            );
            if ($this->cloneResult == self::CLONE_RESULT_PREPARE) {
                // Prepare for adding
                $data['adm-load-target'] = $this->addSelector;
            }
            else {
                // Clone and do nothing
                $data['adm-load-target'] = $this->lstSelector;
            }
            $actions['clon'] = array(
                'href' => 'javascript:void(0)',
                'text' => '<i class="fas fa-clone"></i>',
                'data' => $data
            );

        }
        return $actions;
    }

    // --------------------------------------------------------------------

    /**
     * Clone row
     *
     * @param string $packedID
     */
    public function clon($packedID = '')
    {
        // Get row ID
        $id = PKView::unpack($packedID);
        if (!$id) {
            return;
        }

        if ($this->cloneType == self::CLONE_TYPE_SINGLE || $this->cloneType == self::CLONE_TYPE_MULTIPLE) {
            // Get row without primary keys
            $result = $this->getSchema($this->cloneDsMode)->removeKeys($this->getTable()->readR($id));
            // Apply filters (if defined)
            if ($this->cloneIncludeFields) {
                $filtered = [];
                foreach ($this->cloneIncludeFields as $field) {
                    if (array_key_exists($field, $result)) {
                        $filtered[$field] = $result[$field];
                    }
                }
                $result = $filtered;
            }
            if ($this->cloneExcludeFields) {
                foreach ($this->cloneExcludeFields as $field) {
                    if (array_key_exists($field, $result)) {
                        unset($result[$field]);
                    }
                }
            }
            if (!$result) {
                return;
            }
            // Result processing
            if ($this->cloneResult == self::CLONE_RESULT_PREPARE) {
                $this->defaultRS = array_replace($this->defaultRS, $result);
                if (is_callable($this->cloneRSModel)) {
                    call_user_func_array($this->cloneRSModel, [&$this->defaultRS]);
                }
                $this->add();
            }
            elseif ($this->cloneResult == self::CLONE_RESULT_SAVE) {
                $result = array_merge($result, $this->defaultRS);
                if (is_callable($this->cloneRSModel)) {
                    call_user_func_array($this->cloneRSModel, [&$result]);
                }
                $this->insert($result);
                if ($this->cloneType == self::CLONE_TYPE_MULTIPLE) {
                    $tbl = $this->getTable($this->cloneDsMode);
                    if ($tbl instanceof \zfx\AutoTable && $tbl->getSchema() instanceof \zfx\AutoSchema) {
                        $this->cloneRel($tbl, $id, PKView::unpack($this->insertResult));
                    }
                }
                if (is_callable($this->cloneModel)) {
                    call_user_func($this->cloneModel, $id, $this->insertResult);
                }
                if ($this->editAfterInsert) {
                    $_SESSION[$this->locId]['editablePK'] = $this->insertResult;
                }
                $this->lst();
            }
        }
        elseif ($this->cloneType == self::CLONE_TYPE_MANUAL) {
            // Use model
            if (is_callable($this->cloneModel)) {
                call_user_func($this->cloneModel, $id);
                $this->lst();
            }
            else {
                return;
            }
        }
        else {
            return;
        }
    }


    // --------------------------------------------------------------------

    protected function lstGetTableActions()
    {
        $actions = [];


        if (!trueEmpty($this->pickerTargetField)) {
            $selector       = '#_DBPicker_' . StrFilter::safeEncode($this->pickerTargetField);
            $data           = array(
                'adm-action'          => 'setval',
                'adm-setval-target-1' => $selector . '_hid',
                'adm-setval-value-1'  => '',
                'adm-setval-target-2' => $selector . '_disp',
                'adm-setval-value-2'  => '',
                'adm-setval-close'    => $selector . '_target'
            );
            $actions['sel'] = array(
                'url'  => 'javascript:void(0)',
                'en'   => array(
                    'Choose nothing',
                    Config::get('zaf-icon-ta-clean')
                ),
                'es'   => array(
                    'No elegir nada',
                    Config::get('zaf-icon-ta-clean')
                ),
                'data' => $data,
            );
        }


        if ($this->canAdd) {

            if (trueEmpty($this->addOtherClass)) {
                $data = array(
                    'adm-action'      => 'load',
                    'adm-load-target' => $this->addSelector,
                    'adm-load-source' => $this->_urlController() . 'add/',
                    'adm-options'     => 'autoFocus' . ($this->addExpand ? ' expand' : '')
                );
            }
            else {
                $data = array(
                    'adm-action'      => 'load',
                    'adm-load-target' => $this->addSelector,
                    'adm-load-source' => Config::getModRootUrl() . StrFilter::dashes($this->addOtherClass) . '/add/',
                    'adm-options'     => 'autoFocus' . ($this->addExpand ? ' expand' : '')
                );
            }

            $actions['add'] = array(
                'en'   => array(
                    'Add',
                    Config::get('zaf-icon-ta-add')
                ),
                'es'   => array(
                    'Añadir',
                    Config::get('zaf-icon-ta-add')
                ),
                'data' => $data,
                'url'  => 'javascript:void(0)'
            );
        }

        // Exportar
        if (!Config::get('zafMobile') && !Config::get('zafDisableExport')) {
            $actions['export'] = array(
                'en'   => array('Export', Config::get('zaf-icon-ta-export')),
                'es'   => array('Exportar', Config::get('zaf-icon-ta-export')),
                'menu' => array(
                    /*                    'expdfv'  => array(
                                            'en'     => array('View PDF', Config::get('zaf-icon-ta-export-pdfv')),
                                            'es'     => array('Ver PDF', Config::get('zaf-icon-ta-export-pdfv')),
                                            'url'    => $this->_urlController() . 'exlst/pdfv',
                                            'target' => '_blank',
                                        ),
                                        'expdf'   => array(
                                            'en'     => array('Download PDF', Config::get('')),
                                            'es'     => array('Descargar PDF', Config::get('')),
                                            'url'    => $this->_urlController() . 'exlst/pdf',
                                            'target' => '_blank',
                                        ), */
                    'exhtmlv' => array(
                        'en'     => array('View HTML format', Config::get('zaf-icon-ta-export-htmlv')),
                        'es'     => array('Ver formato HTML', Config::get('zaf-icon-ta-export-htmlv')),
                        'url'    => $this->_urlController() . 'exlst/htmlv',
                        'target' => '_blank',
                    ),
                    'exhtml'  => array(
                        'en'     => array('Download HTML format', Config::get('zaf-icon-ta-export-html')),
                        'es'     => array('Descargar formato HTML', Config::get('zaf-icon-ta-export-html')),
                        'url'    => $this->_urlController() . 'exlst/html',
                        'target' => '_blank',
                    ),
                    'excsve'  => array(
                        'en'     => array('Download CSV (Excel) ', Config::get('zaf-icon-ta-export-csve')),
                        'es'     => array('Descargar CSV (Excel)', Config::get('zaf-icon-ta-export-csve')),
                        'url'    => $this->_urlController() . 'exlst/csve',
                        'target' => '_blank',
                    ),
                    'excsv'   => array(
                        'en'     => array('Download CSV (other)', Config::get('zaf-icon-ta-export-csv')),
                        'es'     => array('Descargar CSV (otros)', Config::get('zaf-icon-ta-export-csv')),
                        'url'    => $this->_urlController() . 'exlst/csv',
                        'target' => '_blank',
                    ),
                    'excsvi'  => array(
                        'en'     => array('Download CSV (importable)', Config::get('zaf-icon-ta-export-csvi')),
                        'es'     => array('Descargar CSV (importable)', Config::get('zaf-icon-ta-export-csvi')),
                        'url'    => $this->_urlController() . 'exlst/csvi',
                        'target' => '_blank',
                    ),
                    'exexn'   => array(
                        'en'     => array('Download Excel XLSX', Config::get('zaf-icon-ta-export-xlsx')),
                        'es'     => array('Descargar Excel XLSX', Config::get('zaf-icon-ta-export-xlsx')),
                        'url'    => $this->_urlController() . 'exlst/exn',
                        'target' => '_blank',
                    ),
                )
            );

            if ($this->canImport) {
                // Imprtar
                $actions['import'] = array(
                    'en'   => array('Import', Config::get('zaf-icon-ta-import')),
                    'es'   => array('Importar', Config::get('zaf-icon-ta-import')),
                    'menu' => array(
                        'excsv' => array(
                            'en'   => array('From Excel CSV', Config::get('zaf-icon-ta-import-csv')),
                            'es'   => array('Desde CSV de Excel', Config::get('zaf-icon-ta-import-csv')),
                            'data' => array(
                                'adm-action'      => 'load',
                                'adm-load-target' => $this->impSelector,
                                'adm-load-source' => $this->_urlController() . 'impform/excsv/',
                                'adm-options'     => 'autoFocus'
                            ),
                            'url'  => 'javascript:void(0)'
                        )
                    )
                );
            }
        }
        return $actions;
    }

    // --------------------------------------------------------------------


    protected function insertData($data)
    {
        $data               = $this->prepareInsertData($data);
        $this->insertResult = NULL;
        if (nwcount($this->getSchema()->getPrimaryKey()) == 1) {
            // If the primary key is a single column, set to be returned on insert
            $returnCol          = current($this->getSchema()->getPrimaryKey());
            $this->insertResult = $this->getTable()->insertR($data, $returnCol);
            if (is_array($this->insertResult)) {
                trigger_error('Missing fields on insertR(): ' . print_r($this->insertResult, TRUE));
            }
            else {
                $this->insertResult = PKView::pack(array($returnCol => $this->insertResult));
            }
        }
        else {
            // If not, we will use the $data sent to Insert.
            $this->insertResult = $this->getTable()->insertR($data);
            if ($this->insertResult === TRUE) {
                $this->insertResult = PKView::pack($this->getSchema()->extractPk($data));
            }
            else {
                if (is_array($this->insertResult)) {
                    trigger_error('Missing fields on insertR(): ' . print_r($this->insertResult, TRUE));
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Preparamos los datos para ser insertados
     *
     * @param $data
     */
    protected function prepareInsertData($data)
    {
        /*
         * Si estamos bajo un sistema de relaciones automático, añadimos a los datos de inserción
         * la columna que indica la tabla relacionada padre.
         */
        $this->calcFilRel($data);
        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * Cuando estamos bajo un sistema de relaciones automático, calculamos la clave primaria de la tabla padre.
     * Si además se le pasa un array, deposita los datos en dicho array.
     * @param array|null $data
     * @return void
     */
    protected function calcFilRel(array &$data = NULL)
    {
        if ($this->hideFilRel) {
            if (isset($_SESSION[$this->schId]['cols'])) {
                foreach ($_SESSION[$this->schId]['cols'] as $k => $v) {
                    if (substr($k, 0, 1) == '_') {
                        if (is_array($data)) {
                            $data[$v] = $_SESSION[$this->schId]['values'][$k];
                        }
                        $this->filRel[$k] = $_SESSION[$this->schId]['values'][$k];
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------


    protected function insert($data)
    {
        // Insert data
        $this->insertData($data);

        // Si hay que editar después de insertar, el resultado de la inserción
        // queda exento de las limitaciones impuestas por $this->editIsView durante esta sesión.
        if ($this->editAfterInsert) {
            $_SESSION[$this->locId]['editablePK'] = $this->insertResult;
        }


        // Declare that we should remain in last page if applicable
        $this->stayOnLast = TRUE;

        // Bootstrap
        $this->calcLstBootStrap(self::MODE_ADD);
    }

    // --------------------------------------------------------------------

    public function cfg()
    {
        // A partir de este punto si hay fuentes alternativas de datos las usaremos.
        $this->checkEnableDsMode(self::MODE_LST);

        if (a($_POST, 'typ') == 'col') {
            $source   = StrFilter::safeDecode(a($_POST, 'src'));
            $dest     = StrFilter::safeDecode(a($_POST, 'des'));
            $newOrder = [];
            foreach ($this->getTableView()->getFieldOrder() as $f) {
                if ($f == $dest) {
                    $newOrder[] = $source;
                }
                if ($f != $source) {
                    $newOrder[] = $f;
                }
            }
            $this->getTableView()->setFieldOrder($newOrder);
            if (!isset($_SESSION[$this->cfgId])) {
                $_SESSION[$this->cfgId] = new stdClass();
            }
            $_SESSION[$this->cfgId]->fieldOrder = $newOrder;
        }
        $this->saveCfg();
        $this->lst();
    }

    // --------------------------------------------------------------------


    /**
     * Dibujar un bloque de vista de lista
     */
    public function lst()
    {
        if (!$this->canLst) {
            return;
        }

        // A partir de este punto si hay fuentes alternativas de datos las usaremos.
        $this->checkEnableDsMode(self::MODE_LST);

        if ($this->hideFilRel) {
            $this->removeFilRel();
        }

        // Apply filters, if any
        $filters = $this->applyCurrentFilters();
        if ($filters && $this->lstFilterDisablesPaginator) {
            $this->rowsPerPage = 0;
        }

        // Process page number (if any) or retrieve from session
        if ($this->rowsPerPage > 0) {
            if ($this->_processNumPage('p') === NULL) {
                $this->_numPage = (int)a($_SESSION, $this->pagId);
                if ($this->_numPage < 1) {
                    $this->_numPage = 1;
                }
            }
            else {
                $_SESSION[$this->pagId] = $this->_numPage;
            }
        }

        $this->lstSetupTableView();

        // TableViewer
        $tvr = $this->lstGetTableViewer();
        if (!$tvr) {
            return;
        }
        if ($this->canSort) {
            $sb = $this->lstGetSortButtons();
            if ($sb) {
                $tvr->setSortButton($sb);
            }
        }
        if ($this->rowsPerPage > 0) {
            $pag = $this->lstGetPaginator();
            if ($pag) {
                $tvr->setPaginator($pag);
            }
        }

        // Row actions for the tableviewer
        $tvr->createRowActions($this->lstGetActions());

        // Mostrar la vista
        $tvr->direct();

        // If we have drawn the last page, we will remember it
        $_SESSION[$this->lasId] = FALSE;
        $pag                    = $tvr->getPaginator();
        if ($pag && $pag->getCurrentPage() == $pag->getNumPages()) {
            $_SESSION[$this->lasId] = TRUE;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Quita del tableView aquellos campos que se están usando para mantener una relación con otra tabla (otro CRUD),
     * obteniéndolos de la sesión
     */
    protected function removeFilRel()
    {
        if (isset($_SESSION[$this->schId]['cols'])) {
            foreach ($_SESSION[$this->schId]['cols'] as $k => $v) {
                if (substr($k, 0, 1) == '_') {
                    $this->getTableView()->removeFields(array($v));
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Apply filter currently stored in session.
     */
    protected function applyCurrentFilters()
    {
        if (va($_SESSION[$this->schId]['filters'])) {
            $this->getTable()->setSqlFilter(implode(' AND ', $_SESSION[$this->schId]['filters']));
            return TRUE;
        }
        return FALSE;
    }

    // --------------------------------------------------------------------

    protected function lstSetupTableView()
    {
        // Let's set primary key to the sorting info array if empty
        $ofs = clone a($_SESSION, $this->ofsId);
        if (!$ofs->getSingle() && $this->safeSorting && !$ofs->getFieldSet()) {
            $pk = $this->getSchema()->getPrimaryKey();
            foreach ($pk as $p) {
                if ($ofs->getField($p) === NULL) {
                    $ofs->setField($p);
                }
            }
        }
        // Set sorting info
        $this->getTableView()->setOrderedFieldSet($ofs);

        // Remove unused fields
        $this->getTableView()->removeFields($this->lstHideFields);

        // Set all fieldviews to display-only
        $this->getTableView()->setAllDisplayOnly(TRUE);
    }

    // --------------------------------------------------------------------

    protected function lstGetTableViewer()
    {
        $tvr = new TableViewer();
        $tvr->setTableView($this->getTableView());
        $tvr->setSectionId($this->vSectionIDTable);
        $tvr->setSectionTemplate($this->vTemplateTable);

        if ($this->stayOnLast && a($_SESSION, $this->lasId) === TRUE) {
            $tvr->setStayOnLast(TRUE);
        }
        $tvr->setViewData(array(
            '_lang'           => $this->loc->getLang(),
            '_loc'            => $this->loc,
            '_actions'        => $this->lstGetTableActions(),
            '_headers'        => TRUE,
            '_bootStrap'      => $this->lstBootStrap,
            '_tvClass'        => $this->vTemplateTableCSS,
            'zjClickOnRow'    => $this->clkOnRow,
            '_fastInserter'   => $this->fastInserter,
            '_varTypeField'   => $this->varTypeField,
            '_varTypeOptions' => $this->varTypeOptions,
            '_varTypeOrigin'  => $this->varTypeOrigin,
            '_dbFileUrl'      => $this->dbFileUrl
        ));
        return $tvr;
    }

    // --------------------------------------------------------------------
    // Non-action methods that affect CRUD behavior
    // --------------------------------------------------------------------

    protected function lstGetSortButtons()
    {
        $sb = new SortButton();
        $sb->setTextToDESC('<i class="fas fa-sort-up"></i>');
        $sb->setTextToNUL('<i class="fas fa-sort-down"></i>');
        $sb->setTextToASC('<i class="fas fa-sort"></i>');
        $sb->setNulData(array(
            'adm-action'      => 'load',
            'adm-load-target' => $this->lstSelector,
            'adm-load-source' => $this->_urlController() . 'srt/nul/%1$s/'
        ));
        $sb->setAscData(array(
            'adm-action'      => 'load',
            'adm-load-target' => $this->lstSelector,
            'adm-load-source' => $this->_urlController() . 'srt/asc/%1$s/'
        ));
        $sb->setDesData(array(
            'adm-action'      => 'load',
            'adm-load-target' => $this->lstSelector,
            'adm-load-source' => $this->_urlController() . 'srt/des/%1$s/'
        ));
        return $sb;
    }

    // --------------------------------------------------------------------

    protected function lstGetPaginator()
    {
        $pag = new Paginator();
        $pag->setSeparator('');
        $pag->setNumItemsPerPage((int)$this->rowsPerPage);
        $pag->setConfig($this->lstGetPaginatorConfig($this->_urlController() . 'lst/', $this->lstSelector));
        $pag->setCurrentPage($this->_getNumPage());
        return $pag;
    }

    // --------------------------------------------------------------------

    protected function lstGetPaginatorConfig($sourceURL, $targetSelector)
    {
        if (Config::get('admTheme') == 'chm') {
            return array(
                array(
                    'type'   => 'previous',
                    'prefix' => NULL,
                    'code'   => "<a class=\"_paginatorButton\" href=\"javascript:void(0);\" data-adm-action=\"load\" data-adm-load-source=\"{$sourceURL}p/%1\$u\" data-adm-load-target=\"{$targetSelector}\"><img src=\"" . \zfx\Config::get('rootUrl') . "res/zaf/chm/icon/int-anterior.svg\"></a>",
                    'suffix' => NULL
                ),
                array(
                    'type'   => 'current',
                    'prefix' => NULL,
                    'code'   => '<div class="_paginatorNumber">Página <input class="_paginatorInput" value="%1$u" size="2">/%2$u</div>',
                    'suffix' => NULL
                ),
                array(
                    'type'   => 'next',
                    'prefix' => NULL,
                    'code'   => "<a class=\"_paginatorButton\" href=\"javascript:void(0);\" data-adm-action=\"load\" data-adm-load-source=\"{$sourceURL}p/%1\$u\" data-adm-load-target=\"{$targetSelector}\"><img src=\"" . \zfx\Config::get('rootUrl') . "res/zaf/chm/icon/int-siguiente.svg\"></a>",
                    'repeat' => NULL
                ),
                array(
                    'type'   => 'rowcount',
                    'prefix' => NULL,
                    'code'   => '<div class="_paginatorNumber" style="padding-top: 5px;">Total: %1$u</div>',
                    'suffix' => NULL
                )
            );
        }
        else if (Config::get('admTheme') == 'zth1') {
            return array(
                array(
                    'type'   => 'first',
                    'prefix' => NULL,
                    'code'   => "<a class=\"_paginatorButton\" href=\"javascript:void(0);\" data-adm-action=\"load\" data-adm-load-source=\"{$sourceURL}p/%1\$u\" data-adm-load-target=\"{$targetSelector}\"><i class=\"fas fa-fast-backward\"></i></a>",
                    'suffix' => NULL
                ),
                array(
                    'type'   => 'previous',
                    'prefix' => NULL,
                    'code'   => "<a class=\"_paginatorButton\" href=\"javascript:void(0);\" data-adm-action=\"load\" data-adm-load-source=\"{$sourceURL}p/%1\$u\" data-adm-load-target=\"{$targetSelector}\"><i class=\"fas fa-backward\"></i></a>",
                    'suffix' => NULL
                ),
                array(
                    'type'   => 'current',
                    'prefix' => NULL,
                    'code'   => '<div class="_paginatorNumber">Pag %1$u/%2$u</div>',
                    'suffix' => NULL
                ),
                array(
                    'type'   => 'next',
                    'prefix' => NULL,
                    'code'   => "<a class=\"_paginatorButton\" href=\"javascript:void(0);\" data-adm-action=\"load\" data-adm-load-source=\"{$sourceURL}p/%1\$u\" data-adm-load-target=\"{$targetSelector}\"><i class=\"fas fa-forward\"></i></a>",
                    'repeat' => NULL
                ),
                array(
                    'type'   => 'last',
                    'prefix' => NULL,
                    'code'   => "<a class=\"_paginatorButton\" href=\"javascript:void(0);\" data-adm-action=\"load\" data-adm-load-source=\"{$sourceURL}p/%1\$u\" data-adm-load-target=\"{$targetSelector}\"><i class=\"fas fa-fast-forward\"></i></a>",
                    'suffix' => NULL
                ),
                array(
                    'type'   => 'rowcount',
                    'prefix' => NULL,
                    'code'   => '<div class="_paginatorNumber">Total: %1$u</div>',
                    'suffix' => NULL
                )
            );
        }
    }

    // --------------------------------------------------------------------

    /**
     * Generar bloque de búsqueda (primaria o única)
     * @return void
     */
    public function sch()
    {
        if ($this->filterPrimary || $this->filterPrimaryBox || $this->filterPrimaryRange) {
            $this->searchBlock(TRUE);
        }
        else $this->searchBlock(NULL);
    }

    // --------------------------------------------------------------------

    /**
     * Generar bloque de búsqueda (secundaria o única)
     * @return void
     */
    public function schs()
    {
        $this->searchBlock(FALSE);
    }

    // --------------------------------------------------------------------

    /**
     * Función que genera un bloque de búsqueda, para ser llamada desde las acciones sch y schs.
     * @param boolean|null $primary Atención con este parámetro:
     * - Si es TRUE, solo mostrar las primarias.
     * - Si es FALSE, solo mostrar las secundarias.
     * - Si es NULL, mostrar todo.
     */
    protected function searchBlock($primary)
    {
        if (!$this->getSchema() || !$this->getTable() || !$this->getTableView()) {
            return;
        }

        // A partir de este punto si hay fuentes alternativas de datos las usaremos.
        $this->checkEnableDsMode(self::MODE_LST);

        if (!$primary === NULL || ($primary && $this->filterPrimaryBox) || (!$primary && !$this->filterPrimaryBox)) {
            $filterBoxes = $this->schGetFilterBoxes();
            foreach ($filterBoxes as $f) {
                $f->render();
            }
        }

        if (!$primary === NULL || ($primary && $this->filterPrimaryRange) || (!$primary && !$this->filterPrimaryRange)) {
            $filterRanges = $this->schGetFilterRanges();
            foreach ($filterRanges as $f) {
                $f->render();
            }
        }

        if ($primary === TRUE) {
            $filters = $this->schGetFilters($this->filterPrimary);
        }
        elseif ($primary === FALSE) {
            $filters = $this->schGetFilters($this->getTableView()->getFieldList($this->filterPrimary));
        }
        elseif ($primary === NULL) {
            $filters = $this->schGetFilters();
        }
        if ($filters) foreach ($filters as $f) {
            $f->render();
        }
    }

    // --------------------------------------------------------------------

    /**
     * Generate filter textboxes based on schema info
     *
     * @return array of DataFilterBox
     */
    protected function schGetFilterBoxes()
    {
        $filterBox = new DataFilterBox('filterBox_' . get_class($this));
        if ($this->filterBoxFields) {
            $filterBox->fields = $this->filterBoxFields;
            $filterBox->jsh    = $this->filterBoxJSH;
        }
        else {
            $filterBox->fields = [];
            foreach ($this->getTableView()->getFieldViews() as $fieldID => $field) {
                if (is_a($field, 'zfx\FieldViewStringMap')) {
                    continue;
                }
                if (in_array($fieldID, $this->filterDisabledFilters)) {
                    continue;
                }
                if ($this->getSchema()->getField($fieldID)) {
                    if (!$this->filterIndexedOnly || $this->getSchema()->getField($fieldID)->getIndex()) {
                        $filterBox->fields[$fieldID] = $field->getLabel();
                        $filterBox->jsh[$fieldID]    = $field->getJSH();
                    }
                }
            }
        }
        if (!$filterBox->fields) {
            return [];
        }
        $filterBox->searchValue = aa($_SESSION, $this->schId, 'values', $filterBox->id);
        $filterBox->selected    = aa($_SESSION, $this->schId, 'cols', $filterBox->id);

        if (trueEmpty($filterBox->selected) && !trueEmpty($this->filterBoxDefault)) {
            $filterBox->selected = $this->filterBoxDefault;
        }

        $filterBox->action      = $this->_urlController() . 'fil/';
        $filterBox->class       = 'zjNoSendKey' . ($this->schCompact ? ' _compact' : '');
        $filterBox->datas       = array(
            'adm-action'        => 'submit',
            'adm-submit-action' => $filterBox->action,
            'adm-submit-form'   => '#' . $filterBox->getFormID(),
            'adm-submit-target' => $this->lstSelector
        );
        $filterBox->searchLabel = $this->loc->getString('adm', 'label-search');
        $filterBox->inLabel     = $this->loc->getString('adm', 'label-in');
        return array($filterBox);
    }

    // --------------------------------------------------------------------

    /**
     * Generate filter range textboxes based on schema info
     *
     * @return array of DataFilterRange
     */
    protected function schGetFilterRanges()
    {
        $filterRange = new DataFilterRange('filterRange_' . get_class($this));

        if ($this->filterRangeFields) {
            $filterRange->fields = $this->filterRangeFields;
            $filterRange->jsh    = $this->filterRangeJSH;
        }
        else {
            $filterRange->fields = [];
            foreach ($this->getTableView()->getFieldViews() as $fieldID => $fieldView) {
                if (is_a($fieldView, 'zfx\FieldViewStringMap') || is_a($fieldView, 'zfx\FieldViewDBPicker')) {
                    continue;
                }
                if (in_array($fieldID, $this->filterDisabledFilters)) {
                    continue;
                }
                $field = $this->getSchema()->getField($fieldID);
                if ($field && $field::getBounded()) {
                    if (!$this->filterIndexedOnly || $field->getIndex()) {
                        $filterRange->fields[$fieldID] = $fieldView->getLabel();
                        $filterRange->jsh[$fieldID]    = $fieldView->getJSH();
                    }
                }
            }
        }
        if (!$filterRange->fields) {
            return [];
        }

        $filterRange->searchFromValue = aa($_SESSION, $this->schId, 'values', $filterRange->id . '_from');
        $filterRange->searchToValue   = aa($_SESSION, $this->schId, 'values', $filterRange->id . '_to');
        $filterRange->selected        = aa($_SESSION, $this->schId, 'cols', $filterRange->id);
        $filterRange->action          = $this->_urlController() . 'fil/';
        $filterRange->class           = 'zjNoSendKey' . ($this->schCompact ? ' _compact' : '');
        $filterRange->datas           = array(
            'adm-action'        => 'submit',
            'adm-submit-action' => $filterRange->action,
            'adm-submit-form'   => '#' . $filterRange->getFormID(),
            'adm-submit-target' => $this->lstSelector
        );
        $filterRange->fromLabel       = $this->loc->getString('adm', 'label-from');
        $filterRange->toLabel         = $this->loc->getString('adm', 'label-to');
        $filterRange->inLabel         = $this->loc->getString('adm', 'label-in');

        return array($filterRange);
    }

    // --------------------------------------------------------------------

    /**
     * Generate filter (dropdowns) inputs based on schema info
     *
     * @param array|null $filterList Solo mostrar los especificados o NULL para mostrar todos.
     * @return DataFilterList
     */
    protected function schGetFilters(array $filterList = NULL)
    {
        $filters = [];

        // Si no hay un criterio de ordenación, tomamos los filtros tal cual aparecen en el tableviewer.
        if (!$this->filterListOrder) {
            $fieldViewList = array_keys($this->getTableView()->getFieldViews());
        }
        // En caso de haberlo, entonces lo usamos.
        else {
            $fieldViewList = $this->filterListOrder;
            foreach (array_keys($this->getTableView()->getFieldViews()) as $fieldID) {
                if (!in_array($fieldID, $fieldViewList)) {
                    $fieldViewList[] = $fieldID;
                }
            }
        }

        foreach ($fieldViewList as $fieldID) {
            $fieldView = $this->getTableView()->getFieldView($fieldID);
            if (!$fieldView) continue;
            if ($filterList !== NULL) if (!in_array($fieldID, $filterList)) continue;
            if (!(is_a($fieldView, 'zfx\FieldViewStringMap') ||
                  is_a($this->getSchema()->getField($fieldID), 'zfx\FieldBoolean')
            )) {
                continue;
            }
            if (in_array($fieldID, $this->filterDisabledFilters)) {
                continue;
            }
            if ($this->filterIndexedOnly && !$this->getSchema()->getField($fieldID)->getIndex()) {
                continue;
            }
            $filter         = new DataFilterList('filter_' . get_class($this) . '_' . $fieldID);
            $filter->loc    = $this->loc;
            $filter->label  = $fieldView->getLabel();
            $filter->values = [];
            if (is_a($this->getSchema()->getField($fieldID), 'zfx\FieldBoolean')) {
                if (!$this->getSchema()->getField($fieldID)->getRequired()) {
                    $filter->values = array(
                        ''  => $this->loc->getNull(),
                        '0' => $this->loc->getBoolean(FALSE),
                        '1' => $this->loc->getBoolean(TRUE)
                    );
                }
                else {
                    $filter->values = array(
                        '0' => $this->loc->getBoolean(FALSE),
                        '1' => $this->loc->getBoolean(TRUE)
                    );
                }
            }
            else {
                if ($this->getSchema()->getField($fieldID)->getRequired()) {
                    $filter->values = $fieldView->getFilterMap();
                }
                else {
                    $filter->values = array('' => $this->loc->getNull()) + $fieldView->getFilterMap();
                }
            }
            $filter->col       = $fieldID;
            $filter->selected  = aa($_SESSION, $this->schId, 'values', $filter->id);
            $filter->action    = $this->_urlController() . 'fil/';
            $filter->class     = 'zjNoSendKey' . ($this->schCompact ? ' _compact' : '');
            $filter->datas     = array(
                'adm-action'        => 'submit',
                'adm-submit-action' => $filter->action,
                'adm-submit-form'   => '#' . $filter->getFormID(),
                'adm-submit-target' => $this->lstSelector
            );
            $filters[$fieldID] = $filter;
        }

        return $filters;
    }

    // --------------------------------------------------------------------

    /**
     * Render an edit form block
     * @param string $packedID
     */
    public function edt($packedID = '')
    {
        // A partir de este momento si hay para edición fuentes de datos alternativas las usaremos.
        $this->checkEnableDsMode(self::MODE_EDT);

        // Comprobamos si podemos editar
        if (!$this->canEdt || !$packedID) {
            return;
        }

        $editIsViewBackup = $this->editIsView;
        if ($this->editIsView && aa($_SESSION, $this->locId, 'editablePK') == $packedID) {
            $this->editIsView = FALSE;
        }
        $this->form($packedID);
        $this->editIsView = $editIsViewBackup;
    }

    // --------------------------------------------------------------------

    /**
     * Convierte el valor introducido en el formulario en un valor de BD.
     * @param $data
     * @param $varType
     * @param string $options
     * @return string|void|null
     */
    private function varTypeCast($data, $varType, $options = '')
    {
        if (!$varType) {
            return;
        }
        switch ($varType) {
            case DataVarType::TYPE_INTEGER:
            {
                return (int)$data;
            }
            case DataVarType::TYPE_FLOAT:
            {
                $num = new \zfx\Num($data);
                return $num->getVal();
            }
            case DataVarType::TYPE_DATE:
            {
                $value = $this->loc->interpretDate($data, TRUE);
                if (is_a($value, '\DateTime')) {
                    return $value->format(Config::get('dbDateFormat'));
                }
                else {
                    return '';
                }
            }
            case DataVarType::TYPE_TIME:
            {
                $value = $this->loc->interpretTime($data, TRUE);
                if (is_a($value, '\DateTime')) {
                    return $value->format(Config::get('dbTimeFormat'));
                }
                else {
                    return '';
                }
            }
            default:
                return $data;
        }
    }


    // --------------------------------------------------------------------

    protected function form($packedID = '')
    {
        if ($this->hideFilRel) {
            $this->removeFilRel();
        }

        $this->viewData['_lang']         = $this->loc->getLang();
        $this->viewData['_tabList']      = $this->tabList;
        $this->viewData['_tabForm']      = $this->tabForm;
        $this->viewData['_tabSelected']  = $this->tabSelected;
        $this->viewData['_formExpanded'] = $this->getTableView()->hasExpandedGroups();
        if ($packedID == '') {
            $this->viewData['_hideOtherTabs'] = $this->tabHideOnAdd;
        }
        else {
            $this->viewData['_hideOtherTabs'] = FALSE;
        }
        $this->view = new View($this->vTemplateForm, $this->viewData);
        $this->setupViewForm($packedID);
        $this->view->show($this->viewData);
    }

    // --------------------------------------------------------------------

    protected function setupViewForm($packedID = '')
    {
        // En caso de editar pero solo para ver, activamos unas opciones.
        if (!trueEmpty($packedID) && $this->editIsView) {
            $this->editableFields    = 'none';
            $this->edtShowSaveButton = FALSE;
        }

        // En un formulario, lógicamente, no necesitamos campos inline
        $this->getTableView()->disableInline();

        if ($this->editableFields === NULL || $this->editableFields == 'all') {
            $this->getTableView()->setAllEditable(TRUE, TRUE);
        }
        elseif ($this->editableFields === 'none') {
            $this->getTableView()->setAllEditable(FALSE, TRUE);
        }
        else {
            if (is_array($this->editableFields) && nwcount($this->editableFields) > 0) {
                $this->getTableView()->setAllEditable(FALSE, TRUE);
                foreach ($this->editableFields as $field) {
                    $fv = $this->getTableView()->getFieldView($field);
                    if ($fv) {
                        $fv->setEditable(TRUE);
                    }
                }
            }
        }

        if (is_array($this->nonEditableFields) && nwcount($this->nonEditableFields) > 0) {
            foreach ($this->nonEditableFields as $field) {
                $this->getTableView()->getFieldView($field)->setEditable(FALSE);
            }
        }
        // RowViewer
        $this->rvr = new RowViewer();
        $this->rvr->setTableView($this->getTableView());
        if ($this->tabForm != '') {
            $this->rvr->setSectionId($this->tabForm);
        }
        else {
            $this->rvr->setSectionId($this->vTemplateFormSectionID);
        }

        $this->rvr->setSectionTemplate($this->vTemplateFormSection);
        $this->rvr->setInterDeps($this->interDeps);
        $this->rvr->addViewData('_submitFormID', $this->vTemplateFormID);

        $this->viewData['_submitAction']         = $this->_urlController();
        $this->viewData['_submitTargetSelector'] = $this->lstSelector;
        $this->viewData['_closeTargetSelector']  = ($packedID == '' ? $this->addSelector : $this->edtSelector);
        $this->viewData['_loc']                  = $this->loc;

        if (trueEmpty($packedID)) {
            // It's an insert.
            if ($this->addAutoHideFields) { // Hide auto fields?
                foreach ($this->getTableView()->getFieldViews() as $key => $field) {
                    if ($field->getField()) {
                        if ($field->getField()->getAuto()) {
                            $this->addHideFields[] = $key;
                        }
                    }
                }
            }
            if ($this->defaultRS) {
                $this->rvr->setRS($this->defaultRS);
            }

            if ($this->addHideFields) {
                $this->getTableView()->removeFields(array_unique($this->addHideFields));
            }

            $this->viewData['_showSaveButton']  = $this->addShowSaveButton;
            $this->viewData['_showCloseButton'] = $this->addShowCloseButton;
            $this->viewData['_closeOnSave']     = $this->addCloseOnSave;
            $this->viewData['_feedbackOnSave']  = $this->addFeedbackOnSave;
            $this->viewData['_disableOnSave']   = TRUE;
            $this->viewData['_saveDatas']       = $this->addSaveDatas;
            $this->viewData['_cancelDatas']     = $this->addCancelDatas;
            $this->viewData['_submitFormID']    = $this->vTemplateFormID;
            $this->viewData['_noTarget']        = $this->noTarget;
        }
        else {
            // Se trata de la edición de un registro existente
            // Obtener el RecordSet
            $recordSet = $this->readR($packedID);
            $this->rvr->setRS($recordSet);

            // Si tenemos activado el mecanismo de tipo variable dependiente de
            // otro campo del mismo registro, lo activamos.
            if ($this->varTypeField) {
                DataVarType::varTypeChange($this->getTableView(), $this->varTypeField, $this->dbFileUrl, $this->loc, a($recordSet, $this->varTypeOrigin), a($recordSet, $this->varTypeOptions));
            }

            $this->viewData['_formHidden'] = array('_id' => $packedID);
            if ($this->edtHideFields) {
                $this->getTableView()->removeFields(array_unique($this->edtHideFields));
            }
            $this->viewData['_showSaveButton']  = $this->edtShowSaveButton;
            $this->viewData['_showCloseButton'] = $this->edtShowCloseButton;
            $this->viewData['_closeOnSave']     = $this->edtCloseOnSave;
            $this->viewData['_feedbackOnSave']  = $this->edtFeedbackOnSave;
            $this->viewData['_disableOnSave']   = FALSE;
            $this->viewData['_saveDatas']       = $this->edtSaveDatas;
            $this->viewData['_cancelDatas']     = $this->edtCancelDatas;
            $this->viewData['_submitFormID']    = $this->vTemplateFormID;
            $this->viewData['_noTarget']        = $this->noTarget;
        }
        $this->addRvrSection();
    }


    // --------------------------------------------------------------------


    protected function addRvrSection()
    {
        $this->rvr->addSection($this->view);
    }

    // --------------------------------------------------------------------

    /*
     * Read R from table
     */
    protected function readR($packedID = '')
    {
        return $this->getTable()->readR(PKView::unpack($packedID));
    }

    // --------------------------------------------------------------------

    /*
     * This function resets $this->view
     */

    /**
     * Render a creation form block
     */
    public function add()
    {
        if (!$this->canAdd) {
            return;
        }

        // A partir de este momento, si fuentes de datos alternativas, las usaremos
        $this->checkEnableDsMode(self::MODE_ADD);

        // Mostrar un formulario
        $this->form();
    }

    // --------------------------------------------------------------------

    /**
     * Delete and render a list block
     * @param string $packedID
     */
    public function del($packedID = '', $doList = 'true')
    {
        if (!$this->canDel || !$packedID) {
            return;
        }
        $this->stayOnLast = TRUE;
        $id               = PKView::unpack($packedID);
        $this->getTable()->deleteR($id);
        if ($doList == 'true') $this->lst();
    }

    // --------------------------------------------------------------------

    /**
     * Apply sort and render a list form
     * @param string $mode can be 'nul', 'asc', 'desc'
     * @param string $field a safe-encoded field ID
     */
    public function srt($mode = '', $field = '')
    {
        if (!$mode || !$field || !$this->canSort || !$this->canLst || !$this->getSchema() || !$this->getTable() || !$this->getTableView()) {
            return;
        }
        $field = StrFilter::safeDecode($field);
        $this->setDsMode(self::MODE_LST);
        if ($this->getSchema()->getField($field)) {
            if ($mode == 'nul') {
                $_SESSION[$this->ofsId]->removeField($field);
            }
            else {
                $_SESSION[$this->ofsId]->setField($field,
                    ($mode == 'asc' ? OrderedFieldSet::ORDER_ASCENDING : OrderedFieldSet::ORDER_DESCENDING));
            }
        }
        $this->lst();
    }

    // --------------------------------------------------------------------

    /**
     * Aplicar filtros y después listar llamando a lst().
     *
     * @param string $id ID del filtro con codificación segura.
     * @param string $fieldName ID del campo con codificación segura.
     * @param string $value ID del valor con codificación segura.
     */
    public function fil($id = '', $fieldName = '', $value = '')
    {
        // A partir de este punto si hay fuentes alternativas de datos las usaremos.
        $this->checkEnableDsMode(self::MODE_LST);
        /*
         * Obtener los datos bien mediante la URL (segmentos) o desde POST.
         * Nótese que desde POST se obtienen más parámetros (valueFrom, valueTo) que desde la URL.
         */
        if (!trueEmpty($id)) {
            /*
             * Como tomamos los parámetros desde la URL hay que descodificarlos.
             */
            $id        = StrFilter::safeDecode($id);
            $fieldName = StrFilter::safeDecode($fieldName);
            $value     = StrFilter::safeDecode($value);
        }
        else {
            /*
             * Desde POST algunos parámetros vienen sin codificación segura, pues no es necesaria.
             */
            $id        = a($_POST, 'id');
            $fieldName = StrFilter::safeDecode(a($_POST, 'col'));
            $value     = a($_POST, 'text');
            $valueFrom = a($_POST, 'textFrom');
            $valueTo   = a($_POST, 'textTo');
        }

        // Construimos filtros SQL en base a los parámetros recibidos.
        if (trueEmpty($valueFrom) && trueEmpty($valueTo)) {
            $filter = $this->buildFilter($fieldName, $value);
        }
        else {
            $filter = $this->buildRangeFilter($fieldName, $valueFrom, $valueTo);
        }

        /*
         * Aplicamos el filtro, introduciendo la info en la sesión.
         * @@@ esto se puede mejorar, pero bueno, funciona
         */
        $this->applySessionFilter($id, $fieldName, $value, $valueFrom, $valueTo, $filter);

        /*
         * Reset del número de página, pues no sabemos dónde vamos a acabar con el filtro
         */
        unset($_SESSION[$this->pagId]);

        /*
         * Listado, que es el fin último de esta función.
         */
        $this->lst();
    }

    // --------------------------------------------------------------------

    protected function buildFilter($fieldName = '', $value = '')
    {
        // No field specified? No filter
        // @@@ Ver si no rompe nada devolver "FALSE" en vez de una cadena vacía.
        if (trueEmpty($fieldName)) {
            return '';
        }
        // This is weird
        $field  = $this->getSchema()->getField($fieldName);
        $filter = $field::cond(
            $fieldName,
            $field::castHttp($value, $this->loc, $field->getRequired()),
            $field->getRequired(),
            ($this->filterUseQualifiers ? $this->getSchema()->getRelationName() : '')
        );
        return $filter;
    }

    // --------------------------------------------------------------------

    protected function buildRangeFilter($fieldName = '', $valueFrom = '', $valueTo = '')
    {
        if (trueEmpty($fieldName)) {
            return '';
        }
        // This is weird
        $field  = $this->getSchema()->getField($fieldName);
        $filter = $field::condRange(
            $fieldName,
            $field::castHttp($valueFrom, $this->loc, $field->getRequired()),
            $field::castHttp($valueTo, $this->loc, $field->getRequired()),
            $field->getRequired(),
            ($this->filterUseQualifiers ? $this->getSchema()->getRelationName() : '')
        );
        return $filter;
    }

    // --------------------------------------------------------------------

    /**
     * Apply a filter to current session
     *
     * @param string $id Filter Identification
     * @param string $col Column
     * @param string $text Text value. Filter on this text or filter on (from, to) pair.
     * @param string $textFrom From value (if any)
     * @param string $textTo To value (if any)
     * @param string $filter SQL Filter. If empty the filter will be removed from session.
     * @param string $info Texto descriptivo opcional
     */
    protected function applySessionFilter($id, $col = '', $text = '', $textFrom = '', $textTo = '', $filter = '', $info = '')
    {
        // Hemos reimplementado esta función como estática porque nos conviene llamarla desde fuera a veces.
        self::sessionFilter($this->schId, $id, $col, $text, $textFrom, $textTo, $filter, $info);
    }

    // --------------------------------------------------------------------

    protected function getSessionFilter($id)
    {

        if (
            array_key_exists($this->schId, $_SESSION) &&
            array_key_exists('filters', $_SESSION[$this->schId]) &&
            is_array($_SESSION[$this->schId]['filters']) &&
            array_key_exists($id, $_SESSION[$this->schId]['filters'])
        ) {
            return array(
                'value'  => $_SESSION[$this->schId]['values'][$id],
                'from'   => $_SESSION[$this->schId]['values'][$id . '_from'],
                'to'     => $_SESSION[$this->schId]['values'][$id . '_to'],
                'col'    => $_SESSION[$this->schId]['cols'][$id],
                'filter' => $_SESSION[$this->schId]['filters'][$id],
                'info'   => $_SESSION[$this->schId]['info'][$id],
            );
        }
        else {
            return NULL;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Apply foreign key filter condition and render list block
     *
     * @param string $fk packed foreign table ID
     * @param string $relName safe-encoded schema relation name
     */
    public function rel($fk, $relName)
    {
        // A partir de este punto si hay fuentes alternativas de datos las usaremos.
        $this->checkEnableDsMode(self::MODE_LST);

        $relName = StrFilter::safeDecode($relName);
        $fk      = PKView::unpack($fk);
        $rel     = a($this->getSchema()->getFks(), $relName);
        if ($rel) {
            foreach ($fk as $fkCol => $fkValue) {
                // Construct filter
                $filter = $this->buildFilter($rel->getLocalOf($fkCol), $fkValue);
                // Apply filter (if any)
                $this->applySessionFilter("_$fkCol", $rel->getLocalOf($fkCol), $fkValue, '', '', $filter);
            }
        }
        // After filtering, list
        $this->lst();
    }

    // --------------------------------------------------------------------

    /**
     * Reset all session data
     */
    protected function resetAll()
    {
        unset($_SESSION[$this->pagId]);
        unset($_SESSION[$this->schId]);
        unset($_SESSION[$this->lasId]);
        unset($_SESSION[$this->ofsId]);
        unset($_SESSION[$this->locId]);
        $this->setupSessionSortInfo();
    }

    // --------------------------------------------------------------------

    protected function setLstOtherClass($className)
    {
        $this->lstOtherClass = $className;
        $this->lstSelector   = "#lst_" . $className;
    }

    // --------------------------------------------------------------------

    protected function setAddOtherClass($className)
    {
        $this->addOtherClass = $className;
        $this->addSelector   = "#add_" . $className;
    }

    // --------------------------------------------------------------------

    protected function setEdtOtherClass($className)
    {
        $this->edtOtherClass = $className;
        $this->edtSelector   = "#edt_" . $className;
    }

    // --------------------------------------------------------------------

    /**
     * Add (or replace) data to a $postData set
     * @param array $postData
     * @param string $fieldId
     * @param string $value
     */
    protected function addPostData(&$postData, $fieldId, $value)
    {
        $postData['_' . StrFilter::safeEncode($fieldId)] = $value;
    }

    // --------------------------------------------------------------------

    /**
     * Get the raw value of a $postData key
     * @param array $postData
     * @param string $fieldId
     * @return string
     */
    protected function getPostData(&$postData, $fieldId)
    {
        return a($postData, '_' . StrFilter::safeEncode($fieldId));
    }

    // --------------------------------------------------------------------

    protected function setPostData(&$postData, $fieldId, $value)
    {
        $postData['_' . StrFilter::safeEncode($fieldId)] = $value;
    }

    // --------------------------------------------------------------------

    protected function isUpdate(array &$postData)
    {
        $oldId = PKView::unpack(a($postData, '_id'));
        if ($oldId) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    // --------------------------------------------------------------------

    protected function getLastId($postData)
    {
        if ($this->isUpdate($postData)) {
            $id = (int)\zfx\PKView::unpack(\zfx\a($postData, '_id'), 'id');
        }
        else {
            $id = (int)\zfx\a($this->postDataPK, 'id');
        }
        return $id;
    }

    // --------------------------------------------------------------------

    /**
     * A partir del nombre de una tabla (o VIEW), inicializa el Schema, Table y TableView que son las fuentes
     * de datos que se usarán en el CRUD.
     *
     * Opcionalmente se puede especificar un ID de modo para que dichos objetos se guarden en la lista de fuentes
     * de datos alternativas bajo dicho modo.
     *
     * También se le puede pasar, opcionalmente, un Schema del que se importarán las propiedades clave:
     * Clave primaria, índices, etc. Útil si estamos inicializando a partir de una VIEW.
     *
     * @param $tableName
     * @param $profile
     * @param $ds
     * @param Schema|null $importSchema
     * @return void
     */
    protected function auto($tableName, $profile = NULL, int $ds = NULL, Schema $importSchema = NULL)
    {
        $s = new AutoSchema($tableName, $profile);
        if ($importSchema != NULL) {
            $s->importViewInfo($importSchema);
        }
        $t = new AutoTable($s, $profile);
        $v = TableView::auto($t);

        if ($ds === NULL) {
            $this->schema    = $s;
            $this->table     = $t;
            $this->tableView = $v;
        }
        else {
            $this->setDsAutoEnable(TRUE);
            $this->dsSchemas[$ds]    = $s;
            $this->dsTables[$ds]     = $t;
            $this->dsTableViews[$ds] = $v;
        }
    }


    // --------------------------------------------------------------------

    /**
     * Promotes local fieldview to a StringMap using a rel table column as data
     * @param string $relName Foreign key constraint name
     * @param string $fieldToShow Field to show from related table. Usually 'name'.
     * @param string $sqlFilter SQL Filter (without 'WHERE' clause) to apply to the related table
     * @param string $sqlSortBy SQL Order by (needs 'ORDER BY' clause) to apply to the related table
     */
    protected function relName($relName, $fieldToShow, $sqlFilter = NULL, $sqlSortBy = NULL, $dsMode = NULL)
    {
        $fk = a($this->getSchema($dsMode)->getFks(), $relName);
        if ($fk) {
            $relTableName   = $fk->getRelation();
            $localFieldName = current($fk->getLocalColumns());
            $relFieldName   = current($fk->getForeignColumns());
            if ($sqlSortBy === NULL) {
                $sqlSortBy = "ORDER BY " . DB::quote($fieldToShow);
            }
            $localField = FieldViewStringMap::promote($this->getTableView($dsMode)->getFieldView($localFieldName));
            $relTable   = new AutoTable(new AutoSchema($relTableName, $this->getTable($dsMode)->getProfile()), $this->getTable($dsMode)->getProfile());
            if ($sqlFilter !== NULL) {
                $relTable->setSqlFilter($sqlFilter);
            }
            if ($sqlSortBy !== NULL) {
                $relTable->setSqlSortBy($sqlSortBy);
            }
            if ($this->getSchema($dsMode)->getField($relFieldName) && $this->getSchema($dsMode)->getField($relFieldName)->getRequired()) {
                $localField->setMap($relTable->readRS(0, 0, FALSE, $relFieldName, $fieldToShow));
            }
            else {
                $localField->setMap(array('' => $this->loc->getNull()) + (array)$relTable->readRS(0, 0, FALSE, $relFieldName, $fieldToShow));
            }
            $this->getTableView($dsMode)->setFieldView($localFieldName, $localField);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Add related (child) table CRUD to form template
     *
     * @param string $packedID Parent table ID (packed)
     * @param string $relName Name of child table foreign key constraint pointing to the parent table
     * @param string $controller Name of child table CRUD controller
     * @param string $title Title to be shown
     * @param string $section View section that we will use to embed into
     * @param string $cssClass Parameter _cssClass to be passed to Crud Bootstrap List
     * @param string $selector Parameter _selector to be passed to Crud Bootstrap List
     */
    protected function addFrmSectionRel(
        $packedID,
        $relName,
        $controller,
        $title,
        $section = 'footer',
        $cssClass = '',
        $selector = '',
        $module = TRUE,
        $showFilter = FALSE,
        $titleFilter = ''
    )
    {
        $relName  = StrFilter::safeEncode($relName);
        $viewData = array(
            '_url'             => ($module ? Config::getModRootUrl() : Config::get('rootUrl')) . StrFilter::dashes($controller) . "/rel/$packedID/$relName/",
            '_controller'      => $controller,
            '_title'           => $title,
            '_loc'             => $this->loc,
            '_showFilterInTab' => $showFilter,
            '_secondary'       => TRUE,
        );
        if (!trueEmpty($cssClass)) {
            $viewData['_cssClass'] = $cssClass;
        }
        if (!trueEmpty($selector)) {
            $viewData['_selector'] = $selector;
        }
        if ($showFilter) $this->view->addSection($section, 'zaf/' . Config::get('admTheme') . '/crud-bootstrap-search', ['_title' => $titleFilter, '_controller' => $controller]);
        $this->view->addSection($section, 'zaf/' . Config::get('admTheme') . '/crud-bootstrap-list', $viewData);
    }

    // --------------------------------------------------------------------

    /**
     * Apply current orderedfieldset (from tableView) to the table instance
     * This method is presented here for convenience (ex. export data)
     * but is not used by the CRUD system.
     */
    protected function applyCurrentOrder()
    {
        $this->getTableView()->setOrderedFieldSet(a($_SESSION, $this->ofsId));
        if ($this->getTableView()->getOrderedFieldSet()) {
            $this->getTableView()->getTable()->setSqlSortBy(SQLGen::getOrderBy($this->getTableView()->getOrderedFieldSet()->getFieldSet()));
        }
    }

    // --------------------------------------------------------------------


    /**
     * Exportar lista. Ya qué mas da, vamos a darle de comer a este monstruo de clase :)
     */
    public function exlst($formato)
    {
        $this->setDsMode($this->dsModeExport);
        if (!$this->canLst || !$this->getSchema() || !$this->getTable() || !$this->getTableView()) {
            return;
        }
        if (Config::get('zafDisableExport')) {
            return;
        }

        /*
         * Aplicamos los filtros si es que hay y también el orden especificado.
         */
        $this->applyCurrentFilters();
        $this->applyCurrentOrder();

        /*
         * Generamos el documento
         */
        $db = $this->getTableView()->getTable()->readRS(0, 0, TRUE);
        if (!$db) {
            return;
        }

        /*
         * Si no hay ninguna lista de campos, usamos los filtros del listado si es que existe
         */
        if ($this->lstHideFields && !a($this->expFields, $formato)) {
            $this->expFields[$formato] = array_diff(
                array_keys($this->getTableView()->getFieldViews()),
                $this->lstHideFields
            );
        }

        if ($formato == 'html') {
            TableExport::html($db, $this->getTableView(), a($this->expFields, $formato), FALSE);
        }
        elseif ($formato == 'htmlv') {
            TableExport::html($db, $this->getTableView(), a($this->expFields, $formato), TRUE);
        }
        elseif ($formato == 'csv') {
            TableExport::csv($db, $this->getTableView(), a($this->expFields, $formato));
        }
        elseif ($formato == 'csve') {
            TableExport::csv($db, $this->getTableView(), a($this->expFields, $formato), 'excel');
        }
        elseif ($formato == 'csvi') {
            TableExport::csv($db, $this->getTableView(), a($this->expFields, $formato), 'excel', TRUE, FALSE, $this->expFieldMapper);
        }
        elseif ($formato == 'pdf') {
            TableExport::pdf($db, $this->getTableView(), a($this->expFields, $formato), FALSE);
        }
        elseif ($formato == 'pdfv') {
            TableExport::pdf($db, $this->getTableView(), a($this->expFields, $formato), TRUE);
        }
        elseif ($formato == 'exn') {
            TableExport::exn($db, $this->getTableView(), a($this->expFields, $formato), $this->tableViewImages ?: $this->tableView);
        }

    }

    // --------------------------------------------------------------------

    protected function createFastInserter($insertAction, $searchAction = '')
    {
        $this->fastInserter = new FastInserter();
        if (!\zfx\trueEmpty($insertAction)) {
            $this->fastInserter->setAxInsertUrl($this->_urlController() . $insertAction);
        }
        if (!\zfx\trueEmpty($searchAction)) {
            $this->fastInserter->setAxSearchUrl($this->_urlController() . $searchAction);
        }
        $this->fastInserter->setTarget($this->lstSelector);
    }

    // --------------------------------------------------------------------

    public function impform($importType)
    {
        $this->view = new View($this->vImportForm);
        $data       = array(
            'type'                  => $importType,
            '_loc'                  => $this->loc,
            '_submitAction'         => $this->_urlController() . "imp/$importType/",
            '_submitTargetSelector' => $this->lstSelector,
            '_closeTargetSelector'  => $this->impSelector,
            'campos'                => $this->impObtCampos($importType),
            'camposReq'             => $this->impObtCamposReq($importType),
        );
        $this->view->show($data);
    }

    // --------------------------------------------------------------------

    protected function impObtCampos($tipo)
    {
        if ($tipo == 'excsv') {
            return implode(";", $this->getTable()->getSchema()->getFieldsKeys());
        }
    }

    // --------------------------------------------------------------------

    protected function impObtCamposReq($tipo)
    {
        if ($tipo == 'excsv') {
            return implode(";", $this->getTable()->getSchema()->getFieldsKeys(TRUE));
        }
    }

    // --------------------------------------------------------------------

    public function imp($importType)
    {
        $errorTxt = '';
        if (array_key_exists('upfile', $_FILES) && $_FILES['upfile']['error'] == 0) {
            $errorTxt = \zfx\CrudTools::importFile($_FILES['upfile']['tmp_name'], $importType, $this->getTableView());
        }

        if ($errorTxt != '') {
            View::direct($this->vImportFormError, array('errorTxt' => $errorTxt));
        }
        $this->lstBootStrap = "<script>actionClose('{$this->impSelector}');</script>";
        $this->lst();
    }

    // --------------------------------------------------------------------

    /**
     * Apply a filter to current session
     *
     * @param string $id Filter Identification
     * @param string $col Column
     * @param string $text Text value. Filter on this text or filter on (from, to) pair.
     * @param string $textFrom From value (if any)
     * @param string $textTo To value (if any)
     * @param string $filter SQL Filter. If empty the filter will be removed from session.
     * @param string $info Texto descriptivo opcional
     */
    static function sessionFilter($schId, $id, $col = '', $text = '', $textFrom = '', $textTo = '', $filter = '', $info = '')
    {
        if (trueEmpty($filter)) {
            unset($_SESSION[$schId]['values'][$id . '_from']);
            unset($_SESSION[$schId]['values'][$id . '_to']);
            unset($_SESSION[$schId]['values'][$id]);
            unset($_SESSION[$schId]['filters'][$id]);
            unset($_SESSION[$schId]['cols'][$id]);
            unset($_SESSION[$schId]['info'][$id]);
        }
        else {
            $_SESSION[$schId]['values'][$id]           = $text;
            $_SESSION[$schId]['values'][$id . '_from'] = $textFrom;
            $_SESSION[$schId]['values'][$id . '_to']   = $textTo;
            $_SESSION[$schId]['cols'][$id]             = $col;
            $_SESSION[$schId]['filters'][$id]          = '(' . $filter . ')';
            $_SESSION[$schId]['info'][$id]             = $info;
        }
    }

    // --------------------------------------------------------------------


    // --------------------------------------------------------------------
    // - FUNCIONES DE ACCESO ESPECIAL -------------------------------------
    // --------------------------------------------------------------------

    protected function getTableView($dsMode = NULL): \zfx\TableView
    {
        if ($dsMode === NULL) {
            if ($this->getDsMode() == NULL) {
                return $this->tableView;
            }
            else {
                return (a($this->dsTableViews, $this->dsMode) ?: $this->tableView);
            }
        }
        else {
            if ($dsMode === FALSE) {
                return $this->tableView;
            }
            else {
                return (a($this->dsTableViews, $dsMode) ?: $this->tableView);
            }
        }
    }

    // --------------------------------------------------------------------

    protected function getTable($dsMode = NULL): \zfx\Table
    {
        if ($dsMode === NULL) {
            if ($this->getDsMode() === NULL) {
                return $this->table;
            }
            else {
                return (a($this->dsTables, $this->dsMode) ?: $this->table);
            }
        }
        else {
            if ($dsMode === FALSE) {
                return $this->table;
            }
            else {
                return (a($this->dsTables, $dsMode) ?: $this->table);
            }
        }
    }

    // --------------------------------------------------------------------

    protected function getSchema($dsMode = NULL): Schema
    {
        if ($dsMode === NULL) {
            if ($this->getDsMode() === NULL) {
                return $this->schema;
            }
            else {
                return (a($this->dsSchemas, $this->dsMode) ?: $this->schema);
            }
        }
        else {
            if ($dsMode === FALSE) {
                return $this->schema;
            }
            else {
                return (a($this->dsSchemas, $dsMode) ?: $this->schema);
            }
        }
    }

    // --------------------------------------------------------------------

    public function checkEnableDsMode($dsMode = NULL)
    {
        if ($this->isDsAutoEnable()) {
            $this->setDsMode($dsMode);
        }
    }

    // --------------------------------------------------------------------

    public function checkDisableDsMode()
    {
        if ($this->isDsAutoEnable()) {
            $this->setDsMode();
        }
    }

    // --------------------------------------------------------------------
    // -GETTERS Y SETTERS -------------------------------------------------
    // --------------------------------------------------------------------

    /**
     * @return int
     */
    public function getDsMode(): ?int
    {
        return $this->dsMode;
    }

    // --------------------------------------------------------------------

    /**
     * @param int|null $dsMode
     */
    public function setDsMode(int $dsMode = NULL): void
    {
        $this->dsMode = $dsMode;
    }

    // --------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isDsAutoEnable(): bool
    {
        return $this->dsAutoEnable;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed $dsAutoEnable
     */
    public function setDsAutoEnable($dsAutoEnable): void
    {
        $this->dsAutoEnable = (bool)$dsAutoEnable;
    }

    // --------------------------------------------------------------------

    // Otro backend más: para la edición inline
    public function inl()
    {
        $data  = RecordSet::processPOST($_POST, $this->getSchema(), $this->loc);
        $oldId = PKView::unpack(a($_POST, '_id'));
        if ($oldId) {
            $this->update($oldId, $data);
            $this->postDataPK = $oldId;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Configurar un fieldview para que se pueda hacer edición inline.
     *
     * @param string $idFieldView El ID del campo
     * @param integer|null $dsMode Modo para elegir un TableView diferente.
     * @return void
     */
    protected function setFVInline($idFieldView, $dsMode = NULL)
    {
        $fv = $this->getTableView($dsMode)->getFieldView($idFieldView);
        if ($fv) {
            $fv->setInlineEdit(TRUE);
            $fv->setInlineBackend($this->_urlController() . 'inl/');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Teniendo dos filas de una misma tabla, clona todas las relacionadas con la primera, relacionandolas con la segunda.
     *
     * @param \zfx\AutoTable $table La tabla con la que se trabaja
     * @param array $or Una clave primaria desempaquetada (array devuelto por PKView) de la fila origen (la que se ha clonado)
     * @param array $de Una clave primaria desempaquetada (array devuelto por PKView) de la fila destino (el producto de la clonación)
     * @return void
     */
    protected function cloneRel(\zfx\AutoTable $table, array $or, array $de)
    {
        // Datos básicos
        $sch = $table->getSchema();
        $sch->calculateRelTables();
        $rels = $sch->getRelTables();
        if (!$rels) return;
        // Obtengo la fila completa que fue clonada
        $orRS = $table->readR($or);
        $deRS = $table->readR($de);
        foreach ($rels as $fk) {

            // Inclusión de campos/tablas
            // Solo si tenemos una lista explícita a clonar
            $inCampos = [];
            if ($this->cloneMultipleIncludeFields) {
                // Si la tabla no se encuentra en ella, la saltamos
                if (!array_key_exists($fk->getRelation(), $this->cloneMultipleIncludeFields)) continue;
                // Veamos ahora si contiene una lista de campos
                $include = \zfx\a($this->cloneMultipleIncludeFields, $fk->getRelation());
                if ($include != '_') {
                    if (is_string($include) && !\zfx\trueEmpty($include)) $inCampos = [$include];
                    elseif (\zfx\va($include)) $inCampos = $include;
                }
            }

            // Exclusión de campos/tablas
            $exCampos = [];
            $exclude  = \zfx\a($this->cloneMultipleExcludeFields, $fk->getRelation());
            if ($exclude == '_') continue;
            elseif (is_string($exclude) && !\zfx\trueEmpty($exclude)) $exCampos = [$exclude];
            elseif (\zfx\va($exclude)) $exCampos = $exclude;

            // Buscamos todas las filas implicadas
            $rSch   = new \zfx\AutoSchema($fk->getRelation(), $sch->getProfile());
            $rTbl   = new \zfx\AutoTable($rSch, $sch->getProfile());
            $filter = [];
            foreach ($fk->getLocalColumns() as $lcol) {
                $fcol     = $fk->getForeignOf($lcol);
                $filter[] = "(" . $rSch->getField($lcol)::cond($lcol, $orRS[$fcol]) . ")";
            }
            $rTbl->setSqlFilter(implode(' and ', $filter));
            $filas = $rTbl->readRS();
            if (!$filas) continue;

            // Y ahora para cada fila,
            foreach ($filas as &$ds) {
                // Voy a querer una copia del dataset que clonaré
                $copiaDS = $ds;

                // Cambio en cada fila las que apuntaban a la fila OR haciendo que apunten a la fila DE.
                foreach ($fk->getLocalColumns() as $lcol) {
                    $fcol      = $fk->getForeignOf($lcol);
                    $ds[$lcol] = $deRS[$fcol];
                }

                if ($inCampos) {
                    // Si teníamos una lista de inclusión, vamos a eliminar todos los campos que no vayan incluidos
                    foreach ($copiaDS as $campo => $valor) {
                        if (!in_array($campo, $inCampos)) {
                            unset($ds[$campo]);
                        }
                    }
                }
                else {
                    // Si no teníamos una lista de inclusión, aplicamos una exclusión estándar
                    // Eliminar la clave primaria, pero solo si no son las únicas columnas, claro.
                    if (count($rSch->getPrimaryKey()) < count($ds)) {
                        foreach ($rSch->getPrimaryKey() as $pkCol) {
                            // Si la clave primaria forma parte de la propia relación, también me estoy quietecito
                            if (in_array($pkCol, $fk->getLocalColumns())) continue;
                            unset($ds[$pkCol]);
                        }
                    }
                    // También eliminaré todos los campos que deban ser excluidos
                    if ($exCampos) foreach ($exCampos as $campo) {
                        unset($ds[$campo]);
                    }
                }

                // La inserto
                // Esto no va a funcionar con MySQL todavía porque insertR no tiene soporte para un returnColumns array.
                $resultado = $rTbl->insertR($ds, $rSch->getPrimaryKey());

                // Llamo recursivamente a la propia función para a su vez clonar las relacionadas con la fila insertada
                if ($resultado) $this->cloneRel($rTbl, $copiaDS, $resultado);
            }
        }
    }

    // --------------------------------------------------------------------


}
