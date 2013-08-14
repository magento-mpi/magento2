<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminNotification_Model_System_Message_CacheOutdated
    implements Magento_AdminNotification_Model_System_MessageInterface
{
    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var Magento_Core_Model_Cache
     */
    protected $_cache;

    /**
     * @var Magento_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @param Magento_AuthorizationInterface $authorization
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Core_Model_Cache $cache
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(
        Magento_AuthorizationInterface $authorization,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Core_Model_Cache $cache,
        Magento_Core_Model_Factory_Helper $helperFactory
    ) {
        $this->_authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;
        $this->_cache = $cache;
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Get array of cache types which require data refresh
     *
     * @return array
     */
    protected function _getCacheTypesForRefresh()
    {
        $output = array();
        foreach ($this->_cache->getInvalidatedTypes() as $type) {
            $output[] = $type->getCacheType();
        }
        return $output;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5('cache' . implode(':', $this->_getCacheTypesForRefresh()));
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::cache')
            && count($this->_getCacheTypesForRefresh()) > 0;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        /** @var $helper Magento_AdminNotification_Helper_Data */
        $helper = $this->_helperFactory->get('Magento_AdminNotification_Helper_Data');
        $cacheTypes = implode(', ', $this->_getCacheTypesForRefresh());
        $message = $helper->__('One or more of the Cache Types are invalidated: %s. ', $cacheTypes) . ' ';
        $url = $this->_urlBuilder->getUrl('adminhtml/cache');
        $message .= $helper->__('Please go to <a href="%s">Cache Management</a> and refresh cache types.', $url);
        return $message;
    }

    /**
     * Retrieve problem management url
     *
     * @return string|null
     */
    public function getLink()
    {
        return $this->_urlBuilder->getUrl('adminhtml/cache');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return Magento_AdminNotification_Model_System_MessageInterface::SEVERITY_CRITICAL;
    }
}
