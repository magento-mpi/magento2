<?php
/**
 * Test client for REST API testing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_TestCase_Webapi_Adapter_Rest_CurlClient
{

    /**
     * @var string REST URL base path
     */
    const REST_BASE_PATH = '/webapi/rest/';

    /**
     * @var array Default CURL options
     */
    protected $_curlOpts = array(
        CURLOPT_RETURNTRANSFER => true, // return result instead of echoing
        CURLOPT_SSL_VERIFYPEER => false, // stop cURL from verifying the peer's certificate
        CURLOPT_FOLLOWLOCATION => false, // follow redirects, Location: headers
        CURLOPT_MAXREDIRS => 10, // but don't redirect more than 10 times
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json'
        )
    );


    /**
     * @var array Last response
     */
    protected $_lastResponse;

    /**
     * @var array Last response headers
     */
    protected $_lastResponseHeaders;

    /**
     * Perform HTTP GET request
     *
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @param array $data
     * @param array $headers
     * @return string
     */
    public function get($resourcePath, $data = array(), $headers = array())
    {
        $url = $this->_constructResourceUrl($resourcePath);
        if (!empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        $curlOpts = $this->_curlOpts;
        $headers = array_merge($curlOpts[CURLOPT_HTTPHEADER], $headers);
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;


        $curl = $this->_prepRequest($url, $curlOpts);
        $body = $this->_invokeApi($curl);
        $body = $this->_processBody($body);

        return $body;
    }

    /**
     * Perform HTTP POST request
     *
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @param array $data
     * @param array $headers
     * @return string
     */
    public function post($resourcePath, $data, $headers = array())
    {
        $url = $this->_constructResourceUrl($resourcePath);

        // json encode data
        $jsonData = $this->_jsonEncode($data);

        $curlOpts = $this->_curlOpts;
        $curlOpts[CURLOPT_CUSTOMREQUEST] = 'POST';
        if (!is_array($data)) {
            $headers[] = 'Content-Length: ' . strlen($jsonData);
        }
        $headers = array_merge($curlOpts[CURLOPT_HTTPHEADER], $headers);
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;
        $curlOpts[CURLOPT_POSTFIELDS] = $jsonData;

        $curl = $this->_prepRequest($url, $curlOpts);
        $body = $this->_invokeApi($curl);
        $body = $this->_processBody($body);
        return $body;
    }

    /**
     * Perform HTTP PUT request
     *
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @param array $data
     * @param array $headers
     * @return string
     */
    public function put($resourcePath, $data, $headers = array())
    {
        $url = $this->_constructResourceUrl($resourcePath);

        // json encode data
        $jsonData = $this->_jsonEncode($data);

        $curlOpts = $this->_curlOpts;
        $curlOpts[CURLOPT_CUSTOMREQUEST] = 'PUT';
        if (!is_array($data)) {
            $headers[] = 'Content-Length: ' . strlen($jsonData);
        }
        $headers = array_merge($curlOpts[CURLOPT_HTTPHEADER], $headers);
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;
        $curlOpts[CURLOPT_POSTFIELDS] = $jsonData;

        $curl = $this->_prepRequest($url, $curlOpts);
        $body = $this->_invokeApi($curl);

        $body = $this->_processBody($body);

        return $body;
    }

    /**
     * Perform HTTP DELETE request
     *
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @param array $headers
     * @return string
     */
    public function delete($resourcePath, $headers = array())
    {
        $url = $this->_constructResourceUrl($resourcePath);
        $curlOpts = $this->_curlOpts;
        $curlOpts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        $headers = array_merge($curlOpts[CURLOPT_HTTPHEADER], $headers);
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;

        $curl = $this->_prepRequest($url, $curlOpts);
        $body = $this->_invokeApi($curl);

        $body = $this->_processBody($body);

        return $body;
    }

    /**
     * Get response body
     *
     * @return string
     */
    public function getResponseBody()
    {
        return $this->_lastResponse['body'];
    }

    /**
     * Get response status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_lastResponse['meta']['http_code'];
    }

    /**
     * Return response header (case insensitive) or NULL if not present.
     *
     * @param string $header
     * @return string
     */
    public function getResponseHeader($header)
    {
        if (empty($this->_lastResponseHeaders[strtolower($header)])) {
            return NULL;
        }
        return $this->_lastResponseHeaders[strtolower($header)];
    }

    /**
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @return string resource URL
     * @throws Exception
     */
    protected function _constructResourceUrl($resourcePath)
    {
        return rtrim(TESTS_WEBSERVICE_URL, '/') . self::REST_BASE_PATH . ltrim($resourcePath, '/');
    }

    /**
     * Prepare request
     *
     * @param string URL
     * @param array $opts cURL Options
     * @throws Exception
     * @return resource
     */
    protected function _prepRequest($url, $opts)
    {
        $curl = curl_init($url);
        if ($curl === false) {
            throw new Exception("Error Initializing cURL for baseUrl: " . $url);
        }

        foreach ($opts as $opt => $val) {
            curl_setopt($curl, $opt, $val);
        }
        return $curl;
    }

    /**
     * Invokes the REST api using passing $curl object
     *
     * @param resource $curl
     * @return mixed
     * @throws Exception
     */
    protected function _invokeApi($curl)
    {
        $this->_lastResponseHeaders = array();
        $this->_lastResponse = array();

        // curl_error() needs to be tested right after function failure
        $this->_lastResponse["body"] = curl_exec($curl);
        if ($this->_lastResponse["body"] === false) {
            throw new Exception(curl_error($curl));
        }

        $this->_lastResponse["meta"] = curl_getinfo($curl);
        if ($this->_lastResponse["meta"] === false) {
            throw new Exception(curl_error($curl));
        }

        curl_close($curl);

        $this->_checkResponseForError();

        return $this->_lastResponse["body"];
    }

    /**
     * Check last response for error & throw exception if a error response was received
     *
     * @throws Exception
     */
    protected function _checkResponseForError()
    {
        $meta = $this->_lastResponse['meta'];
        $body = $this->_lastResponse['body'];

        if ($meta === false) {
            return;
        }

        if ($meta['http_code'] >= 400) {
            throw new Exception ($body, $meta['http_code']);
        }
    }

    /**
     * Process body
     *
     * @param string $body
     * @return mixed
     */
    protected function _processBody($body)
    {
        return $this->_jsonDecode($body);
    }

    /**
     * JSON encode with error checking
     *
     * @param mixed $data
     * @return string
     * @throws Exception
     */
    protected function _jsonEncode($data)
    {
        $ret = json_encode($data);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(
                'Encoding error: ' . $this->_getLastJsonErrorMessage(),
                $this->_getLastJsonErrorCode()
            );
        }

        return $ret;
    }

    /**
     * Decode a JSON string with error checking
     *
     * @param string $data
     * @param bool $asArray
     * @throws Exception
     * @return mixed
     */
    protected function _jsonDecode($data, $asArray = true)
    {
        $ret = json_decode($data, $asArray);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(
                'Decoding error: ' . $this->_getLastJsonErrorMessage(),
                $this->_getLastJsonErrorCode()
            );
        }

        return $ret;
    }

    /**
     * Get last JSON error message
     *
     * @return string
     */
    protected function _getLastJsonErrorMessage()
    {
        $lastError = json_last_error();

        switch ($lastError) {
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
                break;
            default:
                return 'Unknown';
                break;
        }
    }


    /**
     * Get last JSON error code
     *
     * @return int|null
     */
    protected function _getLastJsonErrorCode()
    {
        return json_last_error();
    }
}
