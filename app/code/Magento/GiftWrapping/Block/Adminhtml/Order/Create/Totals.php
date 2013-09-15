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
 * Gift wrapping total block for admin checkout
 *
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\Create;

class Totals extends \Magento\Adminhtml\Block\Sales\Order\Create\Totals\DefaultTotals
{
    /**
     * Gift wrapping data
     *
     * @var Magento_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param Magento_GiftWrapping_Helper_Data $giftWrappingData
     * @param Magento_Sales_Helper_Data $salesData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GiftWrapping_Helper_Data $giftWrappingData,
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
