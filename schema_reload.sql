--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

ALTER TABLE ONLY public.teams DROP CONSTRAINT teams_fk_league_id_fkey;
ALTER TABLE ONLY public.teams DROP CONSTRAINT teams_fk_city_id_fkey;
ALTER TABLE ONLY public.plays_for DROP CONSTRAINT plays_for_fk_team_id_fkey;
ALTER TABLE ONLY public.plays_for DROP CONSTRAINT plays_for_fk_player_id_fkey;
ALTER TABLE ONLY public.players DROP CONSTRAINT players_fk_position_code_fkey;
ALTER TABLE ONLY public.players DROP CONSTRAINT players_fk_nationality_fkey;
ALTER TABLE ONLY public.matches DROP CONSTRAINT matches_fk_stadium_id_fkey;
ALTER TABLE ONLY public.matches DROP CONSTRAINT matches_fk_home_id_fkey;
ALTER TABLE ONLY public.matches DROP CONSTRAINT matches_fk_away_id_fkey;
ALTER TABLE ONLY public.home DROP CONSTRAINT home_fk_team_id_fkey;
ALTER TABLE ONLY public.away DROP CONSTRAINT away_fk_team_id_fkey;
ALTER TABLE ONLY public.teams DROP CONSTRAINT teams_team_id_key;
ALTER TABLE ONLY public.teams DROP CONSTRAINT teams_pkey;
ALTER TABLE ONLY public.teams DROP CONSTRAINT teams_name_short_key;
ALTER TABLE ONLY public.stadiums DROP CONSTRAINT stadiums_stadium_id_key;
ALTER TABLE ONLY public.stadiums DROP CONSTRAINT stadiums_pkey;
ALTER TABLE ONLY public.positions DROP CONSTRAINT positions_position_key;
ALTER TABLE ONLY public.positions DROP CONSTRAINT positions_pkey;
ALTER TABLE ONLY public.plays_for DROP CONSTRAINT plays_for_pkey;
ALTER TABLE ONLY public.plays_for DROP CONSTRAINT plays_for_fk_player_id_key;
ALTER TABLE ONLY public.players DROP CONSTRAINT players_player_id_key;
ALTER TABLE ONLY public.players DROP CONSTRAINT players_pkey;
ALTER TABLE ONLY public.nationalities DROP CONSTRAINT nationalities_pkey;
ALTER TABLE ONLY public.matches DROP CONSTRAINT matches_pkey;
ALTER TABLE ONLY public.matches DROP CONSTRAINT matches_fk_stadium_id_match_date_key;
ALTER TABLE ONLY public.leagues DROP CONSTRAINT leagues_pkey;
ALTER TABLE ONLY public.leagues DROP CONSTRAINT leagues_league_id_key;
ALTER TABLE ONLY public.home DROP CONSTRAINT home_pkey;
ALTER TABLE ONLY public.cities DROP CONSTRAINT cities_pkey;
ALTER TABLE ONLY public.cities DROP CONSTRAINT cities_city_id_key;
ALTER TABLE ONLY public.away DROP CONSTRAINT away_pkey;
ALTER TABLE public.teams ALTER COLUMN team_id DROP DEFAULT;
ALTER TABLE public.stadiums ALTER COLUMN stadium_id DROP DEFAULT;
ALTER TABLE public.players ALTER COLUMN player_id DROP DEFAULT;
ALTER TABLE public.leagues ALTER COLUMN league_id DROP DEFAULT;
ALTER TABLE public.home ALTER COLUMN home_id DROP DEFAULT;
ALTER TABLE public.cities ALTER COLUMN city_id DROP DEFAULT;
ALTER TABLE public.away ALTER COLUMN away_id DROP DEFAULT;
DROP SEQUENCE public.teams_team_id_seq;
DROP TABLE public.teams;
DROP SEQUENCE public.stadiums_stadium_id_seq;
DROP TABLE public.stadiums;
DROP TABLE public.positions;
DROP TABLE public.plays_for;
DROP SEQUENCE public.players_player_id_seq;
DROP TABLE public.players;
DROP TABLE public.nationalities;
DROP TABLE public.matches;
DROP SEQUENCE public.leagues_league_id_seq;
DROP TABLE public.leagues;
DROP SEQUENCE public.home_home_id_seq;
DROP TABLE public.home;
DROP SEQUENCE public.cities_city_id_seq;
DROP TABLE public.cities;
DROP SEQUENCE public.away_away_id_seq;
DROP TABLE public.away;
DROP FUNCTION public.truncate_tables(username character varying);
DROP SCHEMA public;
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET search_path = public, pg_catalog;

--
-- Name: truncate_tables(character varying); Type: FUNCTION; Schema: public; Owner: football
--

CREATE FUNCTION truncate_tables(username character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
    statements CURSOR FOR
        SELECT tablename FROM pg_tables
        WHERE tableowner = username AND schemaname = 'public';
BEGIN
    FOR stmt IN statements LOOP
        EXECUTE 'TRUNCATE TABLE ' || quote_ident(stmt.tablename) || ' CASCADE;';
    END LOOP;
END;
$$;


ALTER FUNCTION public.truncate_tables(username character varying) OWNER TO football;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: away; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE away (
    away_id integer NOT NULL,
    fk_team_id integer NOT NULL,
    goals smallint DEFAULT 0 NOT NULL,
    shots smallint DEFAULT 0 NOT NULL,
    fouls smallint DEFAULT 0 NOT NULL,
    yellows smallint DEFAULT 0 NOT NULL,
    reds smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.away OWNER TO postgres;

--
-- Name: away_away_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE away_away_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.away_away_id_seq OWNER TO postgres;

--
-- Name: away_away_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE away_away_id_seq OWNED BY away.away_id;


--
-- Name: cities; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE cities (
    city_id integer NOT NULL,
    city character varying(150) NOT NULL
);


ALTER TABLE public.cities OWNER TO postgres;

--
-- Name: cities_city_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE cities_city_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.cities_city_id_seq OWNER TO postgres;

--
-- Name: cities_city_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE cities_city_id_seq OWNED BY cities.city_id;


--
-- Name: home; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE home (
    home_id integer NOT NULL,
    fk_team_id integer NOT NULL,
    goals smallint DEFAULT 0 NOT NULL,
    shots smallint DEFAULT 0 NOT NULL,
    fouls smallint DEFAULT 0 NOT NULL,
    yellows smallint DEFAULT 0 NOT NULL,
    reds smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.home OWNER TO postgres;

--
-- Name: home_home_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE home_home_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.home_home_id_seq OWNER TO postgres;

--
-- Name: home_home_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE home_home_id_seq OWNED BY home.home_id;


--
-- Name: leagues; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE leagues (
    league_id integer NOT NULL,
    league character varying(150) NOT NULL
);


ALTER TABLE public.leagues OWNER TO postgres;

--
-- Name: leagues_league_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE leagues_league_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.leagues_league_id_seq OWNER TO postgres;

--
-- Name: leagues_league_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE leagues_league_id_seq OWNED BY leagues.league_id;


--
-- Name: matches; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE matches (
    fk_home_id integer NOT NULL,
    fk_away_id integer NOT NULL,
    fk_stadium_id integer NOT NULL,
    match_date date NOT NULL
);


ALTER TABLE public.matches OWNER TO postgres;

--
-- Name: nationalities; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE nationalities (
    nationality character varying(150) NOT NULL
);


ALTER TABLE public.nationalities OWNER TO postgres;

--
-- Name: players; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE players (
    player_id integer NOT NULL,
    fk_nationality character varying(150),
    fk_position_code character varying(10),
    name character varying(150) NOT NULL,
    surname character varying(150) NOT NULL,
    age smallint DEFAULT 1 NOT NULL,
    speed smallint DEFAULT 0 NOT NULL,
    shoot smallint DEFAULT 0 NOT NULL,
    drible smallint DEFAULT 0 NOT NULL,
    defence smallint DEFAULT 0 NOT NULL,
    pass smallint DEFAULT 0 NOT NULL,
    CONSTRAINT valid_stats CHECK (((((((((((speed >= 0) AND (speed <= 100)) AND (shoot >= 0)) AND (shoot <= 100)) AND (drible >= 0)) AND (drible <= 100)) AND (defence >= 0)) AND (defence <= 100)) AND (pass >= 0)) AND (pass <= 100)))
);


ALTER TABLE public.players OWNER TO postgres;

--
-- Name: players_player_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE players_player_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.players_player_id_seq OWNER TO postgres;

--
-- Name: players_player_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE players_player_id_seq OWNED BY players.player_id;


--
-- Name: plays_for; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE plays_for (
    fk_player_id integer NOT NULL,
    fk_team_id integer NOT NULL,
    start_date date,
    end_date date,
    CONSTRAINT valid_date_span CHECK ((end_date > start_date))
);


ALTER TABLE public.plays_for OWNER TO postgres;

--
-- Name: positions; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE positions (
    "position" character varying(150),
    position_code character varying(10) NOT NULL
);


ALTER TABLE public.positions OWNER TO postgres;

--
-- Name: stadiums; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE stadiums (
    stadium_id integer NOT NULL,
    seats integer NOT NULL,
    name character varying(150) NOT NULL
);


ALTER TABLE public.stadiums OWNER TO postgres;

--
-- Name: stadiums_stadium_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE stadiums_stadium_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stadiums_stadium_id_seq OWNER TO postgres;

--
-- Name: stadiums_stadium_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE stadiums_stadium_id_seq OWNED BY stadiums.stadium_id;


--
-- Name: teams; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE teams (
    team_id integer NOT NULL,
    est integer NOT NULL,
    name character varying(150) NOT NULL,
    name_short character varying(150),
    fk_city_id integer NOT NULL,
    fk_league_id integer NOT NULL
);


ALTER TABLE public.teams OWNER TO postgres;

--
-- Name: teams_team_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE teams_team_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.teams_team_id_seq OWNER TO postgres;

--
-- Name: teams_team_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE teams_team_id_seq OWNED BY teams.team_id;


--
-- Name: away_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY away ALTER COLUMN away_id SET DEFAULT nextval('away_away_id_seq'::regclass);


--
-- Name: city_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY cities ALTER COLUMN city_id SET DEFAULT nextval('cities_city_id_seq'::regclass);


--
-- Name: home_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY home ALTER COLUMN home_id SET DEFAULT nextval('home_home_id_seq'::regclass);


--
-- Name: league_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY leagues ALTER COLUMN league_id SET DEFAULT nextval('leagues_league_id_seq'::regclass);


--
-- Name: player_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY players ALTER COLUMN player_id SET DEFAULT nextval('players_player_id_seq'::regclass);


--
-- Name: stadium_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY stadiums ALTER COLUMN stadium_id SET DEFAULT nextval('stadiums_stadium_id_seq'::regclass);


--
-- Name: team_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY teams ALTER COLUMN team_id SET DEFAULT nextval('teams_team_id_seq'::regclass);


--
-- Name: away_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY away
    ADD CONSTRAINT away_pkey PRIMARY KEY (away_id);


--
-- Name: cities_city_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY cities
    ADD CONSTRAINT cities_city_id_key UNIQUE (city_id);


--
-- Name: cities_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY cities
    ADD CONSTRAINT cities_pkey PRIMARY KEY (city);


--
-- Name: home_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY home
    ADD CONSTRAINT home_pkey PRIMARY KEY (home_id);


--
-- Name: leagues_league_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY leagues
    ADD CONSTRAINT leagues_league_id_key UNIQUE (league_id);


--
-- Name: leagues_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY leagues
    ADD CONSTRAINT leagues_pkey PRIMARY KEY (league);


--
-- Name: matches_fk_stadium_id_match_date_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY matches
    ADD CONSTRAINT matches_fk_stadium_id_match_date_key UNIQUE (fk_stadium_id, match_date);


--
-- Name: matches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY matches
    ADD CONSTRAINT matches_pkey PRIMARY KEY (fk_home_id, fk_away_id, match_date, fk_stadium_id);


--
-- Name: nationalities_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY nationalities
    ADD CONSTRAINT nationalities_pkey PRIMARY KEY (nationality);


--
-- Name: players_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY players
    ADD CONSTRAINT players_pkey PRIMARY KEY (name, surname, age);


--
-- Name: players_player_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY players
    ADD CONSTRAINT players_player_id_key UNIQUE (player_id);


--
-- Name: plays_for_fk_player_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY plays_for
    ADD CONSTRAINT plays_for_fk_player_id_key UNIQUE (fk_player_id);


--
-- Name: plays_for_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY plays_for
    ADD CONSTRAINT plays_for_pkey PRIMARY KEY (fk_player_id, fk_team_id);


--
-- Name: positions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY positions
    ADD CONSTRAINT positions_pkey PRIMARY KEY (position_code);


--
-- Name: positions_position_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY positions
    ADD CONSTRAINT positions_position_key UNIQUE ("position");


--
-- Name: stadiums_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY stadiums
    ADD CONSTRAINT stadiums_pkey PRIMARY KEY (name);


--
-- Name: stadiums_stadium_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY stadiums
    ADD CONSTRAINT stadiums_stadium_id_key UNIQUE (stadium_id);


--
-- Name: teams_name_short_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY teams
    ADD CONSTRAINT teams_name_short_key UNIQUE (name_short);


--
-- Name: teams_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY teams
    ADD CONSTRAINT teams_pkey PRIMARY KEY (name, fk_city_id, fk_league_id);


--
-- Name: teams_team_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY teams
    ADD CONSTRAINT teams_team_id_key UNIQUE (team_id);


--
-- Name: away_fk_team_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY away
    ADD CONSTRAINT away_fk_team_id_fkey FOREIGN KEY (fk_team_id) REFERENCES teams(team_id);


--
-- Name: home_fk_team_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY home
    ADD CONSTRAINT home_fk_team_id_fkey FOREIGN KEY (fk_team_id) REFERENCES teams(team_id);


--
-- Name: matches_fk_away_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY matches
    ADD CONSTRAINT matches_fk_away_id_fkey FOREIGN KEY (fk_away_id) REFERENCES away(away_id);


--
-- Name: matches_fk_home_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY matches
    ADD CONSTRAINT matches_fk_home_id_fkey FOREIGN KEY (fk_home_id) REFERENCES home(home_id);


--
-- Name: matches_fk_stadium_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY matches
    ADD CONSTRAINT matches_fk_stadium_id_fkey FOREIGN KEY (fk_stadium_id) REFERENCES stadiums(stadium_id);


--
-- Name: players_fk_nationality_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY players
    ADD CONSTRAINT players_fk_nationality_fkey FOREIGN KEY (fk_nationality) REFERENCES nationalities(nationality);


--
-- Name: players_fk_position_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY players
    ADD CONSTRAINT players_fk_position_code_fkey FOREIGN KEY (fk_position_code) REFERENCES positions(position_code);


--
-- Name: plays_for_fk_player_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY plays_for
    ADD CONSTRAINT plays_for_fk_player_id_fkey FOREIGN KEY (fk_player_id) REFERENCES players(player_id);


--
-- Name: plays_for_fk_team_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY plays_for
    ADD CONSTRAINT plays_for_fk_team_id_fkey FOREIGN KEY (fk_team_id) REFERENCES teams(team_id);


--
-- Name: teams_fk_city_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY teams
    ADD CONSTRAINT teams_fk_city_id_fkey FOREIGN KEY (fk_city_id) REFERENCES cities(city_id);


--
-- Name: teams_fk_league_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY teams
    ADD CONSTRAINT teams_fk_league_id_fkey FOREIGN KEY (fk_league_id) REFERENCES leagues(league_id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

