<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Block;

class AbstractBlock extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Get store id
     *
     * @return int
     */
    protected function _getStoreId()
    {
        $storeId =   (int) $this->getRequest()->getParam('store_id');
        if ($storeId == null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Get customer group id
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        $customerGroupId =   (int) $this->getRequest()->getParam('cid');
        if ($customerGroupId == null) {
            $customerGroupId = $this->_customerSession->getCustomerGroupId();
        }
        return $customerGroupId;
    }
}
