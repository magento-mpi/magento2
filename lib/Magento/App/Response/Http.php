<?php
/**
 * HTTP response
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Response;

class Http extends \Zend_Controller_Response_Http implements \Magento\App\ResponseInterface
{

    /**
     * Cookie to store page vary string
     */
    const COOKIE_VARY_STRING = 'VARY_STRING';

    /**
     * @var array
     */
    protected $vary;

    /**
     * Get header value by name.
     * Returns first found header by passed name.
     * If header with specified name was not found returns false.
     *
     * @param string $name
     * @return array|bool
     */
    public function getHeader($name)
    {
        foreach ($this->_headers as $header) {
            if ($header['name'] == $name) {
                return $header;
            }
        }
        return false;
    }

    /**
     * Set vary
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function setVary($name, $value)
    {
        if (!empty($value)) {
            if (is_array($value)) {
                $value = serialize($value);
            }
            $this->vary[$name] = $value;
        }
        return $this;
    }

    /**
     * Send the response, including all headers, rendering exceptions if so
     * requested.
     *
     * @return void
     */
    public function sendResponse()
    {
        setcookie(self::COOKIE_VARY_STRING, $this->getVaryString(), null, '/');
        parent::sendResponse();
    }

    /**
     * Returns hash of varies
     *
     * @return string
     */
    public function getVaryString()
    {
        return sha1(serialize($this->vary));
    }
}
