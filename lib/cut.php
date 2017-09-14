<?php namespace ffta_extractor;

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
        
        $stmt = $this->_bdd->get_PDO()->prepare($query_prepare);
        
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
        
        $this->_bdd->create_table_cut($cut_name);

        $pdo = $this->_bdd->get_PDO();
        $nb_score = $this->_configuration->get_configuration_cut($cut_name, "nb_score");
        $table_name = $this->_bdd->get_table_cut_name($cut_name);

        //----------------------------
        // STEP 1 : Création sous selection
        //----------------------------
        $querySubSelect = "NUM_DEPART='1' ";
        
        // Discipline
        $discipline = $this->_configuration->get_configuration_cut($cut_name, "discipline");
        $querySubSelect .= $this->_bdd->create_and_cond_array($discipline, "DISCIPLINE");

        // Sexe
        $sexe = $this->_configuration->get_configuration_cut($cut_name, "sexe");
        $querySubSelect .= $this->_bdd->create_and_cond_array($sexe, "SEXE_PERSONNE");
        
        // Categorie
        $categorie = $this->_configuration->get_configuration_cut($cut_name, "categorie");
        $querySubSelect .= $this->_bdd->create_and_cond_array($categorie, "CAT");
        
        // Arme
        $arme = $this->_configuration->get_configuration_cut($cut_name, "arme");
        $querySubSelect .= $this->_bdd->create_and_cond_array($arme, "ARME");
        

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
            $score_total = ceil( $score_total / $nb_score);

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

        //----------------------------
        // STEP 4 : Calcul Rank
        //----------------------------
        $query_order_by_score = "SELECT NO_LICENCE
        FROM $table_name  
        ORDER BY SCORE_TOTAL DESC";

        $sth_order_by_score = $pdo->prepare($query_order_by_score);
        $sth_order_by_score->execute();
        
        $result = $sth_order_by_score->fetchAll();

        $sth_update_rank = $pdo->prepare("UPDATE $table_name SET RANK=:RANK WHERE NO_LICENCE=:NO_LICENCE");
        
        $cpt = 1;
        foreach ($result as $archer){

            $sth_update_rank->bindValue(":RANK", $cpt);
            $sth_update_rank->bindValue(":NO_LICENCE", $archer["NO_LICENCE"]);
            $sth_update_rank->execute();

            $cpt ++;
        }
        
        //----------------------------
        // STEP 5 : Gestion égalité
        //----------------------------
<<<<<<< HEAD
        $query_egalite = "SELECT NO_LICENCE AS P1, RANK AS R1, X.NO_LICENCE AS P2, X.RANK AS R1, SCORE_TOTAL  FROM (SELECT * FROM $table_name ORDER BY RANK) AS X WHERE SCORE_TOTAL= X.SCORE_TOTAL AND NO_LICENCE!=X.NO_LICENCE ORDER BY RANK";
=======
        $query_egalite = "SELECT NO_LICENCE AS P1, X.NO_LICENCE AS P2, SCORE_TOTAL  FROM (SELECT * FROM $table_name) AS X WHERE SCORE_TOTAL= X.SCORE_TOTAL AND NO_LICENCE!=X.NO_LICENCE";
>>>>>>> a5853f585ff9702a0b17050d1c9c5550993dc155

        $sth_egalite = $pdo->prepare($query_egalite);
        $sth_egalite->execute();
        
<<<<<<< HEAD
        $sth_get_scores = $pdo->prepare("SELECT SCORE FROM RESULTS WHERE  ".$querySubSelect." AND NO_LICENCE=:NO_LICENCE ORDER BY SCORE DESC");

        $result = $sth_egalite->fetchAll();
        foreach ($result as $archer){
            print ("EGALITE : ". $archer["P1"]." - ". $archer["P2"]." = ". $archer["SCORE_TOTAL"]."</br>");

            // Récupération de l'ensemble des score de l'archer 1 :
            $sth_update_rank->bindValue(":NO_LICENCE", $archer["P1"]);
            $result_score_archer1 = $sth_update_rank->execute();

            // Récupération de l'ensemble des score de l'archer 2 :
            $sth_update_rank->bindValue(":NO_LICENCE", $archer["P2"]);
            $result_score_archer2 = $sth_update_rank->execute();

            $nb_score_max = min( count($result_score_archer1), count($result_score_archer2) );

            $min_rank =  min( $archer["R1"], $archer["R2"] );

            //TODO refaire au ca ou égalité de plusieurs personnes

            if( $nb_score_max < $nb_score)
                break;

            for ($i = $nb_score; $i <= $nb_score_max; $i++) {
                $score_archer_1 = $result_score_archer1[$i]["SCORE"];
                $score_archer_2 = $result_score_archer2[$i]["SCORE"];
                if( $score_archer_1 > $score_archer_2 ){
                    //Ranking OK
                }
            }



=======
        $result = $sth_egalite->fetchAll();
        foreach ($result as $archer){
            print ("EGALITE : ". $archer["NO_LICENCE"]." - ". $archer["NO_LICENCE"]." = ". $archer["SCORE_TOTAL"]."</br>");
>>>>>>> a5853f585ff9702a0b17050d1c9c5550993dc155
        }

    }

    public function fill_all_cuts( ){
        foreach( $this->_configuration->get_configuration_cut_names() as $cut_name ){
            $this->fill_cut( $cut_name );
        }
    }




















    public function print_cut( $cut_name ){
        $pdo = $this->_bdd->get_PDO();

        $table_name = $this->_bdd->get_table_cut_name($cut_name);
        $stmt = $pdo->prepare("SELECT * FROM $table_name ORDER BY RANK ASC");
        $stmt->execute();
        $result = $stmt->fetchAll();
        echo("<table border=1 >\n");
        $first_row = true;
        foreach ($result as $row) {
            if ($first_row) {
                $first_row = false;
                // Output header row from keys.
                echo "<tr>";
                foreach($row as $key => $field) {
                    echo "<th>" . htmlspecialchars($key) . "</th>";
                }
                echo "</tr>\n";
            }
            echo "<tr>";
            foreach($row as $key => $field) {
                echo "<td>" . htmlspecialchars($field) . "</td>";
            }
            echo "</tr>\n";
        }
        echo("</table>\n");

        
    }
}

?>