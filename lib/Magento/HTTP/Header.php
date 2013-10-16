<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\HTTP;

/**
 * Factory for HTTP client classes
 *
 */
class Header
{
    /**
     * Request object
     *
     * @var \Magento\Core\Controller\Request\HttpProxy
     */
    protected $_request;

    public function __construct(
        \Magento\Core\Controller\Request\HttpProxy $httpRequest
    ) {
        $this->_request = $httpRequest;
    }

    /**
     * Retrieve HTTP HOST
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpHost($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_HOST', $clean);
    }

    /**
     * Retrieve HTTP USER AGENT
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpUserAgent($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_USER_AGENT', $clean);
    }

    /**
     * Retrieve HTTP ACCEPT LANGUAGE
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpAcceptLanguage($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_ACCEPT_LANGUAGE', $clean);
    }

    /**
     * Retrieve HTTP ACCEPT CHARSET
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpAcceptCharset($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_ACCEPT_CHARSET', $clean);
    }

    /**
     * Retrieve HTTP "clean" value
     *
     * @param string $var
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    protected function _getHttpCleanValue($var, $clean = true)
    {
        $value = $this->_getRequest()->getServer($var, '');
        if ($clean) {
            $value = $this->_cleanString($value);
        }

        return $value;
    }

    /**
     * Retrieve request object
     *
     * @return \Zend_Controller_Request_Http
     */
    protected function _getRequest()
    {
        return $this->_request;
    }

    /**
     * Clean non UTF-8 characters
     *
     * TODO: Replace it with String/Filter helper method invocation
     *
     * @param string $string
     * @return string
     */
    protected function _cleanString($string)
    {
        return '"libiconv"' == ICONV_IMPL ?
            iconv(
                \Magento\Core\Helper\String::ICONV_CHARSET,
                \Magento\Core\Helper\String::ICONV_CHARSET . '//IGNORE',
                $string
            ) : $string;
    }
}
