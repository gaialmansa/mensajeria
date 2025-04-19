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

class CrudTableDef
{
    public $table;
    public $sectionTitle;
    public $subsectionTitle = '';
    public $crudTitle;
    public $controllerName;
    public $module          = '';
    public $fields          = [];
    public $permission      = '';
    public $sqlDDLFile      = '';
    public $phpMenuFile     = '';
    public $icon            = '';
    public $groups          = [];
    public $sectionRels     = [];


    // --------------------------------------------------------------------


    protected $files = [];
    protected $modes = [];

    // --------------------------------------------------------------------


    public function generate()
    {
        $error = $this->check();
        if ($error != '') {
            \zfx\Debug::show($error);
            \zfx\Debug::show($this);
            die;
        }

        if (!trueEmpty($this->sectionTitle)) {
            $this->generateController();
        }
        $this->generateSQL();
        if (!trueEmpty($this->sectionTitle)) {
            $this->generateMenu();
        }
        $this->generateCrudController();
        $this->createFiles();
    }

    // --------------------------------------------------------------------

    protected function createFiles()
    {
        foreach ($this->files as $name => $content) {
            \zfx\Debug::show("Escribiendo fichero: " . $name);
            $f = fopen($name, $this->modes[$name]);
            if (!$f) {
                \zfx\Debug::show("No se pudo abrir el fichero.");
                continue;
            }
            fwrite($f, $content);
            fclose($f);
            chmod($name, 0777);
        }
    }

    // --------------------------------------------------------------------

    protected function check()
    {
        if (trueEmpty($this->crudTitle)) {
            $this->crudTitle = $this->subsectionTitle;
        }
        if (trueEmpty($this->crudTitle)) {
            $this->crudTitle = $this->sectionTitle;
        }

        if ($this->controllerName == '') {
            return "Se necesita [controllerName].";
        }

        if (trueEmpty($this->table)) {
            return "Se necesita [table]";
        }

        if (!$this->fields) {
            return "Se necesitan campos";
        }

        return '';
    }

    // --------------------------------------------------------------------

    protected function getControllerPath()
    {
        if ($this->module == '') {
            return \zfx\Config::get('controllerPath');
        }
        else {
            return \zfx\Config::get('appPath') . 'modules/' . $this->module . '/controllers/';
        }
    }

    // --------------------------------------------------------------------

    protected function getModelPath()
    {
        if ($this->module == '') {
            return \zfx\Config::get('modelPath');
        }
        else {
            return \zfx\Config::get('appPath') . 'modules/' . $this->module . '/models/';
        }
    }

    // --------------------------------------------------------------------

    protected function getSep()
    {
        return "\n\n    // --------------------------------------------------------------------\n\n";
    }

    // --------------------------------------------------------------------

    protected function getSectionId()
    {
        if ($this->module) {
            return $this->module . '-' . StrFilter::dashes(StrFilter::spaceClearAll(StrFilter::titleCase($this->sectionTitle)));
        }
        else {
            return StrFilter::dashes($this->sectionTitle);
        }
    }

    // --------------------------------------------------------------------

    protected function getSubSectionId()
    {
        return StrFilter::dashes(StrFilter::spaceClearAll(StrFilter::titleCase($this->subsectionTitle)));
    }

    // --------------------------------------------------------------------

    protected function generateController()
    {
        $file               = $this->getControllerPath() . 'Ctrl_' . $this->controllerName . '.php';
        $this->modes[$file] = 'wb';
        $this->files[$file] = '<?php';
        $ctlPreffix         = '';
        if (!trueEmpty($this->module)) {
            $ctlPreffix = 'controllers/';
        }
        $this->files[$file] .= "
// Fichero generado automáticamente

include_once('{$ctlPreffix}Abs_AppController.php');

class Ctrl_{$this->controllerName} extends Abs_AppController
{
    public function _main()
    {
        \$this->_view->addSection('body', 'zaf/' . \zfx\Config::get('admTheme') . '/crud-bootstrap-search', array(
            '_title'      => 'Buscar y filtrar',
            '_controller' => '{$this->controllerName}Crud',
            '_autoFocus'  => true
        ));
        \$this->_view->addSection('body', 'zaf/' . \zfx\Config::get('admTheme') . '/crud-bootstrap-list', array(
            '_title'      => '{$this->crudTitle}',
            '_controller' => '{$this->controllerName}Crud'
        ));
        \$this->_view->show();
    }
";
        $this->files[$file] .= $this->getSep();

        // Los permisos
        $this->files[$file] .= "
    protected function _checkPermission()
    {
        \$res = parent::_checkPermission();
        if (!\$res) {
            return false;
        }
";
        if ($this->permission != '') {
            $this->files[$file] .= "        return (\$this->_getUser()->hasPermission('{$this->permission}'));";
        }
        $this->files[$file] .= "
    }
";

        // Sección y subsección
        $sectionId          = $this->getSectionId();
        $subSectionId       = $this->getSubSectionId();
        $this->files[$file] .= $this->getSep();
        $this->files[$file] .= "
    public function _getCurrentSection()
    {
        return '{$sectionId}';
    }
    ";
        $this->files[$file] .= $this->getSep();
        if (!trueEmpty($this->subsectionTitle)) {
            $this->files[$file] .= "
    public function _getCurrentSubSection()
    {
        return '{$subSectionId}';
    }        
            ";
        }

        // Final de la clase
        $this->files[$file] .= $this->getSep();
        $this->files[$file] .= "}\n";
    }

    // --------------------------------------------------------------------

    protected function generateSQL()
    {
        if (trueEmpty($this->sqlDDLFile)) {
            $this->sqlDDLFile               = $this->getModelPath() . 'DDL-' . $this->table . '.sql';
            $this->modes[$this->sqlDDLFile] = 'wb';
        }
        else {
            $this->sqlDDLFile               = $this->getModelPath() . $this->sqlDDLFile;
            $this->modes[$this->sqlDDLFile] = 'ab';
        }

        // Cabecera
        $this->files[$this->sqlDDLFile] = "-- Definicion automatica\n\n";
        $this->files[$this->sqlDDLFile] .= "CREATE TABLE {$this->table} (\n";

        // Para cada campo, su definición
        $t = count($this->fields);
        $i = 1;
        foreach ($this->fields as $field) {
            $this->files[$this->sqlDDLFile] .= "    " . $field->getColSQL($this->table);
            if ($i < $t) {
                $this->files[$this->sqlDDLFile] .= ',';
            }
            $i++;
            $this->files[$this->sqlDDLFile] .= "\n";
        }
        // Final
        $this->files[$this->sqlDDLFile] .= ");\n\n";
        // Ahora los índices
        foreach ($this->fields as $field) {
            if ($field->index) {
                $this->files[$this->sqlDDLFile] .= $field->getIndexSQL($this->table);
                $this->files[$this->sqlDDLFile] .= "\n";
            }
        }
        $this->files[$this->sqlDDLFile] .= "\n\n";
    }

    // --------------------------------------------------------------------

    protected function generateMenu()
    {
        if (trueEmpty($this->phpMenuFile)) {
            $this->phpMenuFile               = $this->getModelPath() . 'Menu-' . $this->controllerName . '.php';
            $this->modes[$this->phpMenuFile] = 'wb';
        }
        else {
            $this->phpMenuFile               = $this->getModelPath() . $this->phpMenuFile;
            $this->modes[$this->phpMenuFile] = 'ab';
        }

        $this->files[$this->phpMenuFile] = "";

        // Preparo los datos
        if (trueEmpty($this->subsectionTitle)) {
            $section = $this->getSectionId();
            $title   = $this->sectionTitle;
        }
        else {
            $section = $this->getSubSectionId();
            $title   = $this->subsectionTitle;
        }
        if (!trueEmpty($this->icon)) {
            $iconTag = "<i class=\"fas fa-{$this->icon}\"></i>";
        }
        else {
            $iconTag = '';
        }
        $controller = StrFilter::dashes($this->controllerName);

        $this->files[$this->phpMenuFile] .= "
'{$section}' => [
    'es' => ['{$title}', '{$iconTag}'],
    'controller' => '{$controller}',
    ";
        if (!trueEmpty($this->permission)) {
            $this->files[$this->phpMenuFile] .= "'perm' => '{$this->permission}',
    ";
        }
        if (!trueEmpty($this->module)) {
            $this->files[$this->phpMenuFile] .= "'module' => '{$this->module}',
    ";
        }
        $this->files[$this->phpMenuFile] .= "],\n";

    }

    // --------------------------------------------------------------------

    protected function generateCrudController()
    {
        $file               = $this->getControllerPath() . 'Ctrl_' . $this->controllerName . 'Crud.php';
        $this->modes[$file] = 'wb';
        $this->files[$file] = '<?php';
        $ctlPreffix         = '';
        if (!trueEmpty($this->module)) {
            $ctlPreffix = 'controllers/';
        }
        $this->files[$file] .= "
// Fichero generado automáticamente

include_once('{$ctlPreffix}Abs_AppCrudController.php');

class Ctrl_{$this->controllerName}Crud extends Abs_AppCrudController
{    
";

        // ------------ PARTE La función initData()
        // Inicio de la función
        $this->files[$file] .= "
    protected function initData()
    {
        \$this->auto('{$this->table}');\n\n";

        // Relaciones, imágenes y cosas así
        foreach ($this->fields as $field) {
            // Las relaciones sencillas (sin Pickers)
            if ($field->forceRelName || (!trueEmpty($field->fkTable) && $field->fkPicker == FALSE && !$field->notRelName)) {
                $this->files[$file] .= str_repeat(" ", 8) . $field->getRelName($this->table) . "\n";
            }
            // Imágenes
            elseif ($field->isImage) {
                $this->files[$file] .= str_repeat(" ", 8) . "\$this->tableView->toImageField('{$field->id}', \$this->dbFileUrl);\n";
                $this->files[$file] .= str_repeat(" ", 8) . "\$this->tableView->getFieldView('{$field->id}')->setKeepOriginalName(true);\n";
            }
            // Ficheros
            elseif ($field->isFile) {
                $this->files[$file] .= str_repeat(" ", 8) . "\$this->tableView->toFileField('{$field->id}', \$this->dbFileUrl);\n";
                $this->files[$file] .= str_repeat(" ", 8) . "\$this->tableView->getFieldView('{$field->id}')->setKeepOriginalName(true);\n";
            }
            // Stringmap
            elseif (!trueEmpty($field->stringMapModel)) {
                $this->files[$file] .= str_repeat(" ", 8) . "\$this->tableView->stringMapFromArray('{$field->id}', {$field->stringMapModel}::{$field->stringMapFunction}());\n";
                // Si hemos especificado un array, crearemos ese modelo y lo serviremos.
                if ($field->stringMap) {
                    $this->addStringMapFile($field->stringMap, $field->stringMapModel, $field->stringMapFunction);
                }
            }
        }
        $this->files[$file] .= "\n";

        // Las etiquetas
        $this->files[$file] .= str_repeat(" ", 8) . "\$this->tableView->setLabels([\n";
        foreach ($this->fields as $field) {
            $this->files[$file] .= str_repeat(" ", 12) . "'{$field->id}' => '{$field->label}',\n";
        }
        $this->files[$file] .= str_repeat(" ", 8) . "]);\n\n";


        // Establecemos el orden de los campos
        $this->files[$file] .= str_repeat(" ", 8) . "\$this->tableView->setFieldOrder([\n";
        foreach ($this->fields as $field) {
            $this->files[$file] .= str_repeat(" ", 12) . "'{$field->id}',\n";
        }
        $this->files[$file] .= str_repeat(" ", 8) . "]);\n\n";


        // Veamos si hay grupos
        if ($this->groups) {
            $this->files[$file] .= str_repeat(" ", 8) . "\$this->tableView->setGroups([\n";
            foreach ($this->groups as $idGroup => $groupData) {
                // Si existen vale y si no pues nada
                $label              = (string)\zfx\a($groupData, 'label');
                $cols               = (int)\zfx\a($groupData, 'cols');
                $expand             = ((bool)\zfx\a($groupData, 'expand') ? 'true' : 'false');
                $this->files[$file] .= str_repeat(" ", 12) . "new \zfx\TableViewGroup([\n";
                foreach ($this->fields as $field) {
                    if ($field->group == $idGroup) {
                        $this->files[$file] .= str_repeat(" ", 16) . "'{$field->id}',\n";
                    }
                }
                $this->files[$file] .= str_repeat(" ", 12) . "], {$expand}, '', '{$label}', {$cols}),\n";
            }
            $this->files[$file] .= str_repeat(" ", 8) . "]);\n";
        }


        // Fin de la función initData()
        $this->files[$file] .= "}\n";

        // ------------ PARTE Los permisos
        $this->files[$file] .= $this->getSep();
        $this->files[$file] .= "
    protected function _checkPermission()
    {
        \$res = parent::_checkPermission();
        if (!\$res) {
            return false;
        }
";
        if ($this->permission != '') {
            $this->files[$file] .= "        return (\$this->_getUser()->hasPermission('{$this->permission}'));";
        }
        $this->files[$file] .= "
    }
";

        // ------------ PARTE Las tablas relacionadas (si hay)
        if ($this->sectionRels) {
            $this->files[$file] .= $this->getSep();
            $this->files[$file] .= "
            
    protected function setupViewForm(\$packedID = '')
    {
        parent::setupViewForm(\$packedID);
        if (\$packedID != '') {
";
            foreach ($this->sectionRels as $rel) {
                if (!is_null($rel['section'])) {
                    $sectionPiece = ", '{$rel['section']}'";
                }
                else {
                    $sectionPiece = '';
                }
                $this->files[$file] .= "\$this->addFrmSectionRel(\$packedID, '{$rel['relName']}', '{$rel['controller']}', '{$rel['title']}' $sectionPiece);\n";
            }
            $this->files[$file] .= "
        }
    }
";
        }


        // ------------ PARTE Final de la clase
        $this->files[$file] .= $this->getSep();
        $this->files[$file] .= "}\n";

    } // fin de generateCrudController()

    // --------------------------------------------------------------------

    protected function addStringMapFile(array $data, $class, $function)
    {
        $file               = $this->getModelPath() . $class . '.php';
        $this->modes[$file] = 'wb';
        $this->files[$file] = '<?php';
        $this->files[$file] .= "
// Fichero generado automáticamente

class {$class}
{\n\n";

        $this->files[$file] .= str_repeat(" ", 4) . "public static function {$function}()\n";
        $this->files[$file] .= str_repeat(" ", 4) . "{\n";
        $this->files[$file] .= str_repeat(" ", 8) . "return " . var_export($data, TRUE) . ";\n";
        $this->files[$file] .= str_repeat(" ", 4) . "}\n";
        $this->files[$file] .= "}\n";

    }

    // --------------------------------------------------------------------


}
