<?php namespace ffta_extractor;

class Printer
{

    private $_configuration;
    private $_bdd;
    private $_export;

    public function __construct( $configuration, $bdd, $export )
    {
        $this->_configuration = $configuration;
        $this->_bdd = $bdd;
        $this->_export = $export;
    }

    public function print_cut( $cut_name, $div=false, $export=true, $admin=false, $print_param="" ){

        // Update status archer
        if( $admin ){
            $this->update_status_admin();
        } else {
            $this->update_status();
        }

        // Récupération des données
        $pdo = $this->_bdd->get_PDO();
        $table_name = $this->_bdd->get_table_cut_name($cut_name);
        $stmt = $pdo->prepare("SELECT * FROM $table_name ORDER BY RANK ASC");
        $stmt->execute();
        $result = $stmt->fetchAll();

        $taille_cut = $this->_configuration->get_configuration_cut($cut_name, "taille");

        $cpt_participants = 0;


        // Affichage des information :
        echo("Taille du cut : ".$taille_cut."\n");
        echo("</br>\n");
        echo("Nombre d'archers : ".count($result)."\n");
        echo("</br>\n");

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

                // RANK
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Rang";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";

                // NO_LICENCE
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Licence";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";
                
                // NOM_PERSONNE
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Nom";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";
                
                // PRENOM_PERSONNE
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Prénom";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";
                
                // CLUB
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Club";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";
                
                // Score 1
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Score 1";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";
                
                // Score 2
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Score 2";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";
                
                // Score 3
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Score 3";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";
                
                // SCORE_TOTAL
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Score Total";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";
                
                // ETAT
                if( $div ) echo("<div class='divTableCell' >");
                else echo "<td>";
                echo "Etat Inscription";
                if( $div ) echo("</div>\n");// divTableCell
                else echo "</td>\n";

                if( $admin ){
                    // UPDATE
                    if( $div ) echo("<div class='divTableCell' >");
                    else echo "<td>";
                    echo "Mise à jour";
                    if( $div ) echo("</div>\n");// divTableCell
                    else echo "</td>\n";
                } else {
                    // UPDATE
                    if( $div ) echo("<div class='divTableCell' >");
                    else echo "<td>";
                    echo "Pré inscription";
                    if( $div ) echo("</div>\n");// divTableCell
                    else echo "</td>\n";
                }


                // End Row
                if( $div ) echo("</div>\n"); //  divTableRow divTableHeading
                else echo "</tr>\n";
                
                if( $div ) echo("<div class='divTableBody' >\n");
            }
                
            if( $row['ETAT'] == 1)
                $classe_status = "cutPreInscrit";
            elseif( $row['ETAT'] == 2)
                $classe_status = "cutInscrit";
            elseif( $row['ETAT'] == 3 )
                $classe_status = "cutRefu";
            else {
                if ( $cpt_participants < $taille_cut )
                    $classe_status = "cutPotentiel";
                else 
                    $classe_status = "cutHorsCut";
            }

            // Start Row
            if( $div ) echo("<div class='divTableRow tableContent $classe_status' >\n");
            else echo "<tr class='tableContent  $classe_status' >\n";

            // RANK
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo Printer::row_to_string('RANK', $row['RANK']);
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";

            // NO_LICENCE
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo Printer::row_to_string('NO_LICENCE', $row['NO_LICENCE']);
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";
            
            // NOM_PERSONNE
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo Printer::row_to_string('NOM_PERSONNE', $row['NOM_PERSONNE']);
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";
            
            // PRENOM_PERSONNE
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo Printer::row_to_string('PRENOM_PERSONNE', $row['PRENOM_PERSONNE']);
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";
            
            // CLUB
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo Printer::row_to_string('CLUB', $row['CLUB']);
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";
            
            $score = explode ( ",", $row['SCORES']);
            // Score 1
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo $score[0];
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";
            
            // Score 2
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo $score[1];
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";
            
            // Score 3
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo $score[2];
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";
            
            // SCORE_TOTAL
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo Printer::row_to_string('SCORE_TOTAL', $row['SCORE_TOTAL']);
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";
            
            // ETAT
            if( $div ) echo("<div class='divTableCell' >");
            else echo "<td>";
            echo Printer::row_to_string('ETAT', $row['ETAT']);
            if( $div ) echo("</div>\n");// divTableCell
            else echo "</td>\n";

            
            if( $row['ETAT'] != 2 ){
                $cpt_participants++;
            }      

        
            if( $admin ){
                // Start Column
                if( $div ) echo("<div class='divTableHead' >");
                else echo "<th>";

                // Column
                echo "<form name='change_mode' method='post' >";
                echo "<select name='select_mode_archer'>";

                echo "<option value=0 ";
                if( $row['ETAT'] == 0 ) echo "selected='selected' ";
                echo ">Non Inscrit</option>";
                
                echo "<option value=1 ";
                if( $row['ETAT'] == 1 ) echo "selected='selected' ";
                echo ">Pré Inscrit</option>";

                echo "<option value=2 ";
                if( $row['ETAT'] == 2 ) echo "selected='selected' ";
                echo ">Inscrit</option>";

                echo "<option value=3 ";
                if( $row['ETAT'] == 3 ) echo "selected='selected' ";
                echo ">Refus</option>";

                echo "</select>";
                echo "<input type='hidden' name='id' value='".$row['NO_LICENCE']."' >";
                echo "<input type='hidden' name='select_cut' value='".urlencode($cut_name)."' >";
                echo "<input type='hidden' name='".$print_param."' value='".urlencode($cut_name)."' >";
                echo "<input type='submit' value='Valider'/>";
                echo "</form>";

                // End Column
                if( $div ) echo("</div>\n"); // divTableHead
                else echo "</th>\n";
            } else {
                // Start Column
                if( $div ) echo("<div class='divTableHead' >");
                else echo "<th>";

                if( $row['ETAT'] == 0 ){
                    echo "<form name='preinscrire' method='post' >";
                    echo "<input type='hidden' name='preinscrire' value='true' >";
                    echo "<input type='hidden' name='id' value='".$row['NO_LICENCE']."' >";
                    echo "<input type='hidden' name='select_cut' value='".urlencode($cut_name)."' >";
                    echo "<input type='hidden' name='".$print_param."' value='".urlencode($cut_name)."' >";
                    echo "<input type='submit' value='Pré inscrire'/>";
                    echo "</form>";
                }

                // End Column
                if( $div ) echo("</div>\n"); // divTableHead
                else echo "</th>\n";
            }

            // End Row
            if( $div ) echo("</div>\n");// divTableRow
            else echo "</tr>\n";

        }
        
        if( $div ) echo("</div>\n"); //divTableBody
        if( $div ) echo("</div>\n"); //divTable
        else echo("</table>\n");

        if($export){
            $this->_export->print_export_icons( $cut_name );
        }
    }

    public function update_status( ){
        if( isset($_REQUEST['preinscrire'])){
            $id = $_REQUEST['id'];
            $cut_name = urldecode($_REQUEST['select_cut']);

            $pdo = $this->_bdd->get_PDO();
            $table_name = $this->_bdd->get_table_cut_name($cut_name);

            $sth_update = $pdo->prepare("UPDATE $table_name SET ETAT=:ETAT WHERE NO_LICENCE=:NO_LICENCE");
            
            $sth_update->bindValue(":NO_LICENCE", $id);
            $sth_update->bindValue(":ETAT", "1");

            try{
                $sth_update->execute();
            }catch (\PDOException $e){
                echo "Echec de l'a mise a jour du nouvel état de ".$archer["NO_LICENCE"]." dans ".$cut_name." : ".$e."<br/>\n";
            }
        }
    }
    
    public function update_status_admin( ){
        if( isset($_REQUEST['select_mode_archer'])){
            $id = $_REQUEST['id'];
            $cut_name = urldecode($_REQUEST['select_cut']);
            $mode = $_REQUEST['select_mode_archer'];

            $pdo = $this->_bdd->get_PDO();
            $table_name = $this->_bdd->get_table_cut_name($cut_name);

            $sth_update = $pdo->prepare("UPDATE $table_name SET ETAT=:ETAT WHERE NO_LICENCE=:NO_LICENCE");
            
            $sth_update->bindValue(":NO_LICENCE", $id);
            $sth_update->bindValue(":ETAT", $mode);

            try{
                $sth_update->execute();
            }catch (\PDOException $e){
                echo "Echec de l'a mise a jour du nouvel état de ".$archer["NO_LICENCE"]." dans ".$cut_name." : ".$e."<br/>\n";
            }
        }
    }
    
    public static function etat_to_string( $valeur ){
        switch($valeur){
            case 0:
                return "";
            case 1:
                return "PRE-INSCRIT";
            case 2:
                return "INSCRIT";
            case 3:
                return "REFU";
        }
    }

    public static function row_to_string( $cles, $valeur ){
        if( $cles == "SCORE_TOTAL" ){
            return  strval( ceil( $valeur ) );
        } elseif ( $cles == "ETAT" ){
            return Printer::etat_to_string($valeur);
        } else {
            return htmlspecialchars($valeur);
        } 
    }
}

?>
