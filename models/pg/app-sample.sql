--   Zerfrex (R) RAD ADM
--   Zerfrex RAD for Administration & Data Management
--
--   Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
--   All rights reserved. Todos los derechos reservados.
--
--   Este software solo se puede usar bajo licencia del autor.
--   El uso de este software no implica ni otorga la adquisición de
--   derechos de explotación ni de propiedad intelectual o industrial.


-- Datos de pruebas para la aplicacion

-- Password: zfxadmin
INSERT INTO zfx_user (login, password_hash, language)
VALUES ('admin', md5('admin95'), 'es');
INSERT INTO zfx_group (name, description)
VALUES ('u-admin', 'Grupo propio del usuario admin');
INSERT INTO zfx_user_group (id_user, id_group)
VALUES (1, 1);
INSERT INTO zfx_permission (code, description)
VALUES ('menu-sis-cuentas-usuarios', 'Menú Sistema/Cuentas/Usuarios');
INSERT INTO zfx_permission (code, description)
VALUES ('menu-sis-cuentas-grupos', 'Menú Sistema/Cuentas/Grupos');
INSERT INTO zfx_permission (code, description)
VALUES ('menu-sis-cuentas-permisos', 'Menú Sistema/Cuentas/Permisos');
INSERT INTO zfx_group_permission (id_group, id_permission)
VALUES (1, 1);
INSERT INTO zfx_group_permission (id_group, id_permission)
VALUES (1, 2);
INSERT INTO zfx_group_permission (id_group, id_permission)
VALUES (1, 3);

