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

class RowActions
{

    /**
     *
     * @var array $actions ;
     */
    protected $actions;

    /**
     * Table or view scheme
     * @var Schema $schema
     */
    protected $schema;

    // --------------------------------------------------------------------

    public static function renderTag(array $actions, $value, $cssClass = '', $visibleValue = '')
    {
        $viewData = array(
            'cssClass'     => (string)$cssClass,
            'visibleValue' => (string)$visibleValue,
            'actions'      => $actions,
            'value'        => $value
        );
        View::direct('zaf/' . Config::get('admTheme') . '/rowactions-tag', $viewData);
    }

    // --------------------------------------------------------------------

    public function getActions()
    {
        return $this->actions;
    }

    // --------------------------------------------------------------------

    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }
    // --------------------------------------------------------------------

    /**
     * Get reference of the Schema used
     *
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    // --------------------------------------------------------------------

    /**
     * Set Schema
     *
     * @param Schema $value
     */
    public function setSchema(Schema $value)
    {
        $this->schema = $value;
    }

    // --------------------------------------------------------------------

    public function render(array $row, $cssClass = '', $visibleValue = '')
    {


        $actions = array();

        if (va($this->actions)) {
            foreach ($this->actions as $action) {
                $rendering = FALSE;
                if (av($action, 'renderer')) {
                    $func      = $action['renderer'];
                    $rendering = $func($row, $action);
                }
                if ($rendering === FALSE) {
                    $class = a($action, 'class');
                    $style = a($action, 'style');
                    $attr  = a($action, 'attr');
                    $text  = a($action, 'text');

                    if (is_callable(a($action, 'var-class'))) {
                        $class = call_user_func(a($action, 'var-class'), $class, $row);
                    }
                    if (is_callable(a($action, 'var-style'))) {
                        $style = call_user_func(a($action, 'var-style'), $style, $row);
                    }
                    if (is_callable(a($action, 'var-attr'))) {
                        $attr = call_user_func(a($action, 'var-attr'), $attr, $row);
                    }
                    if (is_callable(a($action, 'var-text'))) {
                        $text = call_user_func(a($action, 'var-text'), $text, $row);
                    }

                    $disableAction = FALSE;
                    if (a($action, 'disabled')) {
                        $disableAction = TRUE;
                    }
                    else {
                        $dc = (string)a($action, 'disablerColumn');
                        $dv = a($action, 'disablerValue');
                        if ($dc != '' && array_key_exists($dc, $row)) {
                            if (is_array($dv)) {
                                if (in_array($row[$dc], $dv)) {
                                    $disableAction = TRUE;
                                }
                            }
                            elseif (trueEmpty($dv)) {
                                if ((bool)$row[$dc]) {
                                    $disableAction = TRUE;
                                }
                            }
                            elseif ($row[$dc] == $dv) {
                                $disableAction = TRUE;
                            }
                        }
                    }

                    if (!$disableAction) {
                        if (!av($action, 'fields')) {
                            $values    = array(PKView::pack($this->schema->extractPk($row)));
                            $rawValues = $this->schema->extractPk($row);
                        }
                        else {
                            $values    = array();
                            $rawValues = array();
                            foreach ($action['fields'] as $f) {
                                $values[]    = StrFilter::safeEncode(a($row, $f));
                                $rawValues[] = a($row, $f);
                            }
                        }
                        $href       = vsprintf($action['href'], $values);
                        $attrTarget = '';
                        if (!trueEmpty(a($action, 'target'))) {
                            $attrTarget = 'target="' . a($action, 'target') . '"';
                        }
                        $newData = array();
                        if (a($action, 'data')) {
                            foreach (a($action, 'data') as $k => $v) {
                                if (preg_match('%\[\[.*?\]\]%su', $v)) {
                                    $newData[$k] = StrFilter::safeEncode(preg_replace_callback('%\[\[(.*?)\]\]%su',
                                        function ($block) use ($rawValues) {
                                            return vsprintf($block[1], $rawValues);
                                        }, $v));
                                }
                                else {
                                    $newData[$k] = vsprintf($v, $values);
                                }
                            }
                        }
                        $attrData = HtmlTools::dataAttr($newData);

                        $actions[] = array(
                            'type'   => 'enabled',
                            'params' => array(
                                'href'       => $href,
                                'attrData'   => $attrData,
                                'attrTarget' => $attrTarget,
                                'class'      => $class,
                                'style'      => $style,
                                'attr'       => $attr,
                                'text'       => $text,
                            )
                        );
                    }
                    else {
                        $actions[] = array(
                            'type'   => 'disabled',
                            'params' => array(
                                'class' => $class,
                                'style' => $style,
                                'attr'  => $attr,
                                'text'  => $text
                            )
                        );
                    }
                }
                else {
                    $actions[] = array(
                        'type' => 'custom',
                        'html' => $rendering
                    );
                }
            }
        }

        $viewData = array(
            'cssClass'     => (string)$cssClass,
            'visibleValue' => (string)$visibleValue,
            'actions'      => $actions
        );
        View::direct('zaf/' . Config::get('admTheme') . '/rowactions', $viewData);
    }
    // --------------------------------------------------------------------
}
