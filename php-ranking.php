<?php namespace php_ranking;

include_once "lib/conf.php";
include_once "lib/request.php";
include_once "lib/bdd.php";
include_once "lib/cut.php";

class PHP_Ranking
{
    
    private $_configuration = NULL;
    private $_request = NULL;
    private $_bdd = NULL;
    private $_cut = NULL;

    public function __construct($conf_file_name)
    {
        $this->_configuration = new Configuration( dirname(__FILE__)."/conf/".$conf_file_name );
        $this->_request = new Request( $this->_configuration );
        $this->_bdd = new BDD( $this->_configuration );
        $this->_cut = new Cut( $this->_configuration, $this->_bdd );
    }

    public function init_results(){
        $this->_request->login();
        $csvUrl = $this->_request->getDocumentUrl();
        $this->_bdd->createTableResults();
        $this->_cut->fill_all_results( $csvUrl );
        $this->_request->logout();
    }

    public function generate_datas( ){
        $this->init_results();
        $this->_cut->fill_all_cuts( );
    }

    public function test( ){
        $this->generate_datas( );
        $this->_cut->print_cut( "cd31_cl_scratch_salle_homme" );
    }
}

$ranking = new PHP_Ranking("cd31.json");

$ranking->test( );


?>