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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct(
            $context,
            $catalogData,
            $customerSession,
            $checkoutSession,
            $salesConfig,
            $data
        );
        $this->_isScopePrivate = true;
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
