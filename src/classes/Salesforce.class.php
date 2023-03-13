<?php
require("../classes/Pardot.class.php");

class Salesforce {
  private String $env;
  private String $refreshToken;
  private String $clientID;
  private String $consumerSecret;
  public Pardot $Pardot;
  private String $accessToken;
  private Array $config = array(
    "instance_url"=> array(
        "PRODUCTION"=> "https://otr-hub.my.salesforce.com",
        "STAGING" => "https://otr-hub--fullsbuat.sandbox.my.salesforce.com"
    ),
    "login_url" => array(
        "PRODUCTION"=> "https://login.salesforce.com",
        "STAGING" => "https://test.salesforce.com"
    )
  );
  

function __construct(String $env){
    $this->env = strtoupper($env);
    $this->refreshToken = $_ENV["SALESFORCE_REFRESHTOKEN_".$this->env];
    $this->clientID = $_ENV["SALESFORCE_CLIENTID_".$this->env];
    $this->consumerSecret = $_ENV["SALESFORCE_SECRET_".$this->env];
    $this->accessToken = $this->getAccessToken();
    $this->Pardot = new Pardot($this->env, $this->accessToken);
  }
 
  public function getAccessToken(){

    $service = "/services/oauth2/token";

    $data = "refresh_token=".$this->refreshToken."&grant_type=refresh_token&client_id=".$this->clientID."&client_secret=".$this->consumerSecret;

    $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config["instance_url"][$this->env] . $service,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            ));

        $response = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($response, true);

        $accessToken = $json["access_token"];

        return $accessToken;
  }

  public function createLead($lead){
      $curl = curl_init();


            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://otr-hub--fullsbuat.sandbox.my.salesforce.com"."/services/data/v53.0/sobjects/Lead/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($lead, JSON_FORCE_OBJECT),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->accessToken,
                'Content-Type: application/json'
            ),
            ));

            
            $response = curl_exec($curl);
            curl_close($curl);

            $json = json_decode($response, true);

            return $json;
  }

}