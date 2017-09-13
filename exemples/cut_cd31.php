<!DOCTYPE html>
<html>
  <head>
    <title>CUTS cd31</title>
  </head>
  <body>

<?php
// Import de la librairie de gestion des cuts
include_once dirname(__FILE__)."/../cut_manager.php";
$cutManager = new ffta_extractor\Cut_manager("cd31.json");

?>

    <a href="./cut_cd31.php?generate=true">Generer les données</a><br/>
    <br/>



    <ul>
<?php
foreach( $cutManager->get_cut_name_list() as $cutname ){
    echo "      <li>";
    echo "<a href=\"./cut_cd31.php?print=".urlencode($cutname)."\">";
    echo $cutname;
    echo "</a>";
    echo "</li>";
}
?>
    </ul>


<?php

if( isset($_GET['generate']) ){
    $cutManager->generate_datas();
}



if( isset($_GET['print']) ){
    $cutManager->print_cut($_GET['print']);
}

?>


  </body>
</html>