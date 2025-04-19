--   Zerfrex (R) RAD ADM
--   Zerfrex RAD for Administration & Data Management
--
--   Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
--   All rights reserved. Todos los derechos reservados.
--
--   Este software solo se puede usar bajo licencia del autor.
--   El uso de este software no implica ni otorga la adquisición de
--   derechos de explotación ni de propiedad intelectual o industrial.


# Drops

DROP TABLE IF EXISTS zfx_userattribute CASCADE;
DROP TABLE IF EXISTS zfx_user_group CASCADE;
DROP TABLE IF EXISTS zfx_group_permission CASCADE;
DROP TABLE IF EXISTS zfx_user CASCADE;
DROP TABLE IF EXISTS zfx_group CASCADE;
DROP TABLE IF EXISTS zfx_permission CASCADE;


#
Table and
sequence definitions
CREATE TABLE IF NOT EXISTS zfx_group
(
    id bigint(20) unsigned NOT NULL, `
    name
    `
    varchar
(
    64
) NOT NULL,
    description varchar(1024),
    ref1 integer,
    ref2 integer
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS zfx_group_permission
(
    id_group      bigint(20) unsigned NOT NULL,
    id_permission bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS zfx_permission
(
    id bigint(20) unsigned NOT NULL, `
    code
    `
    varchar
(
    64
) NOT NULL,
    description varchar(1024)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS zfx_user
(
    id            bigint(20) unsigned NOT NULL,
    login         varchar(64) NOT NULL,
    password_hash char(32)    NOT NULL, `
    language
    `
    char
(
    2
) NOT NULL,
    ref1 integer,
    ref2 integer
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS zfx_userattribute
(
    id_user bigint(20) unsigned NOT NULL, `
    code
    `
    varchar
(
    64
) NOT NULL, `value` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS zfx_user_group
(
    id_user  bigint(20) unsigned NOT NULL,
    id_group bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Constraints

ALTER TABLE zfx_group_permission
    ADD PRIMARY KEY (id_group, id_permission),
    ADD KEY permission(id_permission);
ALTER TABLE zfx_group
    ADD PRIMARY KEY (id);
ALTER TABLE zfx_permission
    ADD PRIMARY KEY (id),
    ADD UNIQUE KEY ` code ` (` code `);
ALTER TABLE zfx_user
    ADD PRIMARY KEY (id),
    ADD UNIQUE KEY ` login ` (` login `);
ALTER TABLE zfx_userattribute
    ADD PRIMARY KEY (id_user, ` code `);
ALTER TABLE zfx_user_group
    ADD PRIMARY KEY (id_user, id_group),
    ADD KEY ` group ` (id_group);

ALTER TABLE zfx_group MODIFY id bigint (20) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE zfx_permission MODIFY id bigint (20) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE zfx_user MODIFY id bigint (20) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE zfx_group_permission
    ADD CONSTRAINT zfx_group_permission_relPermission FOREIGN KEY (id_permission) REFERENCES zfx_permission (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT zfx_group_permission_relGroup FOREIGN KEY (id_group) REFERENCES zfx_group (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE zfx_userattribute
    ADD CONSTRAINT zfx_userattribute_relUser FOREIGN KEY (id_user) REFERENCES zfx_user (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE zfx_user_group
    ADD CONSTRAINT zfx_user_group_relUser FOREIGN KEY (id_user) REFERENCES zfx_user (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT zfx_user_group_relGroup FOREIGN KEY (id_group) REFERENCES zfx_group (id) ON DELETE CASCADE ON UPDATE CASCADE;

