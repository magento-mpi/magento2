<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Sales;

/**
 * Gift Wrapping Adminhtml Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        array $data = []
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
        $source = $parent->getSource();
        $totals = $this->_giftWrappingData->getTotals($source);
        foreach ($totals as $total) {
            $this->getParentBlock()->addTotalBefore(new \Magento\Framework\Object($total), 'tax');
        }
        return $this;
    }
}
