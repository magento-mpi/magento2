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
 * Gift wrapping total block for checkout
 *
 */
namespace Magento\GiftWrapping\Block\Checkout;

class Totals extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * Template file path
     *
     * @var string
     */
    protected $_template = 'checkout/totals.phtml';

    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($catalogData, $coreData, $context, $data);
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
