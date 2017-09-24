<?php namespace ffta_extractor;

include_once "printer.php";

class Export
{

    private $_configuration;
    private $_bdd;

    public function __construct( $configuration, $bdd )
    {
        $this->_configuration = $configuration;
        $this->_bdd = $bdd;
    }
    
    public function print_export_icons ( $cut_name ){
        echo "<a href='?export=csv&cut_name=".urlencode($cut_name)."' target='_blank'><img alt='csv' src='https://image.freepik.com/free-icon/csv-file-format-extension_318-45083.jpg' width='40' height='40'></a>"; 
    }

    public function manage_export (){
        if( isset($_REQUEST['export'])){
            $cut_name = urldecode($_REQUEST['cut_name']);
            $export_mode = $_REQUEST['export'];

            if( $export_mode == "csv" ){
                $this->export_csv($cut_name);
            }
        }
    }
    
    public function export_csv ( $cut_name ){
        header('Content-type: text/csv');

        $pdo = $this->_bdd->get_PDO();
        $table_name = $this->_bdd->get_table_cut_name($cut_name);

        header("Content-disposition: attachment;filename=\"$table_name.csv\""); 

        $stmt = $pdo->prepare("SELECT * FROM $table_name ORDER BY RANK ASC");
        $stmt->execute();
        $result = $stmt->fetchAll();


        echo("RANK;NO_LICENCE;NOM_PERSONNE;PRENOM_PERSONNE;SCORE_TOTAL;ETAT\n");
        
        foreach ($result as $row) {
            
            $first_column = true;
            foreach($row as $key => $field) {
                
                if ($first_column) {
                    $first_column = false;
                } else {
                    echo ";";
                }
                echo Printer::row_to_string($key, $field);
            }
            echo "\n";
        }
        exit;
    }
}

?>
