<?php namespace php_ranking;

class CUT
{

    private $_configuration;
    private $_bdd;

    public function __construct( $configuration, $bdd )
    {
        $this->_configuration = $configuration;
        $this->_bdd = $bdd;
    }

    public function fill_all_results( $csvUrl ){
        
        $query_prepare = "INSERT INTO RESULTS VALUES(:1";
        for($i = 2; $i <= 42; $i++){
            $query_prepare .= ", :".$i;
        }
        $query_prepare .= ")";
        
        $stmt = $this->_bdd->getPDO()->prepare($query_prepare);
        
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
        
        $this->_bdd->createTableCut($cut_name);

        $pdo = $this->_bdd->getPDO();
        $nb_score = $this->_configuration->get_configuration_cut($cut_name, "nb_score");
        $table_name = $this->_bdd->getTableCutName($cut_name);

        //----------------------------
        // STEP 1 : Création sous selection
        //----------------------------
        $querySubSelect = "NUM_DEPART='1' ";
        
        // Discipline
        $discipline = $this->_configuration->get_configuration_cut($cut_name, "discipline");
        $querySubSelect .= $this->_bdd->createAndCondArray($discipline, "DISCIPLINE");

        // Sexe
        $sexe = $this->_configuration->get_configuration_cut($cut_name, "sexe");
        $querySubSelect .= $this->_bdd->createAndCondArray($sexe, "SEXE_PERSONNE");
        
        // Categorie
        $categorie = $this->_configuration->get_configuration_cut($cut_name, "categorie");
        $querySubSelect .= $this->_bdd->createAndCondArray($categorie, "CAT");
        
        // Arme
        $arme = $this->_configuration->get_configuration_cut($cut_name, "arme");
        $querySubSelect .= $this->_bdd->createAndCondArray($arme, "ARME");
        

        //----------------------------
        // STEP 2 : Extraire la liste des archers
        //----------------------------
        $query = "SELECT NO_LICENCE, NOM_PERSONNE, PRENOM_PERSONNE 
        FROM RESULTS  
        WHERE  ".$querySubSelect." GROUP BY NO_LICENCE";

        $sth_archer = $pdo->prepare($query);
        $sth_archer->execute();
        
        $result = $sth_archer->fetchAll();

        $sth_insert = $pdo->prepare("INSERT INTO $table_name (NO_LICENCE, NOM_PERSONNE, PRENOM_PERSONNE, SCORE_TOTAL) VALUES (:NO_LICENCE, :NOM_PERSONNE, :PRENOM_PERSONNE, :SCORE_TOTAL)");

        foreach ($result as $archer){
            //----------------------------
            // STEP 3 : Calcul du score + ajout de l'archer
            //----------------------------

            $query = "SELECT SCORE 
            FROM RESULTS  
            WHERE  ".$querySubSelect." AND NO_LICENCE='".$archer["NO_LICENCE"]."' ORDER BY SCORE DESC";
    
            $sth_score = $pdo->prepare($query);
            $sth_score->execute();
            
            $result = $sth_score->fetchAll();
            $score_total = 0;
            for($i = 0; $i < $nb_score; $i++){
                if( $i < count($result) ){
                    $score_total += $result[$i]["SCORE"];
                }
            }

            // Insert
            $sth_insert->bindValue(":NO_LICENCE", $archer["NO_LICENCE"]);
            $sth_insert->bindValue(":NOM_PERSONNE", $archer["NOM_PERSONNE"]);
            $sth_insert->bindValue(":PRENOM_PERSONNE", $archer["PRENOM_PERSONNE"]);
            $sth_insert->bindValue(":SCORE_TOTAL", $score_total);
            
            try{
                $sth_insert->execute();
            }catch (\PDOException $e){
                echo "FAIL TO ADD ".$archer["NO_LICENCE"]." in ".$cut_name." : ".$e."<br/>\n";
            }
        } 

        // TODO Gerer égalité
    }

    public function fill_all_cuts( ){
        foreach( $this->_configuration->get_configuration_cut_names() as $cut_name ){
            $this->fill_cut( $cut_name );
        }
    }

    public function print_cut( $cut_name ){
        $pdo = $this->_bdd->getPDO();

        $table_name = $this->_bdd->getTableCutName($cut_name);
        $stmt = $pdo->prepare("SELECT * FROM $table_name ORDER BY SCORE_TOTAL DESC");
        $stmt->execute();
        $result = $stmt->fetchAll();
        echo("<table border=1 >\n");
        $first_row = true;
        foreach ($result as $row) {
            if ($first_row) {
                $first_row = false;
                // Output header row from keys.
                echo '<tr>';
                foreach($row as $key => $field) {
                    echo '<th>' . htmlspecialchars($key) . '</th>';
                }
                echo '</tr>\n';
            }
            echo '<tr>';
            foreach($row as $key => $field) {
                echo '<td>' . htmlspecialchars($field) . '</td>';
            }
            echo "</tr>\n";
        }
        echo("</table>\n");
        
    }
}

?>