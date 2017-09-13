<?php namespace ffta_extractor;

include_once "lib/conf.php";
include_once "lib/request.php";
include_once "lib/bdd.php";
include_once "lib/cut.php";

class Cut_manager
{
    
    private $_configuration = NULL;
    private $_request = NULL;
    private $_bdd = NULL;
    private $_cut = NULL;

    public function __construct($conf_file_name)
    {
        $this->_configuration = new Configuration( dirname(__FILE__)."/conf/".$conf_file_name );
        $this->_request = new RequestExtranet( $this->_configuration );
        $this->_bdd = new BDD( $this->_configuration );
        $this->_cut = new Cut( $this->_configuration, $this->_bdd );
    }


    public function generate_datas( ){

        $this->_request->login();

        $csvUrl = $this->_request->get_document_url();
        $this->_bdd->create_table_results();
        $this->_cut->fill_all_results( $csvUrl );

        $this->_request->logout();
        
        $this->_cut->fill_all_cuts( );
    }
    
    public function print_cut ($cutName){
        $this->_cut->print_cut( $cutName );
    }

    public function get_cut_name_list (){
        return $this->_configuration->get_configuration_cut_names();
    }

}

?>