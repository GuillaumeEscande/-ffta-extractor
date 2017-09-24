<?php namespace ffta_extractor;

include_once "lib/conf.php";
include_once "lib/request.php";
include_once "lib/bdd.php";
include_once "lib/cut.php";
include_once "lib/printer.php";

class Cut_manager
{
    
    private $_configuration = NULL;
    private $_request = NULL;
    private $_bdd = NULL;
    private $_cut = NULL;
    private $_printer = NULL;

    public function __construct($conf_file_name)
    {
        $this->_configuration = new Configuration( dirname(__FILE__)."/conf/".$conf_file_name );
        $this->_request = new RequestExtranet( $this->_configuration );
        $this->_bdd = new BDD( $this->_configuration );
        $this->_cut = new Cut( $this->_configuration, $this->_bdd );
        $this->_printer = new Printer( $this->_configuration, $this->_bdd );
    }


    public function generate_datas( ){

        echo "<p>\n";
        echo "Login </br>\n";
        $this->_request->login();
        echo "|  OK </br>\n";
        echo "</p>\n";


        echo "<p>\n";
        echo "Récupération de l'url du csv : ";
        $csvUrl = $this->_request->get_document_url();
        echo "$csvUrl </br>\n";

        echo "Création de la table - ";
        $this->_bdd->create_table_results();
        echo "OK </br>\n";

        echo "Création de la table - ";
        $this->_cut->fill_all_results( $csvUrl );
        echo "OK </br>\n";
        echo "|  OK </br>\n";
        echo "</p>\n";

        echo "<p>\n";
        echo "Logout </br>\n";
        $this->_request->logout();
        echo "|  OK </br>\n";
        echo "</p>\n";
        
        echo "<p>\n";
        echo "Calcul des cuts </br>\n";
        $this->_cut->fill_all_cuts( );
        echo "|  OK </br>\n";
        echo "</p>\n";
    }
    
    public function generate_cuts( ){
        $this->_cut->fill_all_cuts( );
    }
    
    
    public function print_cut ($cutName, $div=false, $admin=false, $print_param=""){
        $this->_printer->print_cut( $cutName, $div, $admin, $print_param );
    }

    public function get_cut_name_list (){
        return $this->_configuration->get_configuration_cut_names();
    }

}

?>