<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping adminhtml sales order view items
 *
 * @category   Magento
 * @package    Magento_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftWrapping_Block_Adminhtml_Sales_Order_View_Link extends Magento_Backend_Block_Template
{
    /**
     * @var Magento_GiftWrapping_Model_WrappingFactory
     */
    protected $_wrappingFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_GiftWrapping_Model_WrappingFactory $wrappingFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_GiftWrapping_Model_WrappingFactory $wrappingFactory,
        array $data = array()
    ) {
        $this->_wrappingFactory = $wrappingFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get order item from parent block
     *
     * @return Magento_Sales_Model_Order_Item
     */
    public function getItem()
    {
        return $this->getParentBlock()->getItem();
    }

    /**
     * Get gift wrapping design
     *
     * @return string
     */
    public function getDesign()
    {
        if ($this->getItem()->getGwId()) {
            $wrappingModel = $this->_wrappingFactory->create()->load($this->getItem()->getGwId());
            if ($wrappingModel->getId()) {
                return $this->escapeHtml($wrappingModel->getDesign());
            }
        }
        return '';
    }

    /**
     * Check ability to display gift wrapping for order items
     *
     * @return bool
     */
    public function canDisplayGiftWrappingForItem()
    {
        return $this->getItem()->getGwId() && $this->getDesign();
    }
}
