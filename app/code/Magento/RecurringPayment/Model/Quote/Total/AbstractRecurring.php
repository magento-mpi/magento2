<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Quote\Total;

/**
 * Total model for recurring payments
 */
abstract class AbstractRecurring
    extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Don't add amounts to address
     *
     * @var bool
     */
    protected $_canAddAmountToAddress = false;

    /**
     * By what key to set data into item
     *
     * @var string|null
     */
    protected $_itemRowTotalKey = null;

    /**
     * By what key to get data from payment
     *
     * @var string|null
     */
    protected $_paymentDataKey = null;

    /**
     * Collect recurring item parameters and copy to the address items
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        parent::collect($address);
        $items = $this->_getAddressItems($address);
        foreach ($items as $item) {
            if ($item->getProduct()->getIsRecurring()) {
                $paymentData = $item->getProduct()->getRecurringPayment();
                if (!empty($paymentData[$this->_paymentDataKey])) {
                    $item->setData(
                        $this->_itemRowTotalKey,
                        $address->getQuote()->getStore()->convertPrice($paymentData[$this->_paymentDataKey])
                    );
                    $this->_afterCollectSuccess($address, $item);
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
     * Get nominal items only
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return array
     */
    protected function _getAddressItems(\Magento\Sales\Model\Quote\Address $address)
    {
        return $address->getAllNominalItems();
    }

    /**
     * Hook for successful collecting of a recurring amount
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterCollectSuccess($address, $item)
    {
    }
}
