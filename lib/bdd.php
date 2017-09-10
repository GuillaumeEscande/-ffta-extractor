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

    public function getTableCutName($cut_name){
        return "CUT_".$cut_name;
    }

    public function createTableCut($cut_name){

        $table_name = $this->getTableCutName($cut_name);

        $this->_pdo->query("DROP TABLE IF EXISTS $table_name") or die("Error to DROP $table_name");

        $query = "CREATE TABLE $table_name(
            RANK int DEFAULT 0,
            NO_LICENCE text NOT NULL PRIMARY KEY,
            NOM_PERSONNE text NOT NULL,
            PRENOM_PERSONNE text NOT NULL,  
            SCORE_TOTAL int NOT NULL,
            ETAT int DEFAULT 0
        )";

        $this->_pdo->query($query) or die("Error to CREATE $table_name");
    
    }

    public function generateTableCut(){
        $query = "INSERT INTO CUTS
        SELECT NO_LICENCE, NOM_PERSONNE, PRENOM_PERSONNE, SEXE_PERSONNE, CAT, ARME, NIVEAU, SCORE FROM RESULTS A
            WHERE SCORE IN 
                ( SELECT SCORE 
                    FROM RESULTS B 
                    WHERE A.NO_LICENCE = B.NO_LICENCE
                        AND DISCIPLINE='S' 
                        AND SEXE_PERSONNE='H' 
                        AND CAT='S' 
                        AND ARME='CL' 
                        AND NUM_DEPART='1' 
                    ORDER BY SCORE DESC limit 2 )";



        $results = $base->query($query) or die('Error to generate CUT');
    }

    public function createAndCondArray($paramValue, $columnName){
        $array_data = array();
        if ( is_array( $paramValue) )
            $array_data = array_merge($array_data, $paramValue);
        else
            array_push($array_data, $paramValue);

        $query = " AND (";
        if( in_array("*", $array_data) ){
            return "";
        } else {
            $first = true;
            foreach( $array_data as $data ){
                if ( ! $first ){
                    $query .= " OR";
                } else {
                    $first = false;
                }
                
                $query .= " ".$columnName."='".$data."' ";
                
            }
        }
        $query .= " ) ";

        return $query;
    }

}

?>