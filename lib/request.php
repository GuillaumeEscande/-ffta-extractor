<?php namespace ffta_extractor;

abstract class Request
{

    protected $_configuration;
    protected $_cookie;
    public function __construct( $configuration )
    {
        $this->_configuration = $configuration;
        $this->_cookie = dirname(__FILE__).'/../data/cookie.txt';
    }

    protected function curl( $url , $data){
        // initialisation de la session
        $curl = curl_init();

        // configuration des options
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt ($curl, CURLOPT_POST, true);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->_cookie);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->_cookie);

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        // exécution de la session login
        $response = curl_exec($curl);

        // fermeture des ressources
        curl_close($curl);

        // Renvois de la réponse
        return $response;
    }

    abstract public function login();
    
    abstract public function logout();

}

class RequestExtranet extends Request
{

    public function login(){

        $data = array(
            "login[identifiant]" => $this->_configuration->get_configuration_ffta("login"),
            "login[idpassword]" => base64_decode($this->_configuration->get_configuration_ffta("password"))
        );
        $this->curl("https://extranet.ffta.fr/", $data);
    }
    
    public function logout(){
        $data = array( );
        $this->curl("https://extranet.ffta.fr/deconnexion.html", $data);
    }

    public function get_document_url(){


        $data = array(
            "search[Saison]" => $this->_configuration->get_configuration_ffta("saison"),
            "search[Discipline]" => "all",
            "search[TypeChampionnat]" => "all",
            "search[Pers]" => $this->_configuration->get_configuration_ffta("type_structure"),
            "search[oldPers]" => $this->_configuration->get_configuration_ffta("type_structure"),
            "search[Struc]" => $this->_configuration->get_configuration_ffta("code_structure"),
            "search[Date_dbt]" => $this->_configuration->get_configuration_ffta("date_debut"),
            "search[Date_fin]" => $this->_configuration->get_configuration_ffta("date_fin"),
            "StartGen" => "Générer+les+documents"
        );

        $reponse = $this->curl("https://extranet.ffta.fr/extractions/eprv-resind.html", $data);

        $regexp = "/https\:\/\/extranet\.ffta\.fr\/tmp\/resultats\/.*?\.csv/m";
        preg_match($regexp, $reponse, $m );
        $file_url = $m[0]; 

        return $file_url;
    }
}   



?>