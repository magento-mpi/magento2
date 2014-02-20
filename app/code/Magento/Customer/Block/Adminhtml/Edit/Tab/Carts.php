<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Obtain all carts contents for specified client
 *
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

class Carts extends \Magento\Backend\Block\Template
{
    /** @var \Magento\Customer\Model\Config\Share */
    protected $_shareConfig;

    /**
     * @var \Magento\Customer\Service\V1\Dto\CustomerBuilder
     */
    protected $_customerBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context          $context
     * @param \Magento\Customer\Model\Config\Share             $shareConfig
     * @param \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\Config\Share $shareConfig,
        \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder,
        array $data = array()
    ) {
        $this->_shareConfig = $shareConfig;
        $this->_customerBuilder = $customerBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Add shopping cart grid of each website
     *
     * @return \Magento\Customer\Block\Adminhtml\Edit\Tab\Carts
     */
    protected function _prepareLayout()
    {
        $sharedWebsiteIds = $this->_shareConfig->getSharedWebsiteIds($this->getCustomer()->getWebsiteId());
        $isShared = count($sharedWebsiteIds) > 1;
        foreach ($sharedWebsiteIds as $websiteId) {
            $blockName = 'customer_cart_' . $websiteId;
            $block = $this->getLayout()->createBlock(
                'Magento\Customer\Block\Adminhtml\Edit\Tab\Cart',
                $blockName,
                array('data' => array('website_id' => $websiteId))
            );
            if ($isShared) {
                $websiteName = $this->_storeManager->getWebsite($websiteId)->getName();
                $block->setCartHeader(__('Shopping Cart from %1', $websiteName));
            }
            $this->setChild($blockName, $block);
        }
        return parent::_prepareLayout();
    }

    /**
     * Just get child blocks html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('adminhtml_block_html_before', array('block' => $this));
        return $this->getChildHtml();
    }

    /**
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    protected function getCustomer()
    {
        return $this->_customerBuilder
            ->populateWithArray($this->_backendSession->getCustomerData()['account'])->create();
    }
}
