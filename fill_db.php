<?php


##############################
#  LOGIN
##############################

// initialisation de la session
$login = curl_init();

// configuration des options
curl_setopt($login, CURLOPT_URL, "http://extranet.ffta.fr/");

curl_setopt ($login, CURLOPT_POST, true);
curl_setopt ($login, CURLOPT_POSTFIELDS, "login[identifiant]=463693&login[idpassword]=4H216zfd");

$tmpfname = dirname(__FILE__).'/cookie.txt';
curl_setopt($login, CURLOPT_COOKIEJAR, $tmpfname);
curl_setopt($login, CURLOPT_COOKIEFILE, $tmpfname);

curl_setopt($login, CURLOPT_HEADER, false);
curl_setopt($login, CURLOPT_RETURNTRANSFER, true);
curl_setopt($login, CURLOPT_FOLLOWLOCATION, false);

// exécution de la session login
curl_exec($login);

// fermeture des ressources
curl_close($login);


##############################
#  GENERATE DOCUMENT
##############################

// initialisation de la session
$generateDocument = curl_init();

// configuration des options
curl_setopt($generateDocument, CURLOPT_URL, "http://extranet.ffta.fr/extractions/eprv-resind.html");

curl_setopt ($generateDocument, CURLOPT_POST, true);
curl_setopt ($generateDocument, CURLOPT_POSTFIELDS, "search[Saison]=2017&search[Discipline]=all&search[TypeChampionnat]=all&search[Pers]=DEP&search[oldPers]=DEP&search[Struc]=31000&search[Date_dbt]=01/01/2016&search[Date_fin]=31/08/2018&StartGen=Générer+les+documents");

$tmpfname = dirname(__FILE__).'/cookie.txt';
curl_setopt($generateDocument, CURLOPT_COOKIEJAR, $tmpfname);
curl_setopt($generateDocument, CURLOPT_COOKIEFILE, $tmpfname);

curl_setopt($generateDocument, CURLOPT_HEADER, false);
curl_setopt($generateDocument, CURLOPT_RETURNTRANSFER, true);
curl_setopt($generateDocument, CURLOPT_FOLLOWLOCATION, true);

// exécution de la session
$response = curl_exec($generateDocument);

// fermeture des ressources
curl_close($generateDocument);



##############################
#  GET DOCUMENT
##############################
$regexp = "/http\:\/\/extranet\.ffta\.fr\/tmp\/resultats\/.*?\.csv/m";
preg_match($regexp, $response, $m );
$file_url = $m[0]; 

echo "File CSV generated ";

##############################
#  CREATE DB
##############################
$dbname=dirname(__FILE__)."/base.sqlite";
if(!class_exists('SQLite3'))
  die("SQLite 3 NOT supported.");

$base=new SQLite3($dbname);

$mytable = "RESULTS";

$query = "DROP TABLE IF EXISTS $mytable";
$base->exec($query);

$query = "CREATE TABLE $mytable(
    SAISON int NOT NULL,
    DISCIPLINE text NOT NULL,
    NO_LICENCE text NOT NULL,
    NOM_PERSONNE text NOT NULL,
    PRENOM_PERSONNE text NOT NULL,
    HORS_F text,
    SEXE_PERSONNE text NOT NULL,
    CAT text NOT NULL,
    CAT_S text NOT NULL,
    CODE_STRUCTURE text NOT NULL,
    NOM_STRUCTURE text NOT NULL,
    ARME text NOT NULL,
    NIVEAU text,
    SCORE int NOT NULL,
    PAILLE int NOT NULL,
    DIX int NOT NULL,
    NEUF int NOT NULL,
    DISTANCE int NOT NULL,
    BLASON int NOT NULL,
    D_DEBUT_CONCOURS text NOT NULL,
    D_FIN_CONCOURS text NOT NULL,
    LIEU_CONCOURS text NOT NULL,
    CODE_STRUCTURE_ORGANISATRICE text NOT NULL,
    NOM_STRUCTURE_ORGANISATRICE text NOT NULL,
    FORMULE_TIR text NOT NULL,
    NIVEAU_CHPT text,
    DETAIL_NIVEAU_CHPT text,
    DISTINCTION text,
    PLACE_QUALIF int NOT NULL,
    SCORE_DIST1 int NOT NULL,
    SCORE_DIST2 int NOT NULL,
    SCORE_DIST3 int NOT NULL,
    SCORE_DIST4 int NOT NULL,
    SCORE_32 int,
    SCORE_16 int,
    SCORE_8 int,
    SCORE_QUART int,
    SCORE_DEMI int,
    SCORE_PETITE_FINAL int,
    SCORE_FINAL int,
    PLACE_DEF int NOT NULL,
    NUM_DEPART int NOT NULL
    )";
    
$base->exec($query);

echo "Database created";

##############################
#  FILL DB
##############################

$file = fopen ($file_url, "r");

while (!feof ($file)) {
    $line = fgets ($file);
	
	$values = explode(";", $line);
    
	$query = "INSERT INTO $mytable VALUES( ";
	foreach( $values as $val ){
		$query .= '"';
		$val_ok = str_replace("\"", "", $val);
		$query .= $val_ok;
		$query .= '"';
		$query .= ',';
	}
	$query = rtrim($query, ',');
	$query .= " )";
	$base->exec($query);
	
	echo "Data Added : ".$query;
	
}
fclose($file);


##############################
#  LOGOUT
##############################

// initialisation de la session
$logout = curl_init();

// configuration des options
curl_setopt($logout, CURLOPT_URL, "http://extranet.ffta.fr/deconnexion.html");

$tmpfname = dirname(__FILE__).'/cookie.txt';
curl_setopt($logout, CURLOPT_COOKIEJAR, $tmpfname);
curl_setopt($logout, CURLOPT_COOKIEFILE, $tmpfname);

curl_setopt($logout, CURLOPT_HEADER, false);
curl_setopt($logout, CURLOPT_RETURNTRANSFER, true);
curl_setopt($logout, CURLOPT_FOLLOWLOCATION, false);

// exécution de la session login
curl_exec($logout);

// fermeture des ressources
curl_close($logout);

?>