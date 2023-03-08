<?php

class Salesforce {
  private $refreshToken = "5Aep861ckn.CqYCOXzgB9sB.hP681jQeQ0zDvyNiAvdqqF9.dly6BtPQBigHuFTYPpwO._ant8RFTwR_I50mx_L";
  private $clientID = "3MVG9PE4xB9wtoY_i29RD53K7wV4iRrkf4NynZeTsKOYruo2RQ0D_6dr12FImAUgvcenouYdkXOQinvRiGm2e";
  private $consumerSecret = "724F096622B73D8049DA29E62D40FEE4DDCA8F026A885D8742C5ADBCD70B1A18";
  private function getAccessToken(){

    $data = "refresh_token=".$this->refreshToken."&grant_type=refresh_token&client_id=".$this->clientID."&client_secret=".$this->consumerSecret;

    $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://test.salesforce.com/services/oauth2/token",
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

      $accessToken = $this->getAccessToken();
;

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
                'Authorization: Bearer '.$accessToken,
                'Content-Type: application/json'
            ),
            ));

            
            $response = curl_exec($curl);
            curl_close($curl);
            $json = json_decode($response, true);

            return $json;
  }
}