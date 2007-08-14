<?php
/**
 * HTTP CURL Adapter
 *
 * @package     Mage
 * @subpackage  Http
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
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
        if (isset($this->_config['timeout'])) {
            curl_setopt($this->_getResource(), CURLOPT_TIMEOUT, $this->_config['timeout']);
        }
        if (isset($this->_config['maxredirects'])) {
            curl_setopt($this->_getResource(), CURLOPT_MAXREDIRS, $this->_config['maxredirects']);
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
}
