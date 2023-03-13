<?php
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  require("../classes/Salesforce.class.php");
  require("../classes/AppError.class.php");

  require("../utils/bodyValidator.utils.php");
  require("../utils/tryCatch.utils.php");

  $_BASE = "/{environment}/salesforce";

//CREATE A LEAD IN SALESFORCE
$app->post($_BASE."/lead", function(Request $request, Response $response, array $args) {
  $environment= $args["environment"];

  if(isset($_POST["lead"])){
    
  $Salesforce = new Salesforce($environment);
  $sfResponse = $Salesforce->createLead($_POST["lead"]);

  $data = array("status"=>"success", "salesforce" =>$sfResponse);

  
  $newResponse = $response->withJson($data);
  return $newResponse;
  }
  $data = array("status"=>"fail", "message" =>"Please provide a lead");
  $newResponse = $response->withJson($data);
  return $newResponse;
});

$app->post($_BASE."/pardot/prospect", function(Request $request, Response $response, array $args) {
  try{
    $environment= $args["environment"];

    if(!bodyValidator($_POST, ["upsertByEmailPardot"])) throw new AppError("Please provide upsertByEmailPardot key");
    if(!bodyValidator($_POST["upsertByEmailPardot"], ["matchEmail", "prospect"])) throw new AppError("Please provide matchEmail AND prospect keys inside upsertByEmailPardot");

    
    $Salesforce = new Salesforce($environment);
    $responsePardot = $Salesforce->Pardot->createProspect($_POST["upsertByEmailPardot"]);
    $newResponse = $response->withJson($responsePardot);
    return $newResponse;
  }catch(Exception $Error){
    $newResponse = $response->withJson($Error->response);
    return $newResponse;
  }
});
