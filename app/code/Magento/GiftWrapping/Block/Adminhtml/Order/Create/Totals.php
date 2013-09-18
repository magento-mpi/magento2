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
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($salesData, $coreData, $context, $coreConfig, $data);
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
