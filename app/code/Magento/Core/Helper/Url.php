<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core URL helper
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Helper;

class Url extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve current url in base64 encoding
     *
     * @return string
     */
    public function getCurrentBase64Url()
    {
        return $this->urlEncode($this->_urlBuilder->getCurrentUrl());
    }

    /**
     * @param string $url
     * @return string
     */
    public function getEncodedUrl($url = null)
    {
        if (!$url) {
            $url = $this->_urlBuilder->getCurrentUrl();
        }
        return $this->urlEncode($url);
    }

    /**
     * Retrieve homepage url
     *
     * @return string
     */
    public function getHomeUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @param string $string
     * @return string
     */
    protected function _prepareString($string)
    {
        $string = preg_replace('#[^0-9a-z]+#i', '-', $string);
        $string = strtolower($string);
        $string = trim($string, '-');

        return $string;
    }

    /**
     * Add request parameter into url
     *
     * @param  string $url
     * @param  array $param array( 'key' => value )
     * @return string
     */
    public function addRequestParam($url, $param)
    {
        $startDelimiter = (false === strpos($url, '?'))? '?' : '&';

        $arrQueryParams = array();
        foreach ($param as $key=>$value) {
            if (is_numeric($key) || is_object($value)) {
                continue;
            }

            if (is_array($value)) {
                // $key[]=$value1&$key[]=$value2 ...
                $arrQueryParams[] = $key . '[]=' . implode('&' . $key . '[]=', $value);
            } elseif (is_null($value)) {
                $arrQueryParams[] = $key;
            } else {
                $arrQueryParams[] = $key . '=' . $value;
            }
        }
        $url .= $startDelimiter . implode('&', $arrQueryParams);

        return $url;
    }

    /**
     * Remove request parameter from url
     * @param string $url
     * @param string $paramKey
     * @param bool $caseSensitive
     * @return string
     */
    public function removeRequestParam($url, $paramKey, $caseSensitive = false)
    {
        $regExpression = '/\\?[^#]*?(' . preg_quote($paramKey, '/') . '\\=[^#&]*&?)/' . ($caseSensitive ? '' : 'i');
        while (preg_match($regExpression, $url, $matches) !== 0) {
            $paramString = $matches[1];
            // if ampersand is at the end of $paramString
            if (substr($paramString, -1, 1) != '&') {
                $url = preg_replace('/(&|\\?)?' . preg_quote($paramString, '/') . '/', '', $url);
            } else {
                $url = str_replace($paramString, '', $url);
            }
        }
        return $url;
    }
}
