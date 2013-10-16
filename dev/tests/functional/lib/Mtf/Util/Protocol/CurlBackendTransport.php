<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Protocol;

use Mtf\Util\Protocol\CurlTransport;
use Mtf\System\Config;

/**
 * Class BackendTransportDecorator
 */
class CurlBackendTransport
{
    /**
     * @var \Mtf\Util\Protocol\CurlTransport
     */
    protected $_transport;

    /**
     * @var \Mtf\System\Config
     */
    protected $_configuration;

    /**
     * @var string
     */
    protected $_formKey = null;

    /**
     * @var array
     */
    protected $_data;

    /**
     * @var string
     */
    protected $_response;

    /**
     * Constructor
     *
     * @param CurlTransport $transport
     * @param Config $configuration
     */
    public function __construct(CurlTransport $transport, Config $configuration)
   {
       $this->_transport        = $transport;
       $this->_configuration    = $configuration;
       $this->_authorize();
   }

    /**
     * Authorize customer on backend
     */
    protected function _authorize()
    {
        $credentials        = $this->_configuration->getConfigParam('application/backend_user_credentials');
        $backendLoginUrl    = $_ENV['app_backend_url'] . $this->_configuration->getConfigParam('application/backend_login_url');
        $data = array(
            'login[username]' => $credentials['login'],
            'login[password]' => $credentials['password']
        );
        $this->_transport->write(CurlTransport::POST, $backendLoginUrl, '1.0', array(), $data);
        $this->read();
    }

    /**
     * Init Form Key from response
     */
    protected function _initFormKey()
    {
        preg_match('!var FORM_KEY = \'(\w+)\';!', $this->_response, $matches);
        if (!empty($matches[1])) {
            $this->_formKey = $matches[1];
        }
    }

    /**
     * Prepare Curl Data
     */
    protected function _prepareData()
    {
        if ($this->_formKey) {
            $this->_data['form_key'] = $this->_formKey;
        }
    }

    /**
     * Send request to the remote server
     *
     * @param string $method
     * @param string $url
     * @param string $http_ver
     * @param array $headers
     * @param array $body
     * @return string Request as text
     */
    public function write($method, $url, $http_ver = '1.1', $headers = array(), $body = array())
    {
        $this->_data = $body;
        $this->_prepareData();
        return $this->_transport->write($method, $url, $http_ver, $headers, $body);
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        $this->_response = $this->_transport->read();
        $this->_initFormKey();
        return $this->_response;
    }
}