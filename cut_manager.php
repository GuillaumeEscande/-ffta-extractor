<?php namespace ffta_extractor;

include_once "lib/conf.php";
include_once "lib/request.php";
include_once "lib/bdd.php";
include_once "lib/cut.php";
include_once "lib/printer.php";
include_once "lib/export.php";
include_once "lib/logger.php";

class Cut_manager
{
    
    private $_configuration = NULL;
    private $_logger = NULL;
    private $_request = NULL;
    private $_bdd = NULL;
    private $_cut = NULL;
    private $_printer = NULL;
    private $_export = NULL;

    public function __construct($conf_file_name)
    {
        $this->_configuration = new Configuration( dirname(__FILE__)."/conf/".$conf_file_name );

        $this->_logger = new Logger( $this->_configuration );
        $this->_request = new RequestExtranet( $this->_configuration );
        $this->_bdd = new BDD( $this->_configuration );
        $this->_cut = new Cut( $this->_configuration, $this->_bdd );
        $this->_export = new Export( $this->_configuration, $this->_bdd );
        $this->_printer = new Printer( $this->_configuration, $this->_bdd, $this->_export, $this->_logger, $this->_request );
        
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

        $this->_logger->log_operation(0, 2, "Admin : Mise à jour de la base des scores avec le fichier $csvUrl");

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
    
    
    public function print_cut ($cutName, $div=false, $export=true, $admin=false, $print_param=""){
        $this->_printer->print_cut( $cutName, $div, $export, $admin, $print_param );
    }

    public function get_cut_name_list (){
        return $this->_configuration->get_configuration_cut_names();
    }

    public function manage_export (){
        return $this->_export->manage_export();
    }

    public function print_logs ($div=false){
        return $this->_logger->print_logs($div);
    }
    

}

?>