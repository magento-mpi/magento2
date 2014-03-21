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
     * @var \Magento\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\Http\Context $httpContext,
        array $data = array()
    ) {
        $this->httpContext = $httpContext;
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
        $storeId = (int)$this->getRequest()->getParam('store_id');
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
            $customerGroupId = $this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_GROUP);
        }
        return $customerGroupId;
    }
}
