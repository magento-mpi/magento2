<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping total block for admin checkout
 *
 */
class Enterprise_GiftWrapping_Block_Adminhtml_Order_Create_Totals extends Magento_Adminhtml_Block_Sales_Order_Create_Totals_Default
{
    /**
     * Gift wrapping data
     *
     * @var Enterprise_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param Enterprise_GiftWrapping_Helper_Data $giftWrappingData
     * @param Magento_Sales_Helper_Data $salesData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_GiftWrapping_Helper_Data $giftWrappingData,
        Magento_Sales_Helper_Data $salesData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($salesData, $coreData, $context, $data);
    }

    /**
     * Return information for showing
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        $total = $this->getTotal();
        $totals = $this->_giftWrappingData->getTotals($total);
        foreach ($totals as $total) {
            $values[$total['label']] = $total['value'];
        }
        return $values;
    }
}
