<?php

class Pardot {
  private $env;
  private $accessToken;
  private Array $config = array(
    "instance_url" => array(
      "PRODUCTION" => "https://pi.pardot.com",
      "STAGING" => "https://pi.demo.pardot.com"
    ),
    "business_unit_id" => array(
      "PRODUCTION"=> "0Uv2E000000KyknSAC",
      "STAGING" => "0Uv2E000000KyknSAC" //TODO: NOT DEFINED YET
    )
  );
  private Array $leadVSProspect = array(
    "CurrencyIsoCode" => "Currency_FML__c",
    "LeadSource" => "source",
    "Role__c" => "Role__c",
    "Phone" => "phone",
    "Email" => "email",
    "LastName" => "lastName",
    "Country" => "country",
    "Upload_Source__c" => "Upload_Source__c",
    "Notes__c" => "notes",
    "Agreed_to_Terms_and_Conditions__c" => "Agreed_to_Terms_and_Conditions__c",
    "Mobile_Opt_In__c" => "Mobile_Opt_In__c",
    "Previous_Page_URL__c" => "Previous_Page_URL__c",
    "MobilePhone" => "Mobile_Phone__c",
    "Lead_Source_Sub__c" => "Lead_Source_Sub__c",
    "Company" => "company",
    "Zip" => "zip",
    "ft_utm_source__c" => "ft_utm_source__c",
    "ft_utm_medium__c" => "ft_utm_medium__c",
    "ft_utm_campaign__c" => "ft_utm_campaign__c",
    "ft_utm_term__c" => "ft_utm_term__c",
    "ft_utm_content__c" => "ft_utm_content__c",
    "ft_utm_adgroup__c" => "ft_utm_adgroup__c",
    "UTM_campaign_source__c" => "utm_source",
    "UTM_campaign_medium__c" => "utm_medium",
    "UTM_campaign_name__c" => "utm_campaign",
    "UTM_campaign_term__c" => "utm_term",
    "UTM_campaign_content__c" => "utm_content",
    "UTM_campaign_adgroup__c" => "utm_adgroup",
    "lt_utm_source__c" => "lt_utm_source__c",
    "lt_utm_medium__c" => "lt_utm_medium__c",
    "lt_utm_campaign__c" => "lt_utm_campaign__c",
    "lt_utm_term__c" => "lt_utm_term__c",
    "lt_utm_content__c" => "lt_utm_content__c",
    "lt_utm_adgroup__c" => "lt_utm_adgroup__c",
  );

  function  __construct( String $env, String $accessToken){
    $this->env = $env;
    $this->accessToken = $accessToken;
  }

  public function createProspect(Array $body){

    $body["prospect"] = $this->mapProspect($body["prospect"]);

    $service = "/api/v5/objects/prospects/do/upsertLatestByEmail";

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
            CURLOPT_POSTFIELDS => json_encode($body, JSON_FORCE_OBJECT),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->accessToken,
                "Pardot-Business-Unit-Id: ". $this->config["business_unit_id"][$this->env],
                'Content-Type: application/json'
            ),
            ));

    $response = curl_exec($curl);
    curl_close($curl);

    $json = json_decode($response, true);
          

    return $this->sendResponse($json);
  }

  private function mapProspect(Array $lead){
    //GET ARRAY OF KEYS
    $keys = array_keys($lead);
    //INIT ARRAY
    $prospect = array();

    //FILL IN ARRAY PROSPECT
    for($i= 0; $i<count($keys) ; $i++){
      if(isset($lead[$keys[$i]]) && isset($this->leadVSProspect[$keys[$i]])){
      $prospect[$this->leadVSProspect[$keys[$i]]] = $lead[$keys[$i]]; 
      }
    }

    return $prospect;

  }

  private function sendResponse(Array $pardotResponse){
    if(isset($pardotResponse["email"])) {
      return array(
        "status" => "success",
        "app"=> "Pardot",
        "email"=> $pardotResponse["email"],
        "prospectID" => $pardotResponse["id"]
      );
    }
    return $this->errorHandler($pardotResponse);
  }

  private function errorHandler(Array $pardotResponse){
    return array(
            "status"=>"fail", 
            "app" => "Pardot",
            "code" => $pardotResponse["code"],
            "message" => $pardotResponse["message"]
          );
  }

}