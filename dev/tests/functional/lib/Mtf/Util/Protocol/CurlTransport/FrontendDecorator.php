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
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Framework\Data\Form\FormKey;

/**
 * Class FrontendDecorator
 */
class FrontendDecorator implements CurlInterface
{
    /**
     * @var \Mtf\Util\Protocol\CurlTransport
     */
    protected $_transport;

    /**
     * @var string
     */
    protected $_formKey = null;

    /**
     * @var string
     */
    protected $_cookies = '';

    /**
     * @var string
     */
    protected $_response;

    /**
     * @var FormKey
     */
    protected $form_key;

    /**
     * Constructor
     *
     * @param CurlTransport $transport
     * @param CustomerInjectable $customer
     */
    public function __construct(CurlTransport $transport, CustomerInjectable $customer)
    {
        $this->_transport = $transport;
        $this->_authorize($customer);
    }

    /**
     * Authorize customer on backend
     *
     * @param CustomerInjectable $customer
     * @throws \Exception
     * @return void
     */
    protected function _authorize(CustomerInjectable $customer)
    {
        $url = $_ENV['app_frontend_url'] . 'customer/account/login/';
        $this->_transport->write(CurlInterface::POST, $url);
        $this->read();
        $url = $_ENV['app_frontend_url'] . 'customer/account/loginPost/';
        $data = [
            'login[username]' => $customer->getEmail(),
            'login[password]' => $customer->getPassword(),
            'form_key' => $this->_formKey
        ];
        $this->_transport->write(CurlInterface::POST, $url, '1.0', [], $data, $this->_cookies);
        $response = $this->read();
        if (strpos($response, 'customer/account/login')) {
            throw new \Exception($customer->getFirstname() . ', cannot be logged in by curl handler!');
        }
    }

    /**
     * Init Form Key from response
     */
    protected function _initFormKey()
    {
        $str = substr($this->_response, strpos($this->_response, 'form_key'));
        preg_match('/value="(.*)" \/>/', $str, $matches);
        if (!empty($matches[1])) {
            $this->_formKey = $matches[1];
        }
    }

    /**
     * Init Cookies from response
     */
    protected function _initCookies()
    {
        preg_match_all('|Set-Cookie: (.*);|U', $this->_response, $matches);
        if (!empty($matches[1])) {
            $this->_cookies = implode('; ', $matches[1]);
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
     * @param string $cookie
     * @return void
     */
    public function write($method, $url, $httpVer = '1.1', $headers = [], $params = [], $cookie = '')
    {
        $this->_transport->write($method, $url, $httpVer, $headers, http_build_query($params), $this->_cookies);
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        $this->_response = $this->_transport->read();
        $this->_initCookies();
        $this->_initFormKey();
        return $this->_response;
    }

    /**
     * Add additional option to cURL
     *
     * @param  int $option the CURLOPT_* constants
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
