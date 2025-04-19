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

use zfx\DB;

/**
 * The Group Class.
 *
 * Represents an group of users
 */
class Group
{

    /**
     * @var integer Group numeric ID
     */
    protected $id;

    /**
     * @var string $name Group name
     */
    protected $name;

    /**
     * @var integer $ref1 Numeric Reference #1
     */
    protected $ref1;

    /**
     * @var integer $ref2 Numeric Reference #2
     */
    protected $ref2;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param integer $id Numeric ID of the group
     * @param string $name
     */
    protected function __construct($id, $name, $ref1 = 0, $ref2 = 0)
    {
        $this->id   = (int)$id;
        $this->name = (string)$name;
        $this->ref1 = (int)$ref1;
        $this->ref2 = (int)$ref2;

    }
    // --------------------------------------------------------------------

    /**
     * Gets an existing group using ID
     *
     * @param integer $id
     * @return \zfx\Group|null Group object reference or null if wrong ID
     */
    public static function get($id)
    {
        $db = new DB();
        $id = (int)$id;

        $sql  = "
            SELECT          *
            FROM            zfx_group
            WHERE           id = $id;
        ";
        $data = $db->qr($sql);
        if ($data) {
            return new Group($data['id'], $data['name'], $data['ref1'], $data['ref2']);
        }
        else {
            return NULL;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Get group numeric database ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    // --------------------------------------------------------------------

    /**
     * Get group nickname
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    // --------------------------------------------------------------------

    public function retRef1()
    {
        return $this->ref1;
    }

    // --------------------------------------------------------------------

    public function retRef2()
    {
        return $this->ref2;
    }

    // --------------------------------------------------------------------

    /**
     * Clear and write a new set of permissions
     *
     * @param array $permissions
     */
    public function setAllPermissions($permissions = NULL)
    {
        $db  = new DB();
        $sql = "DELETE FROM zfx_group_permission WHERE id_group={$this->id}";
        $db->q($sql);
        $num = nwcount($permissions);
        if ($num > 0) {
            $values = '';
            $i      = 1;
            foreach ($permissions as $p) {
                $values .= '(' . $this->id . ',' . (int)$p . ')';
                $i++;
                if ($i <= $num) {
                    $values .= ',';
                }
            }
            $sql = "
                INSERT INTO         zfx_group_permission
                                    (
                                        id_group,
                                        id_permission
                                    )
                VALUES              $values;
            ";
            $db->setIgnoreErrors(TRUE);
            $db->q($sql);
        }
    }
    // --------------------------------------------------------------------
}
