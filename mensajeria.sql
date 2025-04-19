--
-- PostgreSQL database dump
--

-- Dumped from database version 10.23 (Ubuntu 10.23-0ubuntu0.18.04.2)
-- Dumped by pg_dump version 10.23 (Ubuntu 10.23-0ubuntu0.18.04.2)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: upsert_zfx_userattribute(integer, text, text); Type: FUNCTION; Schema: public; Owner: pofenas
--

CREATE FUNCTION public.upsert_zfx_userattribute(iduser integer, attcode text, attvalue text) RETURNS void
    LANGUAGE plpgsql
    AS $$
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
    $$;


ALTER FUNCTION public.upsert_zfx_userattribute(iduser integer, attcode text, attvalue text) OWNER TO pofenas;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: beepers; Type: TABLE; Schema: public; Owner: software
--

CREATE TABLE public.beepers (
    id_beeper integer NOT NULL,
    id_usuario integer,
    mac character(12)
);


ALTER TABLE public.beepers OWNER TO software;

--
-- Name: beepers_id_beeper_seq; Type: SEQUENCE; Schema: public; Owner: software
--

CREATE SEQUENCE public.beepers_id_beeper_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.beepers_id_beeper_seq OWNER TO software;

--
-- Name: beepers_id_beeper_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: software
--

ALTER SEQUENCE public.beepers_id_beeper_seq OWNED BY public.beepers.id_beeper;


--
-- Name: grupos_id_seq; Type: SEQUENCE; Schema: public; Owner: pofenas
--

CREATE SEQUENCE public.grupos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.grupos_id_seq OWNER TO pofenas;

--
-- Name: grupos; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.grupos (
    id_grupo integer DEFAULT nextval('public.grupos_id_seq'::regclass) NOT NULL,
    grupo character varying(32) NOT NULL,
    id_padre integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.grupos OWNER TO pofenas;

--
-- Name: mensajes_id_seq; Type: SEQUENCE; Schema: public; Owner: pofenas
--

CREATE SEQUENCE public.mensajes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.mensajes_id_seq OWNER TO pofenas;

--
-- Name: mensajes; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.mensajes (
    id_mensaje integer DEFAULT nextval('public.mensajes_id_seq'::regclass) NOT NULL,
    id_usuario_o integer NOT NULL,
    hora_edicion timestamp without time zone NOT NULL,
    mensaje character varying(128) NOT NULL
);


ALTER TABLE public.mensajes OWNER TO pofenas;

--
-- Name: COLUMN mensajes.id_usuario_o; Type: COMMENT; Schema: public; Owner: pofenas
--

COMMENT ON COLUMN public.mensajes.id_usuario_o IS 'origen del mensaje';


--
-- Name: registro; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.registro (
    numero integer NOT NULL,
    id_usuario integer NOT NULL,
    hora timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.registro OWNER TO pofenas;

--
-- Name: rel_usuarios_grupos_id_seq; Type: SEQUENCE; Schema: public; Owner: pofenas
--

CREATE SEQUENCE public.rel_usuarios_grupos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.rel_usuarios_grupos_id_seq OWNER TO pofenas;

--
-- Name: rmu_id_seq; Type: SEQUENCE; Schema: public; Owner: pofenas
--

CREATE SEQUENCE public.rmu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.rmu_id_seq OWNER TO pofenas;

--
-- Name: rmu; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.rmu (
    id integer DEFAULT nextval('public.rmu_id_seq'::regclass) NOT NULL,
    id_mensaje integer NOT NULL,
    id_usuario integer NOT NULL,
    visto boolean DEFAULT false NOT NULL,
    atendido boolean DEFAULT false NOT NULL,
    rehusado boolean DEFAULT false NOT NULL,
    hora_visto timestamp without time zone,
    hora_atendido timestamp without time zone,
    hora_rehusado timestamp without time zone
);


ALTER TABLE public.rmu OWNER TO pofenas;

--
-- Name: rug; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.rug (
    id integer DEFAULT nextval('public.rel_usuarios_grupos_id_seq'::regclass) NOT NULL,
    id_usuario integer NOT NULL,
    id_grupo integer NOT NULL
);


ALTER TABLE public.rug OWNER TO pofenas;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: pofenas
--

CREATE SEQUENCE public.usuarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.usuarios_id_seq OWNER TO pofenas;

--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.usuarios (
    id_usuario integer DEFAULT nextval('public.usuarios_id_seq'::regclass) NOT NULL,
    usuario character varying(16) NOT NULL,
    nombre character varying(128) NOT NULL,
    observaciones text
);


ALTER TABLE public.usuarios OWNER TO pofenas;

--
-- Name: zfx_group; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.zfx_group (
    id integer NOT NULL,
    name character varying(64) NOT NULL,
    description character varying(1024) NOT NULL,
    ref1 integer,
    ref2 integer
);


ALTER TABLE public.zfx_group OWNER TO pofenas;

--
-- Name: zfx_group_id_seq; Type: SEQUENCE; Schema: public; Owner: pofenas
--

CREATE SEQUENCE public.zfx_group_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.zfx_group_id_seq OWNER TO pofenas;

--
-- Name: zfx_group_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: pofenas
--

ALTER SEQUENCE public.zfx_group_id_seq OWNED BY public.zfx_group.id;


--
-- Name: zfx_group_permission; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.zfx_group_permission (
    id_group integer NOT NULL,
    id_permission integer NOT NULL
);


ALTER TABLE public.zfx_group_permission OWNER TO pofenas;

--
-- Name: zfx_permission; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.zfx_permission (
    id integer NOT NULL,
    code character varying(128) NOT NULL,
    description character varying(1024) NOT NULL
);


ALTER TABLE public.zfx_permission OWNER TO pofenas;

--
-- Name: zfx_permission_id_seq; Type: SEQUENCE; Schema: public; Owner: pofenas
--

CREATE SEQUENCE public.zfx_permission_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.zfx_permission_id_seq OWNER TO pofenas;

--
-- Name: zfx_permission_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: pofenas
--

ALTER SEQUENCE public.zfx_permission_id_seq OWNED BY public.zfx_permission.id;


--
-- Name: zfx_user; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.zfx_user (
    id integer NOT NULL,
    login character varying(64) NOT NULL,
    password_hash character(32) NOT NULL,
    language character(2) NOT NULL,
    ref1 integer,
    ref2 integer
);


ALTER TABLE public.zfx_user OWNER TO pofenas;

--
-- Name: zfx_user_group; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.zfx_user_group (
    id_user integer NOT NULL,
    id_group integer NOT NULL
);


ALTER TABLE public.zfx_user_group OWNER TO pofenas;

--
-- Name: zfx_user_id_seq; Type: SEQUENCE; Schema: public; Owner: pofenas
--

CREATE SEQUENCE public.zfx_user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.zfx_user_id_seq OWNER TO pofenas;

--
-- Name: zfx_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: pofenas
--

ALTER SEQUENCE public.zfx_user_id_seq OWNED BY public.zfx_user.id;


--
-- Name: zfx_userattribute; Type: TABLE; Schema: public; Owner: pofenas
--

CREATE TABLE public.zfx_userattribute (
    id_user integer NOT NULL,
    code character varying(256) NOT NULL,
    value text
);


ALTER TABLE public.zfx_userattribute OWNER TO pofenas;

--
-- Name: beepers id_beeper; Type: DEFAULT; Schema: public; Owner: software
--

ALTER TABLE ONLY public.beepers ALTER COLUMN id_beeper SET DEFAULT nextval('public.beepers_id_beeper_seq'::regclass);


--
-- Name: zfx_group id; Type: DEFAULT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_group ALTER COLUMN id SET DEFAULT nextval('public.zfx_group_id_seq'::regclass);


--
-- Name: zfx_permission id; Type: DEFAULT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_permission ALTER COLUMN id SET DEFAULT nextval('public.zfx_permission_id_seq'::regclass);


--
-- Name: zfx_user id; Type: DEFAULT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_user ALTER COLUMN id SET DEFAULT nextval('public.zfx_user_id_seq'::regclass);


--
-- Data for Name: beepers; Type: TABLE DATA; Schema: public; Owner: software
--

COPY public.beepers (id_beeper, id_usuario, mac) FROM stdin;
1	\N	mac1        
2	\N	mac2        
3	\N	mac3        
4	\N	mac4        
5	\N	mac5        
\.


--
-- Data for Name: grupos; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.grupos (id_grupo, grupo, id_padre) FROM stdin;
18	Personal	0
19	Personal Sanitario	18
6	Médicos	19
4	Celadores	18
3	Enfermeras	19
\.


--
-- Data for Name: mensajes; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.mensajes (id_mensaje, id_usuario_o, hora_edicion, mensaje) FROM stdin;
7	5	2024-11-13 11:50:14	Hay una radiografía para Pedro Perez Pofenas en el control de urgencias
8	5	2024-11-13 11:59:58	Hay una radiografía para Pedro Perez Pofenas en el control de urgencias
9	5	2024-11-13 12:02:16	Hay una radiografía para Pedro Perez Pofenas en el control de urgencias
10	5	2024-11-13 12:02:19	Hay una radiografía para Pedro Perez Pofenas en el control de urgencias
11	5	2024-11-13 12:02:39	Hay una radiografía para Pedro Perez Pofenas en el control de urgencias
12	5	2024-11-13 12:03:08	Hay una radiografía para Pedro Perez Pofenas en el control de urgencias
13	3	2024-11-13 12:31:57	
\.


--
-- Data for Name: registro; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.registro (numero, id_usuario, hora) FROM stdin;
\.


--
-- Data for Name: rmu; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.rmu (id, id_mensaje, id_usuario, visto, atendido, rehusado, hora_visto, hora_atendido, hora_rehusado) FROM stdin;
4	12	4	f	f	f	\N	\N	\N
3	12	3	t	t	t	2024-11-13 12:40:41	2024-11-13 12:43:26	2024-11-13 12:43:56
2	7	3	t	f	f	2024-11-13 12:44:15	\N	\N
\.


--
-- Data for Name: rug; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.rug (id, id_usuario, id_grupo) FROM stdin;
3	4	3
4	4	4
6	5	3
11	3	4
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.usuarios (id_usuario, usuario, nombre, observaciones) FROM stdin;
4	cel2	celador de urgencias 2	
5	enf1	enfermera de urgencias 1	
6	enf2	enfermera de urgencias 2	
2	alias	Borrame	Observaciones\r\nUsuario malo malo\r\nHay que deshacerse de este tio cuanto antes
3	cel1	Celador de urgencias 1	
7	med1	medico de urgencias 1	\N
8	med2	medico de urgencias 2	\N
\.


--
-- Data for Name: zfx_group; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.zfx_group (id, name, description, ref1, ref2) FROM stdin;
1	u-admin	Grupo propio del usuario admin	\N	\N
\.


--
-- Data for Name: zfx_group_permission; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.zfx_group_permission (id_group, id_permission) FROM stdin;
1	1
1	2
1	3
\.


--
-- Data for Name: zfx_permission; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.zfx_permission (id, code, description) FROM stdin;
1	menu-sis-cuentas-usuarios	Menú Sistema/Cuentas/Usuarios
2	menu-sis-cuentas-grupos	Menú Sistema/Cuentas/Grupos
3	menu-sis-cuentas-permisos	Menú Sistema/Cuentas/Permisos
\.


--
-- Data for Name: zfx_user; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.zfx_user (id, login, password_hash, language, ref1, ref2) FROM stdin;
2	admin	21232f297a57a5a743894a0e4a801fc3	es	\N	\N
1	mostrenko	aaf216a81361a389a07308f8dfc59884	es	\N	\N
\.


--
-- Data for Name: zfx_user_group; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.zfx_user_group (id_user, id_group) FROM stdin;
1	1
\.


--
-- Data for Name: zfx_userattribute; Type: TABLE DATA; Schema: public; Owner: pofenas
--

COPY public.zfx_userattribute (id_user, code, value) FROM stdin;
\.


--
-- Name: beepers_id_beeper_seq; Type: SEQUENCE SET; Schema: public; Owner: software
--

SELECT pg_catalog.setval('public.beepers_id_beeper_seq', 5, true);


--
-- Name: grupos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pofenas
--

SELECT pg_catalog.setval('public.grupos_id_seq', 20, true);


--
-- Name: mensajes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pofenas
--

SELECT pg_catalog.setval('public.mensajes_id_seq', 13, true);


--
-- Name: rel_usuarios_grupos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pofenas
--

SELECT pg_catalog.setval('public.rel_usuarios_grupos_id_seq', 11, true);


--
-- Name: rmu_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pofenas
--

SELECT pg_catalog.setval('public.rmu_id_seq', 4, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pofenas
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 11, true);


--
-- Name: zfx_group_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pofenas
--

SELECT pg_catalog.setval('public.zfx_group_id_seq', 1, true);


--
-- Name: zfx_permission_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pofenas
--

SELECT pg_catalog.setval('public.zfx_permission_id_seq', 3, true);


--
-- Name: zfx_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pofenas
--

SELECT pg_catalog.setval('public.zfx_user_id_seq', 2, true);


--
-- Name: beepers beepers_pkey; Type: CONSTRAINT; Schema: public; Owner: software
--

ALTER TABLE ONLY public.beepers
    ADD CONSTRAINT beepers_pkey PRIMARY KEY (id_beeper);


--
-- Name: grupos grupos_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.grupos
    ADD CONSTRAINT grupos_pkey PRIMARY KEY (id_grupo);


--
-- Name: mensajes mensajes_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.mensajes
    ADD CONSTRAINT mensajes_pkey PRIMARY KEY (id_mensaje);


--
-- Name: registro registro_numero; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.registro
    ADD CONSTRAINT registro_numero PRIMARY KEY (numero);


--
-- Name: rug rel_usuarios_grupos_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.rug
    ADD CONSTRAINT rel_usuarios_grupos_pkey PRIMARY KEY (id);


--
-- Name: rmu rmu_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.rmu
    ADD CONSTRAINT rmu_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id_usuario);


--
-- Name: zfx_group_permission zfx_group_permission_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_group_permission
    ADD CONSTRAINT zfx_group_permission_pkey PRIMARY KEY (id_group, id_permission);


--
-- Name: zfx_group zfx_group_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_group
    ADD CONSTRAINT zfx_group_pkey PRIMARY KEY (id);


--
-- Name: zfx_permission zfx_permission_code_key; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_permission
    ADD CONSTRAINT zfx_permission_code_key UNIQUE (code);


--
-- Name: zfx_permission zfx_permission_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_permission
    ADD CONSTRAINT zfx_permission_pkey PRIMARY KEY (id);


--
-- Name: zfx_user_group zfx_user_group_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_user_group
    ADD CONSTRAINT zfx_user_group_pkey PRIMARY KEY (id_user, id_group);


--
-- Name: zfx_user zfx_user_name_key; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_user
    ADD CONSTRAINT zfx_user_name_key UNIQUE (login);


--
-- Name: zfx_user zfx_user_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_user
    ADD CONSTRAINT zfx_user_pkey PRIMARY KEY (id);


--
-- Name: zfx_userattribute zfx_userattribute_pkey; Type: CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_userattribute
    ADD CONSTRAINT zfx_userattribute_pkey PRIMARY KEY (id_user, code);


--
-- Name: beepers beepers_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: software
--

ALTER TABLE ONLY public.beepers
    ADD CONSTRAINT beepers_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario);


--
-- Name: mensajes mensajes_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.mensajes
    ADD CONSTRAINT mensajes_id_usuario_fkey FOREIGN KEY (id_usuario_o) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: registro registro_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.registro
    ADD CONSTRAINT registro_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: rug rel_usuarios_grupos_id_grupo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.rug
    ADD CONSTRAINT rel_usuarios_grupos_id_grupo_fkey FOREIGN KEY (id_grupo) REFERENCES public.grupos(id_grupo) ON DELETE CASCADE;


--
-- Name: rug rel_usuarios_grupos_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.rug
    ADD CONSTRAINT rel_usuarios_grupos_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: rmu rmu_id_mensaje_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.rmu
    ADD CONSTRAINT rmu_id_mensaje_fkey FOREIGN KEY (id_mensaje) REFERENCES public.mensajes(id_mensaje) ON DELETE CASCADE;


--
-- Name: rmu rmu_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.rmu
    ADD CONSTRAINT rmu_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: zfx_group_permission zfx_group_permission_relGroup; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_group_permission
    ADD CONSTRAINT "zfx_group_permission_relGroup" FOREIGN KEY (id_group) REFERENCES public.zfx_group(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: zfx_group_permission zfx_group_permission_relPermission; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_group_permission
    ADD CONSTRAINT "zfx_group_permission_relPermission" FOREIGN KEY (id_permission) REFERENCES public.zfx_permission(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: zfx_user_group zfx_user_group_relGroup; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_user_group
    ADD CONSTRAINT "zfx_user_group_relGroup" FOREIGN KEY (id_group) REFERENCES public.zfx_group(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: zfx_user_group zfx_user_group_relUser; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_user_group
    ADD CONSTRAINT "zfx_user_group_relUser" FOREIGN KEY (id_user) REFERENCES public.zfx_user(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: zfx_userattribute zfx_userattribute_relUser; Type: FK CONSTRAINT; Schema: public; Owner: pofenas
--

ALTER TABLE ONLY public.zfx_userattribute
    ADD CONSTRAINT "zfx_userattribute_relUser" FOREIGN KEY (id_user) REFERENCES public.zfx_user(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: TABLE beepers; Type: ACL; Schema: public; Owner: software
--

GRANT ALL ON TABLE public.beepers TO pofenas;


--
-- Name: SEQUENCE beepers_id_beeper_seq; Type: ACL; Schema: public; Owner: software
--

GRANT ALL ON SEQUENCE public.beepers_id_beeper_seq TO pofenas;


--
-- PostgreSQL database dump complete
--

