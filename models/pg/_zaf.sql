    --   Zerfrex (R) RAD ADM
    --   Zerfrex RAD for Administration & Data Management
    --
    --   Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
    --   All rights reserved. Todos los derechos reservados.
    --
    --   Este software solo se puede usar bajo licencia del autor.
    --   El uso de este software no implica ni otorga la adquisición de
    --   derechos de explotación ni de propiedad intelectual o industrial.


    --
    -- Functions
    --
    CREATE OR REPLACE FUNCTION upsert_zfx_userattribute(iduser integer, attcode text, attvalue text)
        RETURNS void AS
    $BODY$
    BEGIN
        LOOP
            UPDATE zfx_userattribute
            SET value = attvalue
            WHERE code = attcode
              AND id_user = iduser;
            IF found THEN
                RETURN;
            END IF;
            BEGIN
                INSERT INTO zfx_userattribute(id_user, code, value)
                VALUES (iduser, attcode, attvalue);
                RETURN;
            EXCEPTION
                WHEN unique_violation THEN
            END;
        END LOOP;
    END;
    $BODY$
        LANGUAGE plpgsql;

    --
    -- Drops
    --
    DROP TABLE IF EXISTS zfx_userattribute CASCADE;
    DROP TABLE IF EXISTS zfx_user_group CASCADE;
    DROP TABLE IF EXISTS zfx_group_permission CASCADE;
    DROP TABLE IF EXISTS zfx_user CASCADE;
    DROP TABLE IF EXISTS zfx_group CASCADE;
    DROP TABLE IF EXISTS zfx_permission CASCADE;

    --
    -- Tables
    --
    create table if not exists zfx_group
    (
        id          serial
            constraint zfx_group_pkey
                primary key,
        name        varchar(64)   not null,
        description varchar(1024) not null,
        ref1        integer,
        ref2        integer
    );


    create table if not exists zfx_permission
    (
        id          serial
            constraint zfx_permission_pkey
                primary key,
        code        varchar(128)  not null
            constraint zfx_permission_code_key
                unique,
        description varchar(1024) not null
    );



    create table if not exists zfx_group_permission
    (
        id_group      integer not null
            constraint "zfx_group_permission_relGroup"
                references zfx_group
                on update cascade on delete cascade,
        id_permission integer not null
            constraint "zfx_group_permission_relPermission"
                references zfx_permission
                on update cascade on delete cascade,
        constraint zfx_group_permission_pkey
            primary key (id_group, id_permission)
    );

    create table if not exists zfx_user
    (
        id            serial      not null
            constraint zfx_user_pkey
                primary key,
        login         varchar(64) not null
            constraint zfx_user_name_key
                unique,
        password_hash char(32)    not null,
        language      char(2)     not null,
        ref1          integer,
        ref2          integer
    );


    create table if not exists zfx_user_group
    (
        id_user  integer not null
            constraint "zfx_user_group_relUser"
                references zfx_user
                on update cascade on delete cascade,
        id_group integer not null
            constraint "zfx_user_group_relGroup"
                references zfx_group
                on update cascade on delete cascade,
        constraint zfx_user_group_pkey
            primary key (id_user, id_group)
    );



    create table if not exists zfx_userattribute
    (
        id_user integer      not null
            constraint "zfx_userattribute_relUser"
                references zfx_user
                on update cascade on delete cascade,
        code    varchar(256) not null,
        value   text,
        constraint zfx_userattribute_pkey
            primary key (id_user, code)
    );

