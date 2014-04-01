<?php
/**
 * Url security information
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Url;

class SecurityInfo implements \Magento\Url\SecurityInfoInterface
{
    /**
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * List of secure url patterns
     *
     * @var array
     */
    protected $_secureUrlList = array();

    /**
     * List of already checked urls
     *
     * @var array
     */
    protected $_secureUrlCache = array();

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $secureUrlList
     */
    public function __construct(\Magento\App\Config\ScopeConfigInterface $scopeConfig, array $secureUrlList = array())
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_secureUrlList = $secureUrlList;
    }

    /**
     * Check whether url is secure
     *
     * @param string $url
     * @return bool
     */
    public function isSecure($url)
    {
        if (!$this->_scopeConfig->getValue(
            \Magento\Store\Model\Store::XML_PATH_SECURE_IN_FRONTEND,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return false;
        }

        if (!isset($this->_secureUrlCache[$url])) {
            $this->_secureUrlCache[$url] = false;
            foreach ($this->_secureUrlList as $match) {
                if (strpos($url, (string)$match) === 0) {
                    $this->_secureUrlCache[$url] = true;
                    break;
                }
            }
        }
        return $this->_secureUrlCache[$url];
    }
}
