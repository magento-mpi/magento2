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

    public function setVary($name, $value)
    {
        if (is_array($value)) {
            $value = serialize($value);
        }
        setcookie('VARY_' . strtoupper($name), $value);
    }
}
