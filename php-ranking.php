<?php namespace php_ranking;

include_once "lib/conf.php";
include_once "lib/request.php";
include_once "lib/bdd.php";

class PHP_Ranking
{
    
    private $config = NULL;
    private $request = NULL;
    private $bdd = NULL;

    public function __construct($conf_file_name)
    {
        $this->config = new Configuration( dirname(__FILE__)."/conf/".$conf_file_name );
        $this->request = new Request( $this->config );
        $this->bdd = new BDD( $this->config );
    }

    private function fill_all_results( $csvUrl ){

        $query_prepare = "INSERT INTO RESULTS VALUES(:1";
        for($i = 2; $i <= 42; $i++){
            $query_prepare .= ", :".$i;
        }
        $query_prepare .= ")";
        
        $stmt = $this->bdd->getPDO()->prepare($query_prepare);
        
        $file = fopen ($csvUrl, "r");
        
        while (!feof ($file)) {
            $line = fgets ($file);
            
            $values = explode(";", $line);
            
            $cpt = 1;
            foreach( $values as $val ){ 
                $stmt->bindValue($cpt, iconv('ISO-8859-1//TRANSLIT','ASCII//TRANSLIT', str_replace("\"", "", $val)));
                $cpt++;
            }
        
            try{
                $result = $stmt->execute();
            }catch (\PDOException $e){
                echo "FAIL TO ADD ".$line."<br/>\n";
            }
        }
        fclose($file);
    }


    private function fill_cut( $cut_name ){
        
        
    }

    private function fill_all_cuts( ){
        foreach( $this->_configuration->get_configuration_cut_names() as $cut_name ){
            $this->fill_cut( $cut_name );
        }
    }


    public function init_results(){
        $this->request->login();
        $csvUrl = $this->request->getDocumentUrl();
        $this->bdd->createTableResults();
        $this->fill_all_results( $csvUrl );
        $this->request->logout();
    }

    public function test( ){
        $this->fill_all_cuts( );
    }
}

$ranking = new PHP_Ranking("cd31.json");

$ranking->test( );


?>