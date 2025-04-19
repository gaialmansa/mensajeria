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

class Context
{

    protected $type;
    protected $label;
    protected $value;
    protected $data;
    protected $removeFormat;
    protected $urlFormat;
    protected $menu;
    protected $view;
    protected $datas;
    protected $cssClass;
    protected $id;


    // --------------------------------------------------------------------

    /*
     * Static functions for session storing
     */

    // --------------------------------------------------------------------


    public static function getAll()
    {
        @session_start();
        return aa($_SESSION, '_adm-context');
    }

    // --------------------------------------------------------------------

    public static function add(Context $c)
    {
        @session_start();
        $_SESSION['_adm-context'][$c->getType()] = $c;
    }

    // --------------------------------------------------------------------

    public static function del($c)
    {
        @session_start();
        if (is_a($c, 'Context')) {
            unset($_SESSION['_adm-context'][$c->getType()]);
        }
        else {
            unset($_SESSION['_adm-context'][(string)$c]);
        }
    }

    // --------------------------------------------------------------------

    /**
     * @param $c Type of Context to be retrieved from session
     * @return Context
     */
    public static function get($c)
    {
        @session_start();
        return aa($_SESSION, '_adm-context', (string)$c);
    }

    // --------------------------------------------------------------------

    /* METHODS */

    // --------------------------------------------------------------------


    public function __construct(
        $type,
        $label,
        $value,
        $data,
        $removeFormat = '',
        $urlFormat = '',
        array $menu = NULL,
        $cssClass = '',
        $id = '',
        array $datas = NULL
    )
    {
        $this->type      = (string)$type;
        $this->label     = (string)$label;
        $this->value     = (string)$value;
        $this->data      = $data;
        $this->urlFormat = (string)$urlFormat;
        $this->menu      = $menu;
        $this->view      = 'zaf/' . Config::get('admTheme') . '/context';
        $this->cssClass  = $cssClass;
        $this->id        = $id;
        $this->datas     = $datas;

        if ($removeFormat == '') {
            $this->removeFormat = Config::get('appRemoveContextUrl');
        }
        else {
            $this->removeFormat = (string)$removeFormat;

        }


    }

    // --------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = (string)$type;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = (string)$label;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    // --------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getUrl()
    {
        return sprintf($this->urlFormat, $this->type, $this->data);
    }

    // --------------------------------------------------------------------

    /**
     * @param string $urlFormat
     */
    public function setUrlFormat($urlFormat)
    {
        $this->urlFormat = $urlFormat;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getRemove()
    {
        return sprintf($this->removeFormat, $this->type, $this->data);
    }

    // --------------------------------------------------------------------

    /**
     * @param string $removeFormat
     */
    public function setRemoveFormat($removeFormat)
    {
        $this->urlFormat = $removeFormat;
    }

    // --------------------------------------------------------------------

    /**
     * @return array
     */
    public function getMenu()
    {
        $fmenu = array();
        if ($this->menu) {
            foreach ($this->menu as $options) {
                $options['url'] = sprintf(a($options, 'url'), $this->type, $this->data);
                $fmenu[]        = $options;
            }
        }
        return $fmenu;
    }

    // --------------------------------------------------------------------

    /**
     * @param array $menu
     */
    public function setMenu(array $menu = NULL)
    {
        $this->menu = $menu;
    }
    // --------------------------------------------------------------------

    /**
     * @return bool
     */
    public function hasMenu()
    {
        return (bool)$this->menu;
    }

    // --------------------------------------------------------------------

    /**
     * @return bool
     */
    public function hasUrl()
    {
        return !trueEmpty($this->urlFormat);
    }

    // --------------------------------------------------------------------

    /**
     * @return bool
     */
    public function hasRemove()
    {
        return !trueEmpty($this->removeFormat);
    }

    // --------------------------------------------------------------------

    public function render()
    {
        View::direct($this->view, array('context' => $this));
    }

    // --------------------------------------------------------------------


    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    // --------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = (string)$id;
    }

    // --------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed $cssClass
     */
    public function setCssClass($cssClass)
    {
        $this->cssClass = (string)$cssClass;
    }

    // --------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getDatas()
    {
        return $this->datas;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed $datas
     */
    public function setDatas(array $datas = NULL)
    {
        $this->datas = $datas;
    }

    // --------------------------------------------------------------------


}
