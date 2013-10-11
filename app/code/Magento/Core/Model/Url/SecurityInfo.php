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

class SecurityInfo implements \Magento\Core\Model\Url\SecurityInfoInterface
{
    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
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
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $secureUrlList
     */
    public function __construct(\Magento\Core\Model\StoreManagerInterface $storeManager, array $secureUrlList = array())
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
        if (!$this->_storeManager->getStore()->getConfig(\Magento\Core\Model\Store::XML_PATH_SECURE_IN_FRONTEND)) {
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
