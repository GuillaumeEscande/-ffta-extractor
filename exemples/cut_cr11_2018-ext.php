<!DOCTYPE html>
<html>
  <head>
    <title>CUTS cd31</title>
  </head>
  <body>

<?php
// Import de la librairie de gestion des cuts
include_once dirname(__FILE__)."/../cut_manager.php";
$cutManagerFITA = new ffta_extractor\Cut_manager("CR11-2018-FITA.json");
$cutManagerFEDERAL = new ffta_extractor\Cut_manager("CR11-2018-FEDERAL.json");

?>

    <a href="?generate=all">Generer les données</a><br/>
    <a href="?generate=cut">Generer les cuts</a><br/>
    <br/>



    <ul>
<?php
foreach( $cutManager->get_cut_name_list() as $cutname ){
    echo "      <li>";
    echo "<a href=\"./cut_cr11_2018.php?print=".urlencode($cutname)."\">";
    echo $cutname;
    echo "</a>";
    echo "</li>";
}
?>
    </ul>


<?php

if( isset($_GET['generate']) ){
    if( $_GET['generate'] == "all"){
        $cutManagerFITA->generate_datas();
        $cutManagerFEDERAL->generate_datas();
    }
    if( $_GET['generate'] == "cut"){
        $cutManagerFITA->generate_cuts();
        $cutManagerFEDERAL->generate_cuts();
    }
}



if( isset($_GET['print']) ){
    $cutManager->print_cut($_GET['print']);
}

?>


  </body>
</html>