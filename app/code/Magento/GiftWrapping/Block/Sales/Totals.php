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
 * Customer balance block for order
 *
 */
class Magento_GiftWrapping_Block_Sales_Totals extends Magento_Core_Block_Template
{
    /**
     * Gift wrapping data
     *
     * @var Magento_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param Magento_GiftWrapping_Helper_Data $giftWrappingData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GiftWrapping_Helper_Data $giftWrappingData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize gift wrapping and printed card totals for order/invoice/creditmemo
     *
     * @return Magento_GiftWrapping_Block_Sales_Totals
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source  = $parent->getSource();
        $totals = $this->_giftWrappingData->getTotals($source);
        foreach ($totals as $total) {
            $this->getParentBlock()->addTotalBefore(new Magento_Object($total), 'tax');
        }
        return $this;
    }
}
