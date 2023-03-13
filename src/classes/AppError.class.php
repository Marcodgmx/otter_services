<?php

class AppError extends Exception {
  public Array $response;

  function __construct($message){
    parent::__construct();
    $this->response = array(
      "status" => "fail",
      "message"=> $message
    );
  }
}