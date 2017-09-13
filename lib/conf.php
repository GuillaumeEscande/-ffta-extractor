<?php namespace ffta_extractor;

class Configuration
{
    private $_fichier = NULL;
    private $_config = NULL;

    private $_cuts_names = array();
    private $_cuts = array();

    public function __construct($fichier)
    {
        $this->_fichier = $fichier;
        
        $jsonStr = file_get_contents($fichier);
        $this->_config = json_decode($jsonStr, true);

        foreach ($this->_config["cuts"] as $cut){
            array_push( $this->_cuts_names, $cut["name"] );
            $this->_cuts[ $cut["name"] ] = $cut;
        }

    }

    public function get_configuration_ffta( $data ){
        return $this->_config["ffta"][$data];
    }

    public function get_configuration_bdd( $data ){
        return $this->_config["bdd"][$data];
    }
    
    public function get_configuration_cut_names( ){
        return $this->_cuts_names;
    }

    public function get_configuration_cut( $profile, $data ){
        return $this->_cuts[ $profile ][$data];
    }
}

?>