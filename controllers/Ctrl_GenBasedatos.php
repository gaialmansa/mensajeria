<?php

use zfx\Controller;


class Ctrl_GenBasedatos extends Controller
{
    public $menu;
    public $sql;

    public function _main()
    {
        \zfx\Debug::show("Generando CRUDs de Base de datos para mecha...");
        $this->menu = '_menu-basedatos.php';
        $this->sql  = '_bd-basedatos.sql';
        unlink('/srv/vhosts/mecha.onlineinfosys.com/modules/mecha/models/_menu-basedatos.php');
        unlink('/srv/vhosts/mecha.onlineinfosys.com/modules/mecha/models/_bd-basedatos.sql');

        $this->puestos();
        $this->personal();
        $this->plantas();
        $this->bases();
        $this->obras();
        $this->fases();
        $this->aridos();
        $this->betunes();
        $this->mezclas();
        $this->eqt();
        $this->eqtpersonal();
        $this->canteras();
        $this->almacenes();
        $this->camiones();
        $this->transportistas();
        $this->plantillas();
    }


    // --------------------------------------------------------------------

    public function puestos()
    {
        // PUESTOS
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_puesto';
        $table->controllerName  = 'Puestos';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Puestos';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'hard-hat';

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'grupo';
        $f->dataType           = 'int';
        $f->label              = 'Grupo';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->stringMap          = [1 => 'Directo', 2 => 'Indirecto'];
        $f->stringMapFunction  = 'obtGrupos';
        $f->stringMapModel     = 'Puestos';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function personal()
    {
        // PERSONAL
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_personal';
        $table->controllerName  = 'Personal';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Personal';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'user-friends';
        $table->groups          = [
            'princ' => [],
            'prl'   => [],
            'prl2'  => ['expand' => TRUE],
            'prl3'  => ['expand' => TRUE],
            'prl4'  => ['expand' => TRUE],
            'prl5'  => ['expand' => TRUE],
        ];

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'nombre';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Nombre';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'recurso';
        $f->dataType           = 'varchar(16)';
        $f->label              = 'Recurso';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'codpersonal';
        $f->dataType           = 'varchar(160)';
        $f->label              = 'Cód. personal';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_mec_puesto';
        $f->dataType           = 'int';
        $f->label              = 'Puesto';
        $f->index              = TRUE;
        $f->fkTable            = 'mec_puesto';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'denominacion';
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_zfx_user';
        $f->dataType           = 'int';
        $f->label              = 'Cuenta usuario';
        $f->index              = TRUE;
        $f->fkTable            = 'zfx_user';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'login';
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'estado';
        $f->dataType           = 'int';
        $f->label              = 'Estado';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->stringMap          = [1 => 'Alta', 2 => 'Baja'];
        $f->stringMapFunction  = 'obtEstados';
        $f->stringMapModel     = 'Personal';
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'telefono';
        $f->dataType           = 'varchar(16)';
        $f->label              = 'Teléfono';
        $f->index              = TRUE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'email';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Email';
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'dni';
        $f->dataType           = 'varchar(16)';
        $f->label              = 'DNI/NIF/NIE';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'punto';
        $f->dataType           = 'boolean';
        $f->label              = 'Punto';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'foto';
        $f->dataType           = 'varchar(512)';
        $f->label              = 'Fotografía';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $f->group              = 'princ';
        $f->isImage            = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'centrotrabajo';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Centro de Trabajo';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'vtocontrato';
        $f->dataType           = 'date';
        $f->label              = 'Vencimiento contrato';
        $f->index              = TRUE;
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'reco';
        $f->dataType           = 'date';
        $f->label              = 'Rec. médico';
        $f->index              = TRUE;
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'epis';
        $f->dataType           = 'date';
        $f->label              = 'EPIs';
        $f->index              = TRUE;
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'contrato';
        $f->dataType           = 'boolean';
        $f->label              = '¿Contrato?';
        $f->index              = TRUE;
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'autmaq';
        $f->dataType           = 'boolean';
        $f->label              = 'Aut. uso maquinaria';
        $f->index              = TRUE;
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'prlquiron';
        $f->dataType           = 'boolean';
        $f->label              = 'PRL Quirón';
        $f->index              = TRUE;
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'idc';
        $f->dataType           = 'boolean';
        $f->label              = 'IDC';
        $f->index              = TRUE;
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'entregainfo';
        $f->dataType           = 'boolean';
        $f->label              = 'Entrega info';
        $f->index              = TRUE;
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'oficio';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Oficio';
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'rp';
        $f->dataType           = 'integer';
        $f->label              = 'RP';
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'recmtodia';
        $f->dataType           = 'boolean';
        $f->label              = 'Rec. mto. diario';
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'recmmedioamb';
        $f->dataType           = 'boolean';
        $f->label              = 'Rec. medio ambiente';
        $f->group              = 'prl';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'aut';
        $f->dataType           = 'text';
        $f->label              = 'Equipos autorizados';
        $f->group              = 'prl2';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'form';
        $f->dataType           = 'text';
        $f->label              = 'Formación según convenio';
        $f->group              = 'prl3';
        $table->fields[$f->id] = $f;


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'formotra';
        $f->dataType           = 'text';
        $f->label              = 'Otra formación';
        $f->group              = 'prl4';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $f->group              = 'prl5';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function plantas()
    {
        // PLANTAS
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_planta';
        $table->controllerName  = 'Plantas';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Plantas';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'cogs';

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'codigoplanta';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Código planta';
        $f->index              = TRUE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'codigoalmacen';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Código almacen';
        $f->index              = TRUE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function bases()
    {
        // BASES
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_base';
        $table->controllerName  = 'Bases';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Bases';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'house-user';

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------


    public function fases()
    {
        // FASES
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_fase';
        $table->controllerName  = 'Fases';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Fases';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'table';

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_mec_obra';
        $f->dataType           = 'int';
        $f->label              = 'Obra';
        $f->index              = TRUE;
        $f->fkTable            = 'mec_obra';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'denominacion';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'codigo';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Código fase';
        $f->index              = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'cliente';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Cliente';
        $f->index              = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_mec_base';
        $f->dataType           = 'int';
        $f->label              = 'Base';
        $f->index              = TRUE;
        $f->fkTable            = 'mec_base';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'denominacion';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'estado';
        $f->dataType           = 'int';
        $f->label              = 'Estado';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->stringMap          = [1 => 'Activa', 2 => 'Terminada'];
        $f->stringMapFunction  = 'obtEstados';
        $f->stringMapModel     = 'Fases';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }


    // --------------------------------------------------------------------


    public function obras()
    {
        // OBRAS
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_obra';
        $table->controllerName  = 'Obras';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Obras';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'cogs';
        $table->groups          = [
            'princ' => [],
            'est'   => ['label' => 'Estudio'],
            'obs'   => ['expand' => TRUE],
            'tot'   => ['label' => 'Total'],
            'prev'  => ['label' => 'Previsión']
        ];


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'codigo';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Código obra';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_mec_fase';
        $f->dataType           = 'integer';
        $f->label              = 'Fase';
        $f->index              = TRUE;
        $f->group              = 'princ';
        $f->forceRelName       = TRUE;
        $f->fkTable            = 'mec_fase';
        $f->fkDesc             = 'denominacion';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'cliente';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Cliente';
        $f->index              = TRUE;
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_mec_base';
        $f->dataType           = 'int';
        $f->label              = 'Base';
        $f->index              = TRUE;
        $f->fkTable            = 'mec_base';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'denominacion';
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'estado';
        $f->dataType           = 'int';
        $f->label              = 'Estado';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->stringMap          = [1 => 'Activa', 2 => 'Terminada'];
        $f->stringMapFunction  = 'obtEstados';
        $f->stringMapModel     = 'Obras';
        $f->group              = 'princ';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_retd';
        $f->dataType           = 'int';
        $f->label              = 'Rto. Est. Tn/día';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_ceft';
        $f->dataType           = 'numeric(18,6)';
        $f->label              = 'Coste Est. Fab./Tn';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_ceet';
        $f->dataType           = 'numeric(18,6)';
        $f->label              = 'Coste Est. Ext./Tn';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_cegt';
        $f->dataType           = 'numeric(18,6)';
        $f->label              = 'Coste Est. Gon./Tn';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_cett';
        $f->dataType           = 'numeric(18,6)';
        $f->label              = 'Coste Est. Tte./Tn';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_camiones';
        $f->dataType           = 'int';
        $f->label              = 'Camiones';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_km';
        $f->dataType           = 'numeric(18,6)';
        $f->label              = 'Km';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_dias';
        $f->dataType           = 'int';
        $f->label              = 'Días';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_peht';
        $f->dataType           = 'int';
        $f->label              = 'PEHT €/h';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_pe';
        $f->dataType           = 'int';
        $f->label              = '%D Est';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'est_pep';
        $f->dataType           = 'int';
        $f->label              = 'PEP €/h';
        $f->group              = 'est';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $f->group              = 'obs';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'tot_importe';
        $f->dataType           = 'numeric(18,6)';
        $f->label              = 'Importe';
        $f->group              = 'tot';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'tot_toneladas';
        $f->dataType           = 'int';
        $f->label              = 'Toneladas';
        $f->group              = 'tot';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'tot_anno';
        $f->dataType           = 'int';
        $f->label              = 'Año';
        $f->group              = 'tot';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_prod';
        $f->dataType           = 'numeric(18,6)';
        $f->label              = 'Prev. producción';
        $f->group              = 'prev';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_cert';
        $f->dataType           = 'numeric(18,6)';
        $f->label              = 'Prev. certificación';
        $f->group              = 'prev';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_pend';
        $f->dataType           = 'numeric(18,6)';
        $f->label              = 'Pendiente facturar';
        $f->group              = 'prev';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function aridos()
    {
        // ÁRIDOS
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_arido';
        $table->controllerName  = 'Aridos';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Áridos';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'truck-loading';

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'codproducto';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Cód. producto';
        $f->index              = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'naturaleza';
        $f->dataType           = 'int';
        $f->label              = 'Naturaleza';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->stringMap          = [1 => 'Calizo', 2 => 'Pórfido'];
        $f->stringMapFunction  = 'obtNaturalezas';
        $f->stringMapModel     = 'Aridos';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'fase';
        $f->dataType           = 'int';
        $f->label              = 'Fase';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;


        $table->generate();
    }

    // --------------------------------------------------------------------

    public function betunes()
    {
        // BETUNES
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_betun';
        $table->controllerName  = 'Betunes';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Betunes';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'tint';

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function mezclas()
    {
        // MEZCLAS
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_mezcla';
        $table->controllerName  = 'Mezclas';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Mezclas';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'fill';

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_mec_arido';
        $f->dataType           = 'integer';
        $f->label              = 'Árido';
        $f->fkTable            = 'mec_arido';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'denominacion';
        $f->index              = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_mec_betun';
        $f->dataType           = 'integer';
        $f->label              = 'Betún';
        $f->fkTable            = 'mec_betun';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'denominacion';
        $f->index              = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'codigo';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Código';
        $f->index              = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'fase';
        $f->dataType           = 'int';
        $f->label              = 'Fase';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function eqt()
    {
        // Equipos de trabajo
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_eqt';
        $table->controllerName  = 'Equipost';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Equipos trabajo';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'user-friends';
        $table->sectionRels     = [
            [
                'relName'    => 'mec_eqt_personal_rel_mec_eqt',
                'controller' => 'EquipostPersonalCrud',
                'title'      => 'Miembros',
                'section'    => NULL
            ]
        ];


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'imagen';
        $f->dataType           = 'varchar(512)';
        $f->label              = 'Imagen';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $f->isImage            = TRUE;
        $table->fields[$f->id] = $f;


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function eqtpersonal()
    {
        // Equipos de trabajo <=> personal

        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table          = 'mec_eqt_personal';
        $table->controllerName = 'EquipostPersonal';
        $table->module         = 'mecha';
        $table->permission     = 'base-datos-basica';


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_mec_eqt';
        $f->dataType           = 'integer';
        $f->label              = 'Equipo de trabajo';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->notRelName         = TRUE;
        $f->fkTable            = 'mec_eqt';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'denominacion';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_mec_personal';
        $f->dataType           = 'integer';
        $f->label              = 'Miembro';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->fkTable            = 'mec_personal';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'nombre';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'fecha';
        $f->dataType           = 'date';
        $f->label              = 'Fecha incorporación';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }


    // --------------------------------------------------------------------


    public function canteras()
    {
        // Canteras
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_cantera';
        $table->controllerName  = 'Canteras';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Canteras';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'eject';


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'nombre';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Nombre';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'poblacion';
        $f->dataType           = 'varchar(256)';
        $f->label              = 'Población';
        $f->index              = TRUE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'cliente';
        $f->dataType           = 'varchar(256)';
        $f->label              = 'Cliente';
        $f->index              = TRUE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'contrato';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Contrato';
        $f->index              = TRUE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'contrato';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Contrato';
        $f->index              = TRUE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'contratogen';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Contrato genérico';
        $f->index              = TRUE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'codprov';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Código proveedor';
        $f->index              = TRUE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------


    public function almacenes()
    {
        // Almacenes
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_almacen';
        $table->controllerName  = 'Almacenes';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Almacenes';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'store';


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'denominacion';
        $f->dataType           = 'varchar(128)';
        $f->label              = 'Denominación';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function camiones()
    {
        // Camiones
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_camion';
        $table->controllerName  = 'Camiones';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Camiones';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'truck';


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'matricula';
        $f->dataType           = 'varchar(32)';
        $f->label              = 'Matrícula';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->unique             = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'empresa';
        $f->dataType           = 'varchar(256)';
        $f->label              = 'Empresa';
        $f->index              = TRUE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'tipo';
        $f->dataType           = 'int';
        $f->label              = 'Tipo';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->stringMap          = [1 => 'CHM', 2 => 'Subcontratistas', 3 => 'Externo de cantera'];
        $f->stringMapFunction  = 'obtTipos';
        $f->stringMapModel     = 'Camiones';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function transportistas()
    {
        // Transportistas
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_transportista';
        $table->controllerName  = 'Transportistas';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Transportistas';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'truck-moving';


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id';
        $f->dataType           = 'serial';
        $f->label              = 'ID';
        $f->primaryKey         = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'empresa';
        $f->dataType           = 'varchar(256)';
        $f->label              = 'Empresa';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_baal';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = 'Precio bañera aluminio';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_baac';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = 'Precio bañera acero';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_cent';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = 'Precio centauro';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'concepto';
        $f->dataType           = 'int';
        $f->label              = 'Concepto';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->stringMap          = [1 => 'Horas', 2 => 'Km'];
        $f->stringMapFunction  = 'obtConceptos';
        $f->stringMapModel     = 'Transportistas';
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_lt15';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = '< 15 Km';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_lt30';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = '< 30 Km';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_lt45';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = '< 45 Km';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_lt60';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = '< 60 Km';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_lt75';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = '< 75 Km';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_lt90';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = '< 90 Km';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'pre_lt100';
        $f->dataType           = 'numeric(18,5)';
        $f->label              = '< 100 Km';
        $f->index              = FALSE;
        $f->canBeNull          = TRUE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'contrato';
        $f->dataType           = 'varchar(64)';
        $f->label              = 'Contrato';
        $f->index              = FALSE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'observaciones';
        $f->dataType           = 'text';
        $f->label              = 'Observaciones';
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

    public function plantillas()
    {
        // Plantillas
        $table              = new \zfx\CrudTableDef();
        $table->phpMenuFile = $this->menu;
        $table->sqlDDLFile  = $this->sql;

        $table->table           = 'mec_plantilla';
        $table->controllerName  = 'Plantillas';
        $table->sectionTitle    = 'Base de datos';
        $table->subsectionTitle = 'Plantillas';
        $table->module          = 'mecha';
        $table->permission      = 'base-datos-basica';
        $table->icon            = 'file';

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'id_srv_servicio';
        $f->dataType           = 'integer';
        $f->label              = 'Grupo Funcional';
        $f->fkTable            = 'srv_servicio';
        $f->fkCol              = 'id';
        $f->fkDesc             = 'denominacion';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $table->fields[$f->id] = $f;

        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'tipo';
        $f->dataType           = 'int';
        $f->label              = 'Tipo';
        $f->index              = TRUE;
        $f->canBeNull          = FALSE;
        $f->stringMap          = [1000 => 'Parte de entrada productos'];
        $f->stringMapFunction  = 'obtTipos';
        $f->stringMapModel     = 'Plantillas';
        $table->fields[$f->id] = $f;


        $f                     = new \zfx\CrudFieldDef();
        $f->id                 = 'fichero';
        $f->dataType           = 'varchar(512)';
        $f->label              = 'Fichero ODT';
        $f->index              = FALSE;
        $f->canBeNull          = FALSE;
        $f->isFile             = TRUE;
        $table->fields[$f->id] = $f;

        $table->generate();
    }

    // --------------------------------------------------------------------

}
