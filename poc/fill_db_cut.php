<?php


echo "<html><head>Generate TABLE CUT<head>";


##############################
#  GET CUT SALLE
##############################

$query = "INSERT INTO CUTS
   SELECT NO_LICENCE, NOM_PERSONNE, PRENOM_PERSONNE FROM RESULTS A
    WHERE SCORE IN 
        ( SELECT NO_LICENCE 
            FROM RESULTS B 
            WHERE A.NO_LICENCE = B.NO_LICENCE
                AND DISCIPLINE='S' 
                AND SEXE_PERSONNE='H' 
                AND CAT='S' 
                AND ARME='CL' 
                AND NUM_DEPART='1' 
            GROUP BY NO_LICENCE )";



$results = $base->query($query) or die('Error to generate CUT');

?>