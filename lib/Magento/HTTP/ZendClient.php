<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_HTTP
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Magento HTTP Client
 *
 * @category   Magento
 * @package    Magento_HTTP
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\HTTP;

class ZendClient extends \Zend_Http_Client
{
    /**
     * Internal flag to allow decoding of request body
     *
     * @var bool
     */
    protected $_urlEncodeBody = true;

    public function __construct($uri = null, $config = null)
    {
        $this->config['useragent'] = 'Magento\HTTP\ZendClient';

        parent::__construct($uri, $config);
    }

    protected function _trySetCurlAdapter()
    {
        if (extension_loaded('curl')) {
            $this->setAdapter(new \Magento\HTTP\Adapter\Curl());
        }
        return $this;
    }

    public function request($method = null)
    {
        $this->_trySetCurlAdapter();
        return parent::request($method);
    }

    /**
     * Change value of internal flag to disable/enable custom prepare functionality
     *
     * @param bool $flag
     * @return \Magento\HTTP\ZendClient
     */
    public function setUrlEncodeBody($flag)
    {
        $this->_urlEncodeBody = $flag;
        return $this;
    }

    /**
     * Adding custom functionality to decode data after
     * standard prepare functionality
     *
     * @return string
     */
    protected function _prepareBody()
    {
        $body = parent::_prepareBody();

        if (!$this->_urlEncodeBody && $body) {
            $body = urldecode($body);
            $this->setHeaders('Content-length', strlen($body));
        }

        return $body;
    }
}
