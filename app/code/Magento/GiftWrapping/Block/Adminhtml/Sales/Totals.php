<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Sales;

/**
 * Gift Wrapping Adminhtml Block
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Totals extends \Magento\View\Element\Template
{
    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($context, $data);
    }

    /**
     * Initialize gift wrapping and printed card totals for order/invoice/creditmemo
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source  = $parent->getSource();
        $totals = $this->_giftWrappingData->getTotals($source);
        foreach ($totals as $total) {
            $this->getParentBlock()->addTotalBefore(new \Magento\Object($total), 'tax');
        }
        return $this;
    }
}
