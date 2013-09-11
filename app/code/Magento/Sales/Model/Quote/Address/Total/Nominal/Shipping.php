<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal shipping total
 */
namespace Magento\Sales\Model\Quote\Address\Total\Nominal;

class Shipping extends \Magento\Sales\Model\Quote\Address\Total\Shipping
{
    /**
     * Don't add/set amounts
     * @var bool
     */
    protected $_canAddAmountToAddress = false;
    protected $_canSetAddressAmount   = false;

    /**
     * Custom row total key
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'shipping_amount';

    /**
     * Whether to get all address items when collecting them
     *
     * @var bool
     */
    protected $_shouldGetAllItems = false;

    /**
     * Collect shipping amount individually for each item
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return \Magento\Sales\Model\Quote\Address\Total\Nominal\Shipping
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        $items = $address->getAllNominalItems();
        if (!count($items)) {
            return $this;
        }

        // estimate quote with all address items to get their row weights
        $this->_shouldGetAllItems = true;
        parent::collect($address);
        $address->setCollectShippingRates(true);
        $this->_shouldGetAllItems = false;
        // now $items contains row weight information

        // collect shipping rates for each item individually
        foreach ($items as $item) {
            if (!$item->getProduct()->isVirtual()) {
                $address->requestShippingRates($item);
                $baseAmount = $item->getBaseShippingAmount();
                if ($baseAmount) {
                    $item->setShippingAmount($address->getQuote()->getStore()->convertPrice($baseAmount, false));
                }
            }
        }

        return $this;
    }

    /**
     * Don't fetch anything
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return array
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        return \Magento\Sales\Model\Quote\Address\Total\AbstractTotal::fetch($address);
    }

    /**
     * Get nominal items only or indeed get all items, depending on current logic requirements
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return array
     */
    protected function _getAddressItems(\Magento\Sales\Model\Quote\Address $address)
    {
        if ($this->_shouldGetAllItems) {
            return $address->getAllItems();
        }
        return $address->getAllNominalItems();
    }
}
