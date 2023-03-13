<?php

function bodyValidator(Array $body, Array $keys){
  $bool = true ;
  for($i=0;$i<count($keys); $i++){
    $bool = $bool * isset($body[$keys[$i]]);
  }
  return $bool;
}