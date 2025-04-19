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

INSERT INTO zfx_user (name, email, password_hash, language, mobile)
VALUES ('admin', 'admin@example.com', '21232f297a57a5a743894a0e4a801fc3', 'en', '123456789');
INSERT INTO zfx_user (name, email, password_hash, language, mobile)
VALUES ('user', 'user@example.com', 'ee11cbb19052e40b07aac0ca060c23ee', 'en', '123456789');
INSERT INTO zfx_group (name)
VALUES ('admins');
INSERT INTO zfx_group (name)
VALUES ('users');
INSERT INTO zfx_user_group (id_user, id_group)
VALUES (1, 1);
INSERT INTO zfx_user_group (id_user, id_group)
VALUES (1, 2);
INSERT INTO zfx_user_group (id_user, id_group)
VALUES (2, 2);
INSERT INTO zfx_permission (code)
VALUES ('admin-zone');
INSERT INTO zfx_permission (code)
VALUES ('user-zone');
INSERT INTO zfx_group_permission (id_group, id_permission)
VALUES (1, 1);
INSERT INTO zfx_group_permission (id_group, id_permission)
VALUES (2, 2);

