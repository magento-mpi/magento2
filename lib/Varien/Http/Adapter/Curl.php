<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Http
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * HTTP CURL Adapter
 *
 * @category   Varien
 * @package    Varien_Http
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Http_Adapter_Curl implements Zend_Http_Client_Adapter_Interface
{
    /**
     * Parameters array
     *
     * @var array
     */
    protected $_config = array();

    protected $_resource;

    /**
     * Apply current configuration array to transport resource
     */
    protected function _applyConfig()
    {
        //curl_setopt();
        if (isset($this->_config['timeout'])) {
            curl_setopt($this->_getResource(), CURLOPT_TIMEOUT, $this->_config['timeout']);
        }
        if (isset($this->_config['maxredirects'])) {
            curl_setopt($this->_getResource(), CURLOPT_MAXREDIRS, $this->_config['maxredirects']);
        }
        if (isset($this->_config['proxy'])) {
            curl_setopt ($this->_getResource(), CURLOPT_PROXY, $this->_config['proxy']);
        }
        if (isset($this->_config['ssl_cert'])) {
            curl_setopt($this->_getResource(), CURLOPT_SSLCERT, $this->_config['ssl_cert']);
        }

        return $this;
    }

    /**
     * Set the configuration array for the adapter
     *
     * @param array $config
     */
    public function setConfig($config = array())
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param int     $port
     * @param boolean $secure
     */
    public function connect($host, $port = 80, $secure = false)
    {
        //curl_setopt();
        if (isset($this->_config['timeout'])) {
            curl_setopt($this->_getResource(), CURLOPT_TIMEOUT, $this->_config['timeout']);
        }
        if (isset($this->_config['maxredirects'])) {
            curl_setopt($this->_getResource(), CURLOPT_MAXREDIRS, $this->_config['maxredirects']);
        }
        if (isset($this->_config['proxy'])) {
            curl_setopt ($this->_getResource(), CURLOPT_PROXY, $this->_config['proxy']);
        }

        return $this;
    }

    /**
     * Send request to the remote server
     *
     * @param string        $method
     * @param Zend_Uri_Http $url
     * @param string        $http_ver
     * @param array         $headers
     * @param string        $body
     * @return string Request as text
     */
    public function write($method, $url, $http_ver = '1.1', $headers = array(), $body = '')
    {
        if ($url instanceof Zend_Uri_Http) {
            $url = $url->getUri();
        }
        $this->_applyConfig();

        // set url to post to
        curl_setopt($this->_getResource(), CURLOPT_URL, $url);
        curl_setopt($this->_getResource(), CURLOPT_RETURNTRANSFER, true);
        if ($method == Zend_Http_Client::POST) {
            curl_setopt($this->_getResource(), CURLOPT_POST, true);
            curl_setopt($this->_getResource(), CURLOPT_POSTFIELDS, $body);
        }
        elseif ($method == Zend_Http_Client::GET) {
            curl_setopt($this->_getResource(), CURLOPT_HTTPGET, true);
        }

        if( is_array($headers) ) {
            curl_setopt($this->_getResource(), CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($this->_getResource(), CURLOPT_HEADER, true);
        curl_setopt($this->_getResource(), CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->_getResource(), CURLOPT_SSL_VERIFYHOST, 0);


        return $body;
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        $response = curl_exec($this->_getResource());

        // Remove 100 and 101 responses headers
        if (Zend_Http_Response::extractCode($response) == 100 ||
            Zend_Http_Response::extractCode($response) == 101) {
            $response = preg_split('/^\r?$/m', $response, 2);
            $response = trim($response[1]);
        }

        return $response;
    }

    /**
     * Close the connection to the server
     *
     */
    public function close()
    {
        curl_close($this->_getResource());
        $this->_resource = null;
        return $this;
    }

    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = curl_init();
        }
        return $this->_resource;
    }

    public function getErrno()
    {
        return curl_errno($this->_getResource());
    }

    public function getError()
    {
        return curl_error($this->_getResource());
    }

    /**
     * Get information regarding a specific transfer
     *
     * @param int $opt CURLINFO option
     * @return mixed
     */
    public function getInfo($opt = 0)
    {
        return curl_getinfo($this->_getResource(), $opt);
    }

    /**
     * curl_multi_* requests support
     *
     * @param array $urls
     * @param array $options
     * @return array
     */
    public function multiRequest($urls, $options = array())
    {
        $handles = array();
        $result  = array();

        $multihandle = curl_multi_init();

        foreach ($urls as $key => $url) {
            $handles[$key] = curl_init();
            curl_setopt($handles[$key], CURLOPT_URL,            $url);
            curl_setopt($handles[$key], CURLOPT_HEADER,         0);
            curl_setopt($handles[$key], CURLOPT_RETURNTRANSFER, 1);
            if (!empty($options)) {
                curl_setopt_array($handles[$key], $options);
            }
            curl_multi_add_handle($multihandle, $handles[$key]);
        }
        $process = null;
        do {
            curl_multi_exec($multihandle, $process);
            usleep(100);
        } while ($process>0);

        foreach ($handles as $key => $handle) {
            $result[$key] = curl_multi_getcontent($handle);
            curl_multi_remove_handle($multihandle, $handle);
        }
        curl_multi_close($multihandle);
        return $result;
    }
}
