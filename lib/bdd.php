<?php namespace php_ranking;

use \PDO;

class BDD
{

    private $_configuration;
    private $_pdo;
    public function __construct( $configuration )
    {
        $this->_configuration = $configuration;

        if(!class_exists('SQLite3'))
          die("SQLite 3 NOT supported.");
        
        try{
            $this->_pdo = new PDO(
                $this->_configuration->get_configuration_bdd("url"),
                $this->_configuration->get_configuration_bdd("login"),
                $this->_configuration->get_configuration_bdd("password") );
                $this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(Exception $e) {
            echo "Impossible d'accéder à la base de données ".$this->_configuration->get_configuration_bdd("url")." : ".$e->getMessage();
            die();
        }
    }

    public function getPDO(){
        return $this->_pdo;
    }

    public function createTableResults(){

        $this->_pdo->query("DROP TABLE IF EXISTS RESULTS") or die("Error to DROP RESULTS");
        
        $query = "CREATE TABLE RESULTS(
            SAISON int NOT NULL,
            DISCIPLINE text NOT NULL,
            NO_LICENCE text NOT NULL,
            NOM_PERSONNE text NOT NULL,
            PRENOM_PERSONNE text NOT NULL,
            HORS_F text,
            SEXE_PERSONNE text NOT NULL,
            CAT text NOT NULL,
            CAT_S text NOT NULL,
            CODE_STRUCTURE text NOT NULL,
            NOM_STRUCTURE text NOT NULL,
            ARME text NOT NULL,
            NIVEAU text,
            SCORE int NOT NULL,
            PAILLE int NOT NULL,
            DIX int NOT NULL,
            NEUF int NOT NULL,
            DISTANCE int NOT NULL,
            BLASON int NOT NULL,
            D_DEBUT_CONCOURS text NOT NULL,
            D_FIN_CONCOURS text NOT NULL,
            LIEU_CONCOURS text NOT NULL,
            CODE_STRUCTURE_ORGANISATRICE text NOT NULL,
            NOM_STRUCTURE_ORGANISATRICE text NOT NULL,
            FORMULE_TIR text NOT NULL,
            NIVEAU_CHPT text,
            DETAIL_NIVEAU_CHPT text,
            DISTINCTION text,
            PLACE_QUALIF int NOT NULL,
            SCORE_DIST1 int NOT NULL,
            SCORE_DIST2 int NOT NULL,
            SCORE_DIST3 int NOT NULL,
            SCORE_DIST4 int NOT NULL,
            SCORE_32 int,
            SCORE_16 int,
            SCORE_8 int,
            SCORE_QUART int,
            SCORE_DEMI int,
            SCORE_PETITE_FINAL int,
            SCORE_FINAL int,
            PLACE_DEF int NOT NULL,
            NUM_DEPART int NOT NULL,
            PRIMARY KEY (NO_LICENCE, D_DEBUT_CONCOURS, CODE_STRUCTURE_ORGANISATRICE, NUM_DEPART, DISCIPLINE, ARME) )";
        $this->_pdo->query($query) or die("Error to CREATE RESULTS");

    }

}

?>