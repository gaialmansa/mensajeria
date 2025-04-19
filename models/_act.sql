-- Plantillas de modificaciones habituales
-- =======================================

-- Añadir columna
-- --------------
-- alter table  <NOMBRETABLA>  add column if not exists  <NOMBRECOL>  <TIPO>;
-- Ejemplo:
-- alter table  srv_ordentrab  add column if not exists  estado       integer;


-- Añadir columna con clave foranea
-- --------------------------------
-- alter table  <NOMBRETABLA>  add column if not exists   <NOMBRECOL>  <TIPO>
-- constraint   <NOMBRETABLA_rel_TABLARELACIONADA>
-- references   <TABLARELACIONADA> (<COLUMNA PK>)
-- on update    <ACCION>
-- on delete    <ACCION>
-- (no olvidar añadir un índice además)


-- Añadir índice
-- -------------
-- create index if not exists  <NOMBRETABLA_idx_CAMPO>   on  <TABLA>  (<COLUMNA>);
-- Ejemplo:
-- create index if not exists  srv_ordentrab_idx_estado  on  srv_ordentrab (estado);


-- Añadir índice UNIQUE ÚNICO
-- ---------------------------
-- create unique index   <NOMBRETABLA_unq_CAMPO>   on   <TABLA>   (<COLUMNA>[,<COLUMNA>[,...]]);
-- Ejemplo:
-- create unique index srv_operario_idx_id_zfx_user on srv_operario (id_zfx_user);


-- Renombrar columna
-- -----------------
-- alter table   <NOMBRETABLA>   rename column  <NOMBRENATIGUO>   to   <NOMBRENUEVO>;


-- Aquí van los cambios que se necesita hacer a la BD ya existente.
-- ================================================================





-- A partir de aquí las cosas que ya se han ejecutado pero todavía no se han pasado a _bd.sql.
-- ===========================================================================================
