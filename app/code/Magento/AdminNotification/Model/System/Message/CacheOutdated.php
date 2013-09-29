<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Model\System\Message;

class CacheOutdated
    implements \Magento\AdminNotification\Model\System\MessageInterface
{
    /**
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Core\Model\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Core\Model\UrlInterface $urlBuilder
     * @param \Magento\Core\Model\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\AuthorizationInterface $authorization,
        \Magento\Core\Model\UrlInterface $urlBuilder,
        \Magento\Core\Model\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->_authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;
        $this->_cacheTypeList = $cacheTypeList;
    }

    /**
     * Get array of cache types which require data refresh
     *
     * @return array
     */
    protected function _getCacheTypesForRefresh()
    {
        $output = array();
        foreach ($this->_cacheTypeList->getInvalidated() as $type) {
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
        $cacheTypes = implode(', ', $this->_getCacheTypesForRefresh());
        $message = __('One or more of the Cache Types are invalidated: %1. ', $cacheTypes) . ' ';
        $url = $this->_urlBuilder->getUrl('adminhtml/cache');
        $message .= __('Please go to <a href="%1">Cache Management</a> and refresh cache types.', $url);
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
        return \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_CRITICAL;
    }
}
