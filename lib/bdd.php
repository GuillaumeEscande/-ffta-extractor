<?php namespace ffta_extractor;

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

            $LIB_HOME = dirname(__FILE__).'/..';
            $this->_pdo = new PDO(
                str_replace('$LIB_HOME', $LIB_HOME, $this->_configuration->get_configuration_bdd("url")),
                $this->_configuration->get_configuration_bdd("login"),
                $this->_configuration->get_configuration_bdd("password") );
                $this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->_pdo->query("PRAGMA synchronous = OFF");
                $this->_pdo->query("PRAGMA journal_mode = MEMORY");
        } catch(Exception $e) {
            echo "Impossible d'accéder à la base de données ".$this->_configuration->get_configuration_bdd("url")." : ".$e->getMessage();
            die();
        }
    }

    public function get_PDO(){
        return $this->_pdo;
    }

    public function create_table_results(){

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

    public function get_table_cut_name($cut_name){
        $cut_name="CUT_".$cut_name;
        $cut_name=str_replace(' ', '_', $cut_name);
        $cut_name=str_replace('-', '_', $cut_name);
        return $cut_name;
    }

    public function create_table_cut($cut_name, $replace=true){

        $table_name = $this->get_table_cut_name($cut_name);

        if($replace){
            $this->_pdo->query("DROP TABLE IF EXISTS $table_name") or die("Error to DROP $table_name");
        }

        $query = "CREATE TABLE IF NOT EXISTS $table_name(
            RANK int DEFAULT 0,
            NO_LICENCE text NOT NULL PRIMARY KEY,
            NOM_PERSONNE text NOT NULL,
            PRENOM_PERSONNE text NOT NULL,  
            CLUB text NOT NULL, 
            SCORES text NOT NULL,
            SCORE_TOTAL int NOT NULL,
            ETAT int DEFAULT 0
        )";

        $this->_pdo->query($query) or die("Error to CREATE $table_name");
    
    }

    public function generate_table_cut(){
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

    public function create_and_cond_array($paramValue, $columnName){
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