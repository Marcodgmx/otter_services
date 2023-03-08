<?php
 
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  require("../classes/Salesforce.class.php");

  
  $_BASE = "/salesforce";

$app->post($_BASE, function(Request $request, Response $response, array $args) {
  $body = $request->getParsedBody();

  $Salesforce = new Salesforce();
  $sfResponse = $Salesforce->createLead($body["lead"]);

  $data = array("success"=>true, "salesforce" =>$sfResponse);




  $newResponse = $response->withJson($data);
  // $response->getBody()->write("$body");
  return $newResponse;
});