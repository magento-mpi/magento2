<?php
/**
 * Url security information
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Url_SecurityInfo
{
    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

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
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param array $secureUrlList
     */
    public function __construct(Magento_Core_Model_StoreManager $storeManager, array $secureUrlList = array())
    {
        $this->_storeManager = $storeManager;
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
        if (!$this->_storeManager->getStore()->getConfig(Magento_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND)) {
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
