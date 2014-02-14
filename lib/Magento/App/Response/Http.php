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

class Http extends \Zend_Controller_Response_Http implements HttpInterface
{
    /**
     * Cookie to store page vary string
     */
    const COOKIE_VARY_STRING = 'X-Magento-Vary';

    /**
     * Response vary identifiers
     *
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
     * Set vary identifier
     *
     * @param string $name
     * @param string|array $value
     * @return $this
     */
    public function setVary($name, $value)
    {
        if (is_array($value)) {
            $value = serialize($value);
        }
        $this->vary[$name] = $value;
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
        if (!empty($this->vary)) {
            ksort($this->vary);
        }

        return sha1(serialize($this->vary));
    }
}
