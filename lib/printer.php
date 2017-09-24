<?php namespace ffta_extractor;

class Printer
{

    private $_configuration;
    private $_bdd;

    public function __construct( $configuration, $bdd )
    {
        $this->_configuration = $configuration;
        $this->_bdd = $bdd;
    }

    public function print_cut( $cut_name, $div=false, $admin=false ){

        // Récupération des données
        $pdo = $this->_bdd->get_PDO();
        $table_name = $this->_bdd->get_table_cut_name($cut_name);
        $stmt = $pdo->prepare("SELECT * FROM $table_name ORDER BY RANK ASC");
        $stmt->execute();
        $result = $stmt->fetchAll();

        $nb_score_cut = $this->_configuration->get_configuration_cut($cut_name, "nb_score");

        $cpt_participants = 0;

        // Affichage de la table

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

                // Row
                foreach($row as $key => $field) {
                    // Start Column
                    if( $div ) echo("<div class='divTableHead' >");
                    else echo "<th>";

                    // Column
                    echo htmlspecialchars($key);

                    // End Column
                    if( $div ) echo("</div>\n"); // divTableHead
                    else echo "</th>\n";
                }

                // End Row
                if( $div ) echo("</div>\n"); //  divTableRow divTableHeading
                else echo "</tr>\n";
                
                if( $div ) echo("<div class='divTableBody' >\n");
            }
                
            if( $row['ETAT'] == 1 )
            $classe_status = "cutInscrit";
            elseif( $row['ETAT'] == 2 )
                $classe_status = "cutRefu";
            else {
                if ( $cpt_participants < $nb_score_cut )
                    $classe_status = "cutPotentiel";
                else 
                    $classe_status = "cutHorsCut";
            }

            // Start Row
            if( $div ) echo("<div class='divTableRow tableContent $classe_status' >\n");
            else echo "<tr class='tableContent  $classe_status' >\n";

            // Row
            foreach($row as $key => $field) {

                // Start Column
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";

                // Column
                if( $key == "SCORE_TOTAL" ){

                    echo strval( ceil( $field ) );

                } elseif ( $key == "ETAT" ){
                    
                    switch($field){
                        case 0:
                            echo "";
                            $cpt_participants++;
                        break;
                        case 1:
                            echo "INSCRIT";
                            $cpt_participants++;
                        break;
                        case 2:
                            echo "REFU";
                        break;
                    }

                } else {
                    echo htmlspecialchars($field);
                }             

                // End Column
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";
            }

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
