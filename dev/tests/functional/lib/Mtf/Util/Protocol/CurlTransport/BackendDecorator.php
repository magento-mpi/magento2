<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Protocol\CurlTransport;

use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\System\Config;

/**
 * Class BackendDecorator
 */
class BackendDecorator implements CurlInterface
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
        $this->_transport = $transport;
        $this->_configuration = $configuration;
        $this->_authorize();
    }

    /**
     * Authorize customer on backend
     */
    protected function _authorize()
    {
        $credentials = $this->_configuration->getConfigParam('application/backend_user_credentials');
        $url = $_ENV['app_backend_url'] . $this->_configuration->getConfigParam('application/backend_login_url');
        $data = [
            'login[username]' => $credentials['login'],
            'login[password]' => $credentials['password']
        ];
        $this->_transport->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $this->read();
        if (strpos($response, 'page-login')) {
            throw new \Exception('Admin user cannot be logged in by curl handler!');
        }
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
     * Send request to the remote server
     *
     * @param string $method
     * @param string $url
     * @param string $httpVer
     * @param array $headers
     * @param array $params
     * @return void
     * @throws \Exception
     */
    public function write($method, $url, $httpVer = '1.1', $headers = [], $params = [])
    {
        if ($this->_formKey) {
            $params['form_key'] = $this->_formKey;
        } else {
            throw new \Exception('Form key is absent! Response: ' . $this->_response);
        }
        $this->_transport->write($method, $url, $httpVer, $headers, http_build_query($params));
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

    /**
     * Add additional option to cURL
     *
     * @param  int $option      the CURLOPT_* constants
     * @param  mixed $value
     */
    public function addOption($option, $value)
    {
        $this->_transport->addOption($option, $value);
    }

    /**
     * Close the connection to the server
     */
    public function close()
    {
        $this->_transport->close();
    }
}
