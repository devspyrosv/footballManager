
DROP DATABASE IF EXISTS football;
DROP ROLE IF EXISTS football;

/* Create user/role */
CREATE USER football CREATEDB CREATEUSER PASSWORD 'pass';

/* Create user's db */
CREATE DATABASE football WITH ENCODING 'UTF-8' LC_COLLATE='el_GR.utf8' LC_CTYPE='el_GR.utf8' TEMPLATE=template0 OWNER=football;

/* USE db */
/* \connect football */

CREATE TABLE Positions (
    position VARCHAR(150) UNIQUE,
    position_code VARCHAR(10) PRIMARY KEY
);

CREATE TABLE Nationalities (    
    nationality VARCHAR(150) PRIMARY KEY    
);

CREATE TABLE Players (
    player_id SERIAL UNIQUE,
    fk_nationality VARCHAR(150) REFERENCES Nationalities(nationality),
    fk_position_code VARCHAR(10) REFERENCES Positions(position_code),
    
    name VARCHAR(150) NOT NULL,
    surname VARCHAR(150) NOT NULL,    
    age SMALLINT NOT NULL DEFAULT 1,       
    speed SMALLINT NOT NULL DEFAULT 0,
    shoot SMALLINT NOT NULL DEFAULT 0,
    drible SMALLINT NOT NULL DEFAULT 0,
    defence SMALLINT NOT NULL DEFAULT 0,
    pass SMALLINT NOT NULL DEFAULT 0,

    /* Valid statistics values are from 0 to 100*/
    CONSTRAINT valid_stats CHECK ( 
        ( 
            (speed>=0) AND (speed<=100)
            AND (shoot>=0) AND (shoot<=100)            
            AND (drible>=0) AND (drible<=100)
            AND (defence>=0) AND (defence<=100)
            AND (pass>=0) AND (pass<=100)
                       
        ) 
    ),
   PRIMARY KEY(name,surname,age)
);

CREATE TABLE Cities (
    city_id SERIAL UNIQUE,
    city VARCHAR(150) PRIMARY KEY
);

CREATE TABLE Leagues (
    league_id SERIAL UNIQUE,
    league VARCHAR(150) PRIMARY KEY
);

CREATE TABLE Teams (
    team_id SERIAL UNIQUE,
    /* Teams usally state their establishment date as an integer not a date e.g. est 1920 */
    est INTEGER NOT NULL,
    name VARCHAR(150) NOT NULL,
    name_short VARCHAR(150) UNIQUE,
    fk_city_id INTEGER REFERENCES Cities(city_id) NOT NULL,
    fk_league_id INTEGER REFERENCES Leagues(league_id) NOT NULL,
    PRIMARY KEY (name,fk_city_id,fk_league_id)
);

CREATE TABLE Plays_for (
    /* 1 player can belong to 1 team */
    fk_player_id INTEGER REFERENCES Players(player_id) UNIQUE, 

    /* But a team can have many players, so its not UNIQUE */
    fk_team_id INTEGER REFERENCES Teams(team_id),
    start_date date,
    end_date date,
    CONSTRAINT valid_date_span CHECK ( 
        ( 
            (end_date> start_date) 
        ) 
    ),
    PRIMARY KEY (fk_player_id,fk_team_id)
);

CREATE TABLE Stadiums (
    stadium_id SERIAL UNIQUE,
    seats INTEGER NOT NULL,
    name VARCHAR(150) PRIMARY KEY
);

CREATE TABLE Home (
    home_id SERIAL PRIMARY KEY,
    fk_team_id INTEGER REFERENCES Teams(team_id) NOT NULL,    
    /* Valid statistics values start from 0 and have no upper limit -> no constraint needed */
    goals SMALLINT NOT NULL DEFAULT 0,
    shots SMALLINT NOT NULL DEFAULT 0,
    fouls SMALLINT NOT NULL DEFAULT 0,
    yellows SMALLINT NOT NULL DEFAULT 0,
    reds SMALLINT NOT NULL DEFAULT 0
);

CREATE TABLE Away (    
    away_id SERIAL PRIMARY KEY,
    fk_team_id INTEGER REFERENCES Teams(team_id) NOT NULL,    
    /* Valid statistics values start from 0 and have no upper limit -> no constraint needed */
    goals SMALLINT NOT NULL DEFAULT 0,
    shots SMALLINT NOT NULL DEFAULT 0,
    fouls SMALLINT NOT NULL DEFAULT 0,
    yellows SMALLINT NOT NULL DEFAULT 0,
    reds SMALLINT NOT NULL DEFAULT 0
);

CREATE TABLE Matches (
    fk_home_id INTEGER REFERENCES Home(home_id) NOT NULL,
    fk_away_id INTEGER REFERENCES Away(away_id) NOT NULL,
    fk_stadium_id INTEGER REFERENCES Stadiums(stadium_id) NOT NULL,   
    match_date date NOT NULL,
    /* Composite primary key. Contains home team id, away team id, stadium_id AND match date. Nobody says a team can play only one game in a day. Also the same game between the same teams and in the same stadium, can be played in different dates e.g. once a year or less. */
    PRIMARY KEY (fk_home_id,fk_away_id,match_date,fk_stadium_id),
    UNIQUE (fk_stadium_id, match_date)
);




