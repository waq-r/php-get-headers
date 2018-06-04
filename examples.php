<?php
error_reporting(E_ERROR | E_PARSE);

require_once('getHeaders.class.php');

//REQUEST HTTP HEADERS

//Make a shell get headers request as mac/chrome browser

$h = new GetHeaders('mac');

//get all headers in variable

$headers = $h->get_shell_headers('http://www.youtube.com/');

var_dump($headers);

//HTTP HEADERS using best method available

//jsut pass URL to getHeaders method, which will decide and use best
// and fastest method to get and return headers

	$h = new GetHeaders('mac');

	$headers = $h->getHeaders('https://www.google.com/');

	var_dump($h, $headers);

//CHECK SITE STATUS

//Site is up, correct URL, status 200 OK

if($headers['code'] === 200){
  echo 'Site is up ';
  }

//Site is up but moved to new URL,
//A missing https or slash at the end of URL also results in
//Status 301 Moved Perminantly

if($headers['code'] === 301){
  echo 'Site new  URL is '.$headers['location'];
  }

//BENCHMARK PERFORMANCE

//An array of URLs to gauge performance, leave blank and
// Benchmark methods stored array of URLs would be used

$h->benchmarkRequest();

