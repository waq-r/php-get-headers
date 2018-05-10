# php-get-headers
<h1>Class to get HTTP headers using Linux shell, CURL or PHP function get_headers.</h1>


Class to get http headers using diffrent methods available in PHP

 * Takes URL as argument
 * Contains 3 diffrent methods to get http headers
   #1. PHP CURL, 
   #2. Linux shell via PHP shell_exec,
   #3. PHP function get_headers()
 * all get header methods return an array of all headers
 * first element of array contains int http response code

<h3>INPUT/OUTPUT</h3>
 makes http request to a URL return http response headers
 @return array of headers
 @param  (mac , pc, mobile): optional user-agent param to change CURL user-agent
 
 <h2>Use Cases</h2>
 <p>
<h3>Check if a web site is up</h3>
Use crontab to make http requests periodically to the website you want to monitor, send alert if http response code is not 200

<h3>Make sure URL is up before making file_get_contents</h3>
Make a headers request to ensure webpage exists, befor making PHP's file_get_contents or fopen calls.
</p>

 
<h2>Usage Example</h2>

$h = new GetHeaders('mac'); //sets CURL's user-agent to Mac

$h = new GetHeaders(); //Keeps default user-agent

$headers = $h->get_curl_headers('https://www.iplocality.com/'); //get headers via CURL

$headers = $h->get_shell_headers('https://preproxy.com/'); //get headers via Linux Shell

$headers = $h->get_headers('http://www.google.com/'); //get headers via PHP function  get_headers

<h3>Benchmark speed of diffrent get_http_headers methods</h3>

<p>Class contains benchmark method with some URLs to test all get header methods. Use your own array of URLs to benchmark performance.</p>

<pre class="xdebug-var-dump" dir="ltr"><small>/home/vagrant/Code/getHeaders.class.php:251:</small>
<b>array</b> <i>(size=10)</i>
  'REQUEST WEBSITES           ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'PHP CURL              LINUX SHELL           PHP GET HEADERS     '</font> <i>(length=64)</i>
  'https://www.google.com/    ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'1.8369688987732       1.4304299354553       1.1995270252228     '</font> <i>(length=64)</i>
  'https://www.bing.com/      ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'0.96593499183655      0.98185205459595      0.92472505569458    '</font> <i>(length=64)</i>
  'http://www.yahoo.com/      ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'0.59260082244873      0.35894203186035      2.4568209648132     '</font> <i>(length=64)</i>
  'http://www.preproxy.com/   ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'1.1098358631134       0.35897588729858      1.6951670646667     '</font> <i>(length=64)</i>
  'http://www.iplocality.com/ ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'0.71652007102966      0.72328615188599      0.82622981071472    '</font> <i>(length=64)</i>
  'http://www.facebook.com/   ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'0.73158597946167      0.81967496871948      2.4969518184662     '</font> <i>(length=64)</i>
  'http://www.youtube.com/    ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'0.58801817893982      1.0290570259094       1.3727321624756     '</font> <i>(length=64)</i>
  'http://www.twitter.com/    ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'0.66506004333496      0.47173810005188      3.1517231464386     '</font> <i>(length=64)</i>
  'AVERAGE SPEED              ' <font color="#888a85">=&gt;</font> <small>string</small> <font color="#cc0000">'0.90081560611725      0.77174451947212      1.7654846310616     '</font> <i>(length=64)</i>
</pre>

<p>PHP function get_headers is slowest, while linux shell and CURL are much faster. However when requesting secure https header PHP functions tend to perform better. Try and see if you get same results</p>
