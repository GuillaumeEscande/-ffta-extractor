
<?php
// Import de la librairie de gestion des cuts
include_once dirname(__FILE__)."/../../cut_manager.php";
$cutManagerFITA = new ffta_extractor\Cut_manager("CR11-2018-FITA.json");
$cutManagerFEDERAL = new ffta_extractor\Cut_manager("CR11-2018-FEDERAL.json");

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
          saison 2018
        </td>
      </tr>
    </table>

    <h2>Categorie FITA</h2>
    <form name="select_cut_form" method="post" action="admin.php">
      <p>
        <select name="select_cut_fita">

<?php
foreach( $cutManagerFITA->get_cut_name_list() as $cutname ){
  echo "<option value=\"".urlencode($cutname)."\"";
  if (isset( $_REQUEST['select_cut_fita'] )) {
    if ($cutname == urldecode($_REQUEST['select_cut_fita'])){
      echo " selected ";
    } 
  }  
  echo ">".$cutname."</option>\n";
}
?>
        </select>
      </p>
      <p>
        <input type="submit" name="submit_select_cut_form" value="Lister les archers"></input>
      </p>
    </form> 


    <h2>Categorie Fédéral</h2>
    <form name="select_cut_form" method="post" action="admin.php">
      <p>
        <select name="select_cut_federal">

<?php
foreach( $cutManagerFEDERAL->get_cut_name_list() as $cutname ){
  echo "<option value=\"".urlencode($cutname)."\"";
  if (isset( $_REQUEST['select_cut_federal'] )) {
    if ($cutname == urldecode($_REQUEST['select_cut_federal'])){
      echo " selected ";
    } 
  }  
  echo ">".$cutname."</option>\n";
}
?>
        </select>
      </p>
      <p>
        <input type="submit" name="submit_select_cut_form" value="Lister les archers"></input>
      </p>
    </form> 


    <form name="show_log_journal" method="post" action="admin.php">
        <input type="submit" name="submit_show_log_journal" value="Voir le journal d'évenement"></input>
    </form> 
    <form name="generate_data" method="post" action="admin.php">
        <input type="submit" name="submit_generate_data" value="Mettre à jour la base"></input>
    </form> 

<?php


if( isset($_REQUEST['select_cut_fita']) ){
  $cutManagerFITA->print_cut(urldecode($_REQUEST['select_cut_fita']), true, false, true, false, 'select_cut_fita');
}
if( isset($_REQUEST['select_cut_federal']) ){
  $cutManagerFEDERAL->print_cut(urldecode($_REQUEST['select_cut_federal']), true, false, true, false, 'select_cut_federal');
}

if( isset($_REQUEST['submit_generate_data'])){
    $cutManagerFITA->generate_datas();
    $cutManagerFEDERAL->generate_datas();
}

if( isset($_REQUEST['submit_show_log_journal'])){
    $cutManagerFITA->print_logs(true);
    $cutManagerFEDERAL->print_logs(true);
}

?>

  </body>
</html>
