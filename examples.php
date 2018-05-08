<?php
error_reporting(E_ERROR | E_PARSE);

require_once('getHeaders.class.php');

//REQUEST HTTP HEADERS

//Make a shell get headers request as mac/chrome browser

$h = new GetHeaders('mac');

//get all headers in variable

$headers = $h->get_shell_headers('http://www.youtube.com/');

var_dump($headers);

//CHECK SITE STATUS

if($headers['code'] === 200){
  echo 'Site is up ';
  }

//BENCHMARK PERFORMANCE

//An array of URLs to gauge performance, leave blank and
// Benchmark methods stored array of URLs would be used

$h->benchmarkRequest();

