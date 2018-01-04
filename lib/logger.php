<?php namespace ffta_extractor;

use \PDO;

class Logger
{

    private $_configuration;

    private $_level;
    private $_pdo;
    private $sth_insert;

    public function __construct( $configuration )
    {
        $this->_configuration = $configuration;

        $_level = $this->_configuration->get_configuration_log("level");

        if(!class_exists('SQLite3'))
        die("SQLite 3 NOT supported.");
        
        try{

            $LIB_HOME = dirname(__FILE__).'/..';
            $this->_pdo = new PDO(
                str_replace('$LIB_HOME', $LIB_HOME, $this->_configuration->get_configuration_log("url")),
                $this->_configuration->get_configuration_log("login"),
                $this->_configuration->get_configuration_log("password") );
                $this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->_pdo->query("PRAGMA synchronous = OFF");
                $this->_pdo->query("PRAGMA journal_mode = MEMORY");
        } catch(Exception $e) {
            echo "Impossible d'accéder à la base de données ".$this->_configuration->get_configuration_log("url")." : ".$e->getMessage();
            die();
        }

        $this->create_table_logs();
        $this->sth_insert = $this->_pdo->prepare("INSERT INTO LOGS (IP_USER, LEVEL_MSG, TYPE_MSG, OPERATION) VALUES (:IP_USER, :LEVEL_MSG, :TYPE_MSG, :OPERATION)");
        
    }

    private function create_table_logs(){
        
        $query = "CREATE TABLE IF NOT EXISTS LOGS(
            ID INTEGER PRIMARY KEY AUTOINCREMENT, 
            TIMESTAMP_MSG DATETIME DEFAULT CURRENT_TIMESTAMP,
            IP_USER text NOT NULL,
            LEVEL_MSG int DEFAULT 0,
            TYPE_MSG int NOT NULL,
            OPERATION text NOT NULL )";
        $this->_pdo->query($query) or die("Error to CREATE LOGS");

    }

    public function log_operation( $level, $type, $operation ){
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $this->sth_insert->bindValue(":IP_USER", $ip);
        $this->sth_insert->bindValue(":LEVEL_MSG", $level);
        $this->sth_insert->bindValue(":TYPE_MSG", $type);
        $this->sth_insert->bindValue(":OPERATION", $operation);
        $this->sth_insert->execute();

    }

    public function print_logs( $div=false ){
        $pdo = $this->_pdo;
        $query = $pdo->query("SELECT * FROM LOGS ORDER BY ID DESC");
        $result = $query->fetchAll();

        if( $div ) echo("<div class='cutTable divTable' >\n");
        else echo("<table class='cutTable' >\n");

        $first_row = true;
        foreach ($result as $row) {
            // Affiche le menu de la table
            if ($first_row) {
                $first_row = false;

                // Start Row
                if( $div ) echo("<div class='divTableRow divTableHeading' >\n");
                else echo "<tr class='tableHeading' >\n";


                // ID
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Id";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";

                // TIMESTAMP_MSG
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Date";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";

                // IP_USER
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "IP";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";

                // OPERATION
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Opération";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";


                // End Row
                if( $div ) echo("</div>\n"); //  divTableRow divTableHeading
                else echo "</tr>\n";
                
                if( $div ) echo("<div class='divTableBody' >\n");
            }


            // Start Row
            if( $div ) echo("<div class='divTableRow tableContent ' >\n");
            else echo "<tr class='tableContent  ' >\n";

            // RANK
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo $row['ID'];
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";

            // TIMESTAMP_MSG
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo $row['TIMESTAMP_MSG'];
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";

            // IP_USER
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo $row['IP_USER'];
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";

            // OPERATION
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo $row['OPERATION'];
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";

            // End Row
            if( $div ) echo("</div>\n");// divTableRow
            else echo "</tr>\n";
        }
        
        if( $div ) echo("</div>\n"); //divTableBody
        if( $div ) echo("</div>\n"); //divTable
        else echo("</table>\n");

    } 
} 

?>