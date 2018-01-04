<?php
// Import de la librairie de gestion des cuts
include_once dirname(__FILE__)."/../../cut_manager.php";
$cutManager = new ffta_extractor\Cut_manager("CR11-2018.json");
$cutManager->manage_export();

?>

<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="cut.css" />
    <title>Classement | Arc Occitanie</title>
    <meta name="keywords" content="ARC, Occitanie, Comite, Regional, tir, arc, archerie, classique, poulie, compound, competition, federal, FITA, nature, field, campagne, ligue, club">
    <meta name="robots" content="index, follow">
    <meta name="classification" content="Comite Regional Tir Arc Occitanie">
  </head>
  <body>
    <table>
      <tr>
        <td>
          <a href="https://arc-occitanie.fr/">
            <img src="https://arc-occitanie.fr/images/logo/20171013_logo_crtao_all_blacks.jpg" width="150" height="150" border="0" title="Arc Occitanie"  alt="Arc Occitanie">
			</a>
        </td>
        <td>
          <h1>Classement des archers</h1>
          avec les 3 meilleurs scores de la saison 2018
        </td>
      </tr>
    </table>


    <form name="select_cut_form" method="post" action="index.php">
      <p>
        <select name="select_cut">

<?php
foreach( $cutManager->get_cut_name_list() as $cutname ){
  echo "<option value=\"".urlencode($cutname)."\"";
  if (isset( $_REQUEST['select_cut'] )) {
    if ($cutname == urldecode($_REQUEST['select_cut'])){
      echo " selected ";
    } 
  }  
  echo ">".$cutname."</option>\n";
}
?>
        </select>
      </p>
      <p>
        <input type="submit" name="submit_select_cut_form" value="Lister les archers"/>
      </p>
    </form> 

<?php

if( isset($_REQUEST['select_cut']) ){
  $cutManager->print_cut(urldecode($_REQUEST['select_cut']), true, true);
}

?>

  </body>
</html>
