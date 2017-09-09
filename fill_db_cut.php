<?php


echo "<html><head>Generate TABLE CUT<head>";


##############################
#  OPEN DB
##############################

echo "<br/>";
echo "OPEN DB";

$dbname=dirname(__FILE__)."/base.sqlite";
$base=new SQLite3($dbname) or die('Unable to open database');

$query = "DROP TABLE IF EXISTS CUTS";
$base->exec($query) or die("Error to DROP CUTS");

$query = "CREATE TABLE CUTS(
    NO_LICENCE text NOT NULL,
    NOM_PERSONNE text NOT NULL,
    PRENOM_PERSONNE text NOT NULL,
    SEXE_PERSONNE text NOT NULL,
    CAT text NOT NULL,
    ARME text NOT NULL,
    NIVEAU text,
    SCORE int NOT NULL
    )";
    
$base->exec($query) or die("Error to CREATE CUTS");

##############################
#  GET CUT SALLE
##############################

$query = "INSERT INTO CUTS
   SELECT NO_LICENCE, NOM_PERSONNE, PRENOM_PERSONNE, SEXE_PERSONNE, CAT, ARME, NIVEAU, SCORE FROM RESULTS A
    WHERE SCORE IN 
        ( SELECT SCORE 
            FROM RESULTS B 
            WHERE A.NO_LICENCE = B.NO_LICENCE
                AND DISCIPLINE='S' 
                AND SEXE_PERSONNE='H' 
                AND CAT='S' 
                AND ARME='CL' 
            ORDER BY SCORE DESC limit 2 )";



$results = $base->query($query) or die('Error to generate CUT');

?>