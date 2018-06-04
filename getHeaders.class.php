<?php
/**
 * Class to get http headers using diffrent methods available in PHP
 *
 * Takes URL as argument
 * Contains 3 diffrent methods to get http headers
 * #1. PHP CURL,
 * #2. Linux shell via PHP shell_exec,
 * #3. PHP function get_headers()
 * all get header methods return an array of all headers
 * first element of array contains int http response code
 *
 * @author waqar <waqar3@gmail.com>
 * @copyright Waqar
 * @license GNU GENERAL PUBLIC LICENSE Version 3
 */
class GetHeaders

	{
	/**
	 * makes http request to a URL return http response headers
	 * @return array of headers
	 * @param  (mac , pc, mobile): optional user-agent param to change CURL user-agent
	 * example usage
	 * $h = new GetHeaders('mac'); //sets CURL's user-agent to Mac
	 * $h = new GetHeaders(); //Keeps default user-agent
	 * $headers = $h->get_curl_headers('http://www.google.com/'); //get headers via CURL
	 * $headers = $h->get_shell_headers('http://www.google.com/'); //get headers via Linux Shell
	 * $headers = $h->get_headers('http://www.google.com/'); //get headers via PHP function  get_headers
	 */

	// Connection time out after x seconds.

	public $timeOut = 15;

	// Change default CURL/Shell user-agent to a custom one.

	public $userAgent = NULL;

	// http request execution time
	// must be reset between two request in same instance

	private $executionTime;
	/**
	 * Some hosts might not respond to CURL requests, or respond diffrently
	 * if request is not from a browser, CURL user-agent can be set to browser.
	 *
	 * Contructors checks and sets, if optional user-agent have been provided
	 * Contains three ready to use CURL/Shell user-agents: mac, pc, mobile
	 */
	public

	function __construct($userAgent = null)
		{

		// if optional second param is set then setup CURL user-agent value

		if ($userAgent === 'mobile' | $userAgent === 'mac' | $userAgent === 'pc')
			{
			$user_agents = array(
				'mobile' => 'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Mobile Safari/537.36',
				'mac' => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36',
				'pc' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246'
			);
			$this->userAgent = $user_agents[$userAgent];
			}
		}
	
	/*
	* based on URL type get http headers using best method
	* @return array of headers
	* @param URL to get http headers of
	*/
	public

	function getHeaders($url)
		{

		// if https then get_headers method would be faster

		if (parse_url($url, PHP_URL_SCHEME) === 'https')
			{
			$headers = $this->get_headers($url);
			}

		// use shell or php curl if url is not secure

		  else
			{
			$headers = $this->get_curl_headers($url);
			}

		return $headers;
		}

	/*
	* get http headers using Linux shell
	* @return array of headers
	* @param URL to get http headers of
	*/
	public

	function get_shell_headers($url)
		{

		// check if custom user-agent needs to be set

		if (!empty($this->userAgent))
			{
			$user_agent = "-H '{$this->userAgent}' ";
			}

		// note execution start time

		$startTime = microtime(true);

		// if request speed is below 1000bytes for 15 sec than close curl

		$header_text = shell_exec("curl --speed-time {$this->timeOut} --speed-limit 1000 10 -Is {$user_agent}{$url} | head");

		// note execution end time

		$this->executionTime = microtime(true) - $startTime;

		// get and set http status code

		$headers['code'] = $this->getHttpStatusCode($header_text);

		// make an array with first element an int http status code followed by rest of headers

		$headers = array_merge($headers, $this->generateHeadersArray($header_text));
		return $headers;
		}

	/*
	* get http headers using CURL
	* @return array of headers
	* @param URL to get http headers of
	*/
	public

	function get_curl_headers($url)
		{

		// note execution start time

		$startTime = microtime(true);

		// initiate curl and set URL

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		// check if custom user-agent needs to be set

		if (!empty($this->userAgent))
			{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				$this->userAgent
			));
			}

		// set rest of options

		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);

		// execute CURL request

		$header_text = curl_exec($ch);

		// note execution end time

		$this->executionTime = microtime(true) - $startTime;

		// check, if no error then make an array of header values

		if (curl_errno($ch))
			{
			print "Error: " . curl_error($ch);
			}
		  else
			{

			// get http code and close curl

			$headers['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			// combine first element http status code and rest of headers

			$headers = array_merge($headers, $this->generateHeadersArray($header_text));
			}

		return $headers;
		}

	/*
	* get http headers using PHP function get_headers
	* @return array of headers, in status code as first element
	* @param URL to get http headers of
	*/
	public

	function get_headers($url)
		{

		// note execution start time

		$startTime = microtime(true);

		// get headers

		$header_array = get_headers($url);

		// note execution end time

		$this->executionTime = microtime(true) - $startTime;

		// get int http status code

		$headers['code'] = $this->getHttpStatusCode($header_array[0]);

		// append status code element on top of headers array elements

		$headers = array_merge($headers, $this->generateHeadersArray($header_array));
		return $headers;
		}

	/*
	* generate a standard structure headers array for all methods
	* @return array of headers, in status code as first element
	* @param takes http headers string or array
	*/
	private
	function generateHeadersArray($headerText)
		{
		if (!is_array($headerText))
			{

			// put each line of header text in array

			$header_text = explode(PHP_EOL, $headerText);
			}
		  else
			{
			$header_text = $headerText;
			}

		// A standard http heade has lines like 'Date: Sat, 05 May 2018 09:37:24 GMT'
		// except first line all other lines contain property name: property value
		// we already have status code, so this foreach loop gets property names and
		// values from lines and stores in an array.

		$headers = [];
		foreach($header_text as $line)
			{

			// explode on : and get 2 elements only

			$value = explode(':', $line, 2);
			$headers[strtolower($value[0]) ] = trim($value[1]);
			}

		return array_filter($headers);
		}

	/*
	* Extracts a 3 digit status code from text
	* @return integer status code
	* @param text string to find status code in
	*/
	private
	function getHttpStatusCode($header)
		{

		// find 3 digits surronded by single space

		preg_match('/\s[\d]{3}\s/', $header, $match);
		return (int)trim($match[0]);
		}

	/*
	* Get executionTime of last HTTP request
	*/
	public

	function getExecutionTime()
		{
		return $this->executionTime;
		}

	/*
	* Extracts a 3 digit status code from text
	* @return var_dumps array with http request URLs and time each method took to get http headers
	*		=> method1_name: time
	*url[0] => method2_name: time
	*		=> method3_name: time
	* @param optional array of URLs, if not set, then stored URls would be used
	*/
	public

	function benchmarkRequest($urlList)
		{

		// if no URL is set then make url array

		if (!is_array($urlList))
			{
			$urlList = array(

				'https://www.google.com/',
				'https://www.bing.com/',
				'http://www.yahoo.com/',
				'http://www.preproxy.com/',
				'http://www.iplocality.com/',
				'http://www.facebook.com/',
				'http://www.youtube.com/',
				'http://www.twitter.com/'
			);

			// Array of all three get http headers methods

			$methods = array(
				'php_curl' => 'get_curl_headers',
				'linux_shell' => 'get_shell_headers',
				'php_get_headers' => 'get_headers'
			);

			// Make a multidimentional array containg method execution times
			// url as keys and their value: an array of method name=>times.
			// loop through methods and use inner foreach to make http request to all urls
			// $reponses

			foreach($methods as $key => $value)
				{

				// 1. make first element of $benchmark, an assoc array of benchmark title/header

				$benchmark['REQUEST WEBSITES'][$key] = strtoupper(str_replace('_', ' ', $key));

				// #2. loop into url's, make requests, save execution times

				foreach($urlList as $k => $url)
					{

					// $value has method name,

					$this->$value($url);
					$benchmark[$url][$key] = $this->executionTime;
					}

				// 3. Append last element to $benchmark, an assoc array of average speeds/execution times

				$benchmark['AVERAGE SPEED'][$key] = array_sum(array_column($benchmark, $key)) / sizeof($urlList);
				}

			// now that $benchmark has titles/header methods/speeds and method/average speed
			// Prettyfy $benchmark: make an array of url as key and their array values flattened as string value

			foreach($benchmark as $url => $value)
				{

				// some padding added to prettyfy

				$benchmarkTable[str_pad($url, 27) ] = implode('  ', array_map(
				function ($val)
					{
					return str_pad($val, 20);
					}

				, $value));
				}

			return '<pre>' . var_dump($benchmarkTable) . '</pre>';
			}
		}
	}
