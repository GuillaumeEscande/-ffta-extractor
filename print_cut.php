<?php


echo "<html><head>Print CUT<head>";


##############################
#  OPEN DB
##############################

echo "<br/>";
echo "OPEN DB";

$dbname=dirname(__FILE__)."/base.sqlite";
$base=new SQLite3($dbname) or die('Unable to open database');;


##############################
#  GET CUT SALLE
##############################

	
$query = "SELECT NOM_PERSONNE, PRENOM_PERSONNE, NO_LICENCE, sum(SCORE) AS SCORE_TOTAL 
            FROM CUTS 
            GROUP BY NO_LICENCE ORDER BY SCORE DESC";


$results = $base->query($query) or die('Error to get DATA');

echo("<table border=1 >");
$first_row = true;
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    if ($first_row) {
        $first_row = false;
        // Output header row from keys.
        echo '<tr>';
        foreach($row as $key => $field) {
            echo '<th>' . htmlspecialchars($key) . '</th>';
        }
        echo '</tr>';
    }
    echo '<tr>';
    foreach($row as $key => $field) {
        echo '<td>' . htmlspecialchars($field) . '</td>';
    }
    echo '</tr>';
}
echo("</table>");

?>