<?php

/**
 * @author    Proximify Inc <support@proximify.com>
 * @copyright Copyright (c) 2020, Proximify Inc
 * @license   MIT
 */

namespace Proximify\Uniweb\API;

use Exception;

require 'RemoteConnection.php';

/**
 * Reference API Client for the Uniweb application.
 */
class UniwebClient
{
	const FILES = '_files_';

	/** @var string Default path for the connection credentials. */
	const CREDENTIALS_PATH = 'settings/credentials.json';

	/** @var string Alternative path for the connection credentials. */
	const ALT_CREDENTIALS_PATH = 'settings/credentials/credentials.json';

	/** @var array API credentials: homepage, clientName, clientSecret. */
	private $credentials;

	/**
	 * Constructs a UNIWeb client with given credentials. There is also a helper static
	 * function, getClient(), that can be used by passing the credential parameters
	 * as individual function arguments in order to construct a client object.
	 */
	public function __construct(?array $credentials = null)
	{
		date_default_timezone_set('UTC');

		$this->assertClientParams($credentials);

		$this->credentials = $credentials;
		$this->conn = new RemoteConnection();
	}

	/**
	 * Get the homepage URL defined in the credentials.
	 *
	 * @return string
	 */
	public function getInstanceUrl(): string
	{
		$url = trim($this->credentials['homepage'] ?? '');

		if (!$url) {
			throw new Exception("Invalid empty homepage URL in credentials");
		}

		$parts = parse_url($url);
		$host = $parts['host'] ?? '';
		$path = $parts['path'] ?? '';

		// If there is no host, there might be a missing '//'
		if (!$host && $path) {
			$parts = parse_url('//' . $url);
			$host = $parts['host'] ?? '';
			$path = $parts['path'] ?? '';
		}

		if (!$host) {
			throw new Exception("Invalid homepage URL");
		}

		// Path must end with a '/' iff it's not empty
		if ($path = trim($path, '/')) {
			$path .= '/';
		}

		if ($host == 'localhost' || $host == '127.0.0.1') {
			$scheme = $parts['scheme'] ?? 'http';
		} else {
			$scheme = 'https';
		}

		// Only allow for the secure HTTPS protocol
		return $scheme . '://' . $host . '/' . $path;
	}

	public function getClientName(): string
	{
		if (!($clientName = $this->credentials['clientName'] ?? false)) {
			throw new Exception("Invalid empty client name in credentials");
		}

		return trim($clientName);
	}

	public function getClientSecret(): string
	{
		if (!($clientSecret = $this->credentials['clientSecret'] ?? false)) {
			throw new Exception("Invalid empty client secret in credentials");
		}

		return trim($clientSecret);
	}

	/**
	 * Add a new section item
	 * @param array $params parameters to add a new section items includes
	 * ID: unique identifier of member ex: macrini@proximify.ca
	 * Resources: path requested ex: cv/education/degrees
	 */
	public function add($request)
	{
		self::assertValidRequest($request);
		self::assertHasId($request);

		$request['action'] = 'add';

		return $this->sendRequest($request);
	}

	/**
	 * Edit a section item
	 * @param array $params parameters to add a new section items includes
	 * ID: unique identifier of member ex: macrini@proximify.ca
	 * Resources: path requested ex: cv/education/degrees
	 */
	public function edit($request)
	{
		self::assertValidRequest($request);
		self::assertHasId($request);

		$request['action'] = 'edit';

		return $this->sendRequest($request);
	}

	/**
	 * Read a section item
	 * @param array $params parameters to add a new section items includes
	 * ID: unique identifier of member ex: macrini@proximify.ca
	 * Resources: path requested ex: cv/education/degrees
	 * Filter(optional): filtering settings ex: login_name => 'mert@proximify.ca'
	 * @param bool $assoc returns array if it is true, otherwise json.
	 */
	public function read($request, $assoc = false)
	{
		self::assertValidRequest($request);

		$request['action'] = 'read';

		return $this->sendRequest($request, $assoc);
	}

	/**
	 * Clear section.
	 *
	 * @param array $params parameters to add a new section items includes
	 * ID: unique identifier of member ex: macrini@proximify.ca
	 * Resources: path requested ex: cv/education/degrees
	 * Filter(optional): filtering settings ex: login_name => 'mert@proximify.ca'
	 */
	public function clear($request)
	{
		self::assertValidRequest($request);
		self::assertHasId($request);

		$request['action'] = 'clear';

		return $this->sendRequest($request);
	}

	/**
	 * Update profile picture
	 * @param array $params
	 */
	public function updatePicture($request)
	{
		self::assertValidRequest($request);
		self::assertHasId($request);

		$request['action'] = 'updatePicture';

		//self::printResponse($request);
		return $this->sendRequest($request);
	}

	/**
	 * Adds a file to the given request.
	 *
	 * @param array $name A unique name for the file. Any name is without dots is fine.
	 */
	public function addFileAttachment(&$request, $name, $path, $mimeType)
	{
		if (!is_readable($path)) {
			throw new Exception("Cannot read file at $path");
		}

		// Make sure that the name has no periods because PHP converts them to '_'
		// when used as the file names.
		if (strpos($name, '.') !== false) {
			throw new Exception("Attachment name can't contain periods: $name");
		}

		self::assertValidRequest($request);

		if (!isset($request[self::FILES])) {
			$request[self::FILES] = [];
		}

		$request[self::FILES][$name] = RemoteConnection::createFileObject($path, $mimeType);
	}

	/**
	 * Get section info
	 * @param (string, array) $resources path requested ex: cv/education/degrees
	 */
	public function getInfo($resources)
	{
		if (!$resources) {
			throw new Exception('Resources cannot be empty');
		}

		$request = array('action' => 'info', 'resources' => $resources);

		return $this->sendRequest($request);
	}

	/**
	 * Get field options.
	 * @param (string, array) $resources path requested ex: cv/education/degrees
	 */
	public function getOptions($resources)
	{
		if (!$resources) {
			throw new Exception('Resources cannot be empty');
		}

		$request = array('action' => 'options', 'resources' => $resources);

		return $this->sendRequest($request);
	}

	/**
	 * Returns list of title names.
	 */
	public function getTitles()
	{
		$request = array('action' => 'getTitles');

		return $this->sendRequest($request);
	}

	/**
	 * Returns list of units and their parents.
	 */
	public function getUnits()
	{
		$request = array('action' => 'getUnits');

		return $this->sendRequest($request);
	}

	/**
	 * Returns list of RBAC Roles.
	 */
	public function getRoles()
	{
		$request = array('action' => 'getRoles');

		return $this->sendRequest($request);
	}

	/**
	 * Returns list of RBAC Roles.
	 */
	public function getPermissions()
	{
		$request = array('action' => 'getPermissions');

		return $this->sendRequest($request);
	}

	/**
	 * Returns list of RBAC Roles.
	 */
	public function getRolesPermissions()
	{
		$request = array('action' => 'getRolesPermissions');

		return $this->sendRequest($request);
	}

	/**
	 * Returns list of members.
	 */
	public function getMembers()
	{
		$request = array('action' => 'getMembers');

		return $this->sendRequest($request);
	}

	/**
	 * Finds the ID of an option from its value(s). The comparisons are case insensitive.
	 *
	 * @param @options The options for the values of a field are given as an array of
	 * arrays of the form: [[ID, name, parent_name, grand_parent_name, ...], [...]]
	 *
	 * @param $value It can be a string or an array of strings. If it is a string,
	 * then only the option 'name' is considered when comparing against the $value. If it
	 * is an array, the elements of $value will be matched against 'name', 'parent_name',
	 * 'grand_parent_name', etc, respectively.
	 *
	 * @return The ID of the first option found that is equal to $value.
	 */
	public function findFieldOptionId($options, $value)
	{
		if (is_array($value)) {
			foreach ($options as $opt) {
				foreach ($value as $valIdx => $val) {
					// Note that the str comparison returns 0 iff the strings are equal
					if (!isset($opt[$valIdx + 1]) || strcasecmp($opt[$valIdx + 1], $val)) {
						continue 2;
					} // The opt is not a match. Go to the next opt.
				}

				return $opt[0];
			}
		} else { // Faster method for the single value case
			foreach ($options as $opt) {
				if (strcasecmp($opt[1], $value) == 0) {
					return $opt[0];
				}
			}
		}

		return false;
	}

	/**
	 * Gets the requested resource.
	 *
	 * @param array $request The request to send to the server.
	 * @param bool $assoc When TRUE, returned objects will be converted into associative arrays.
	 * @param int $maxRetries The number of time that its okay to try when attempting to renew
	 * a token.
	 * @return mixed The answer from the server.
	 */
	public function sendRequest($request, $assoc = false, $maxRetries = 10)
	{
		if (isset($this->accessToken) && time() < $this->accessToken['expiration']) {
			$rawResource = $this->getResource($request);

			$resource = json_decode($rawResource, $assoc);

			if (is_object($resource) && property_exists($resource, 'error')) {
				if ($resource->error != 'invalid_token') {
					throw new Exception($resource->error);
				}
			} elseif (is_null($resource)) {
				throw new Exception('Invalid answer: ' . $rawResource);
			} else {
				return $resource;
			}
		} elseif ($maxRetries < 0) {
			throw new Exception('Could not renew access token. Maximum retry attempts reached.');
		}

		$this->getAccessToken();

		// Recursive call. Avoid accidental infinite-loops by decreasing the number
		// of valid retry attempts.
		return $this->sendRequest($request, $assoc, --$maxRetries);
	}

	/**
	 * Contacts the token server and retrieves the token.
	 */
	public function getAccessToken()
	{
		$postFields = [
			'grant_type' => 'password',
			'username' => $this->getClientName(),
			'password' => $this->getClientSecret()
		];

		$tokenURL = $this->getInstanceUrl() . 'api/token.php';
		$result = $this->conn->post($tokenURL, $postFields, true);

		if ($result === false) {
			throw new Exception('Access token could not be retrieved.');
		}

		$result = json_decode($result);

		if (is_object($result) && property_exists($result, 'error')) {
			throw new Exception('Error: ' . $result->error);
		} elseif (!$result || !property_exists($result, 'expires_in')) {
			throw new Exception('Unable to obtain access token');
		}

		$expiry = time() + $result->{'expires_in'};

		$this->accessToken = array(
			'token' => $result->access_token,
			'expiration' => $expiry
		);
	}

	/**
	 * Ensures that all mandatory the credential properties are set.
	 */
	public static function assertClientParams($credentials)
	{
		if (!$credentials || !is_array($credentials)) {
			throw new Exception('Invalid credentials');
		}

		if (empty($credentials['clientName'])) {
			throw new Exception('Client name cannot be empty');
		}

		if (empty($credentials['clientSecret'])) {
			throw new Exception('Client secret cannot be empty');
		}

		if (empty($credentials['homepage'])) {
			throw new Exception('Homepage cannot be empty');
		}
	}

	/**
	 * Ensures that the request is an array with all mandatory properties set. It does
	 * not check for the presence of an 'id' property because that is optional. To check
	 * for that, call assertHasId() after this function.
	 */
	public static function assertValidRequest($request)
	{
		if (!$request || !is_array($request)) {
			throw new Exception('Invalid request parameters');
		}

		if (empty($request['resources'])) {
			throw new Exception('Empty "resources" property in request');
		}
	}

	/**
	 * Ensures that the request has a 'id' property. Should be called after
	 * assertValidRequest().
	 */
	public static function assertHasId($request)
	{
		if (empty($request['id'])) {
			throw new Exception('Missing "id" property in request');
		}
	}

	/**
	 * Helper function to create an object of this class with given credentials.
	 */
	public static function getClient($clientName, $clientSecret, $homepage)
	{
		$credentials = array(
			'clientName' => $clientName,
			'clientSecret' => $clientSecret,
			'homepage' => $homepage
		);

		return new self($credentials);
	}

	/**
	 * Helper function to print out a response object in a readable way.
	 */
	public static function printResponse($response, $title = false)
	{
		if ($title) {
			echo '<h3>' . $title . '</h3>';
		}

		echo '<pre>' . print_r($response, true) . '</pre>';
	}


	/**
	 * Helper function to log data to the debug console.
	 *
	 * @return void
	 */
	public static function log($data)
	{
		error_log(print_r($data, true));
	}

	/**
	 * Load API credentials.
	 *
	 * @param string|null $path Path to a JSON file with the credentials.
	 * @return string
	 */
	public static function loadCredentials(?string $path = null): array
	{
		if (!$path) {
			$rootDir = dirname(__DIR__);
			$path = self::getSubPath($rootDir, self::CREDENTIALS_PATH) ??
				self::getSubPath($rootDir, self::ALT_CREDENTIALS_PATH);
		}

		if (!is_file($path)) {
			throw new Exception("Cannot find '$path'");
		}

		$json = file_get_contents($path);

		return $json ? json_decode($json, true) : [];
	}

	/**
	 * Run a predefined API query.
	 *
	 * @param array $params Query parameters.
	 * @return string The response.
	 */
	public static function runQuery(array $params): ?string
	{
		if (empty($params['queryName'])) {
			throw new Exception('Invalid empty query name');
		}

		$queryDir = $params['queryDir'] ?? dirname(__DIR__) . '/queries';
		$queryName = $params['queryName'];
		echo $queryDir;
		// Some queries are in a sub-folder and others are a single file
		$filename = self::getSubPath($queryDir, "$queryName.php") ??
			self::getSubPath($queryDir, "$queryName/$queryName.php");

		if (!$filename) {
			throw new Exception("Cannot find query file '$queryName.php'");
		}

		// Buffering the standard output.
		ob_start();
		include $filename;
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Print out the response from runQuery().
	 *
	 * @param array $params
	 * @return void
	 */
	public static function outputQuery(array $params): void
	{
		echo self::runQuery($params);
	}

	/**
	 * Get an existing absolute path to a directory reachable from the given root directory. 
	 * Return null for paths that go outside of the root directory.
	 *
	 * @param string $rootDir Root directory.
	 * @param string $relDir Relative path from the root directory.
	 * @return string|null
	 */
	public static function getSubPath(string $rootDir, string $relDir): ?string
	{
		return (($rootDir = realpath($rootDir)) &&
			($dir = realpath($rootDir . '/' . $relDir)) &&
			substr($dir, 0, strlen($rootDir)) === $rootDir) ?
			$dir : null;
	}

	/**
	 * Contacts the resource server and retrieves the requested resources.
	 *
	 * @param $email The email address of the person searched for, or '*' as wild card.
	 * @param $filters Array of filters. Each member should be a valid filter or a '*'.
	 * @param $format Array of formats. Each member should be a valid format.
	 */
	protected function getResource($request)
	{
		$resourceURL = $this->getInstanceUrl() . 'api/resource.php?access_token=' .
			$this->accessToken['token'];

		$files = [];

		if (is_array($request)) {
			// If the request has files to send, that should not be converted to JSON
			if (!empty($request[self::FILES])) {
				foreach ($request[self::FILES] as $key => $value) {
					if ($value instanceof CURLFile) {
						$files[$key] = $value;
						unset($request[self::FILES][$key]);
					}
				}
			}

			$request = json_encode($request);
		}

		$postFields = array('request' => $request);

		if ($files) {
			$postFields = array_merge($postFields, $files);
		}

		$result = $this->conn->post($resourceURL, $postFields);

		return $result;
	}
}
