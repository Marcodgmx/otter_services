<?php
function tryCatch($function){
  try{
  $function();
  }catch(Exception $Error){
  $newResponse = $response->withJson($Error->response);
  return $newResponse;
  }
}