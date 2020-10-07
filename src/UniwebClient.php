<?php

/**
 * File for class UniwebClient.
 *
 * @author    Proximify Inc <support@proximify.com>
 * @copyright Copyright (c) 2020, Proximify Inc
 * @license   MIT
 * @version   1.1.0 Uniweb API
 */

namespace Proximify\Uniweb\API;

use Exception;

require 'RemoteConnection.php';

class UniwebClient
{
	const FILES = '_files_';

	public $clientName;
	public $homepage;
	protected $clientSecret;

	/**
	 * Constructs a UNIWeb client with given credentials. There is also a helper static
	 * function, getClient(), that can be used by passing the credential parameters
	 * as individual function arguments in order to construct a client object.
	 */
	public function __construct($credentials)
	{
		date_default_timezone_set('UTC');

		$this->assertClientParams($credentials);

		$this->clientName = $credentials['clientName'];
		$this->clientSecret = $credentials['clientSecret'];
		$this->homepage = $credentials['homepage'];

		$this->conn = new RemoteConnection();
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
			$request[self::FILES] = array();
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
		$postFields = array(
			'grant_type' => 'password',
			'username' => $this->clientName,
			'password' => $this->clientSecret
		);

		$tokenURL = $this->homepage . 'api/token.php';
		$result = $this->conn->post($tokenURL, $postFields, true);

		if ($result === false) {
			throw new Exception('Access token could not be retrieved.');
		}

		$result = json_decode($result);

		if (property_exists($result, 'error')) {
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
	 * Ensures that the request is an array with all manadatory properties set. It does
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
	 * Helper function to print out a response object in a deadable way.
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
	 * Contacts the resource server and retrieves the requested resources.
	 *
	 * @param $email The email address of the person searched for, or '*' as wild card.
	 * @param $filters Array of filters. Each member should be a valid filter or a '*'.
	 * @param $format Array of formats. Each member should be a valid format.
	 */
	protected function getResource($request)
	{
		$resourceURL = $this->homepage . 'api/resource.php?access_token=' .
			$this->accessToken['token'];

		$files = array();

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
