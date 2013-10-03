<?php
/**
 * Client for invoking REST API
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\TestCase\Webapi\Adapter\Rest;

class CurlClient
{
    /**
     * @var string REST URL base path
     */
    const REST_BASE_PATH = '/rest/';

    /**
     * @var array JSON Error code to error message mapping
     */
    protected $_jsonErrorMessages = array(
        JSON_ERROR_DEPTH => 'Maximum depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'State mismatch',
        JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
        JSON_ERROR_SYNTAX => 'Syntax error, invalid JSON'
    );

    /**
     * Perform HTTP GET request
     *
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    public function get($resourcePath, $data = array(), $headers = array())
    {
        $url = $this->constructResourceUrl($resourcePath);
        if (!empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        $curlOpts = array();
        $curlOpts[CURLOPT_CUSTOMREQUEST] = \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET;
        $resp = $this->_invokeApi($url, $curlOpts, $headers);
        $respArray = $this->_jsonDecode($resp["body"]);
        return $respArray;
    }

    /**
     * Perform HTTP POST request
     *
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    public function post($resourcePath, $data, $headers = array())
    {
        return $this->_postOrPut($resourcePath, $data, false, $headers);
    }

    /**
     * Perform HTTP PUT request
     *
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    public function put($resourcePath, $data, $headers = array())
    {
        return $this->_postOrPut($resourcePath, $data, true, $headers);
    }

    /**
     * Perform HTTP DELETE request
     *
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @param array $headers
     * @return mixed
     */
    public function delete($resourcePath, $headers = array())
    {
        $url = $this->constructResourceUrl($resourcePath);

        $curlOpts = array();
        $curlOpts[CURLOPT_CUSTOMREQUEST] = \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE;

        $resp = $this->_invokeApi($url, $curlOpts, $headers);
        $respArray = $this->_jsonDecode($resp["body"]);

        return $respArray;
    }

    /**
     * Perform HTTP POST or PUT request
     *
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @param array $data
     * @param boolean $put Set true to post data as HTTP PUT operation (update). If this value is set to false,
     *        HTTP POST (create) will be used
     * @param array $headers
     * @return mixed
     */
    protected function _postOrPut($resourcePath, $data, $put = false, $headers = array())
    {
        $url = $this->constructResourceUrl($resourcePath);

        // json encode data
        $jsonData = $this->_jsonEncode($data);

        $curlOpts = array();
        $curlOpts[CURLOPT_CUSTOMREQUEST] = $put
            ? \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT : \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST;
        $headers[] = 'Content-Length: ' . strlen($jsonData);
        $curlOpts[CURLOPT_POSTFIELDS] = $jsonData;

        $resp = $this->_invokeApi($url, $curlOpts, $headers);
        $respArray = $this->_jsonDecode($resp["body"]);

        return $respArray;
    }

    /**
     * @param string $resourcePath Resource URL like /V1/Resource1/123
     * @return string resource URL
     * @throws \Exception
     */
    public function constructResourceUrl($resourcePath)
    {
        return rtrim(TESTS_BASE_URL, '/') . self::REST_BASE_PATH . ltrim($resourcePath, '/');
    }

    /**
     * Makes the REST api call using passed $curl object
     *
     * @param string $url
     * @param array $additionalCurlOpts cURL Options
     * @param array $headers
     * @return array
     * @throws \Exception
     */
    protected function _invokeApi($url, $additionalCurlOpts, $headers = array())
    {
        // initialize cURL
        $curl = curl_init($url);
        if ($curl === false) {
            throw new \Exception("Error Initializing cURL for baseUrl: " . $url);
        }

        // get cURL options
        $curlOpts = $this->_getCurlOptions($additionalCurlOpts, $headers);

        // add CURL opts
        foreach ($curlOpts as $opt => $val) {
            curl_setopt($curl, $opt, $val);
        }

        $resp = array();
        $resp["body"] = curl_exec($curl);
        if ($resp["body"] === false) {
            throw new \Exception(curl_error($curl));
        }

        $resp["meta"] = curl_getinfo($curl);
        if ($resp["meta"] === false) {
            throw new \Exception(curl_error($curl));
        }

        curl_close($curl);

        $meta = $resp["meta"];
        if ($meta && $meta['http_code'] >= 400) {
            throw new \Exception ($resp["body"], $meta['http_code']);
        }

        return $resp;
    }

    /**
     * Constructs and returns a curl options array
     *
     * @param array $customCurlOpts Additional / overridden cURL options
     * @param array $headers
     * @return array
     */
    protected function _getCurlOptions($customCurlOpts = array(), $headers = array())
    {
        // default curl options
        $curlOpts = array(
            CURLOPT_RETURNTRANSFER => true, // return result instead of echoing
            CURLOPT_SSL_VERIFYPEER => false, // stop cURL from verifying the peer's certificate
            CURLOPT_FOLLOWLOCATION => false, // follow redirects, Location: headers
            CURLOPT_MAXREDIRS => 10, // but don't redirect more than 10 times
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json'
            )
        );

        // merge headers
        $headers = array_merge($curlOpts[CURLOPT_HTTPHEADER], $headers);
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;

        // merge custom Curl Options & return
        foreach ($customCurlOpts as $opt => $val) {
            $curlOpts[$opt] = $val;
        }

        return $curlOpts;
    }

    /**
     * JSON encode with error checking
     *
     * @param mixed $data
     * @return string
     * @throws \Exception
     */
    protected function _jsonEncode($data)
    {
        $ret = json_encode($data);
        $this->_checkJsonError();

        // return the json String
        return $ret;
    }

    /**
     * Decode a JSON string with error checking
     *
     * @param string $data
     * @param bool $asArray
     * @throws \Exception
     * @return mixed
     */
    protected function _jsonDecode($data, $asArray = true)
    {
        $ret = json_decode($data, $asArray);
        $this->_checkJsonError();

        // return the array
        return $ret;
    }

    /**
     * Checks for JSON error in the latest encoding / decoding and throws an exception in case of error
     * @throws \Exception
     */
    protected function _checkJsonError()
    {
        $jsonError = json_last_error();
        if ($jsonError !== JSON_ERROR_NONE) {
            // find appropriate error message
            $message = 'Unknown JSON Error';
            if (isset($this->_jsonErrorMessages[$jsonError])) {
                $message = $this->_jsonErrorMessages[$jsonError];
            }

            throw new \Exception('JSON Encoding / Decoding error: ' . $message, $jsonError);
        }
    }
}
