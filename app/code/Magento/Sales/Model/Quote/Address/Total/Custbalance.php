<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote\Address\Total;

class Custbalance extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        $address->setCustbalanceAmount(0);
        $address->setBaseCustbalanceAmount(0);

        $address->setGrandTotal($address->getGrandTotal() - $address->getCustbalanceAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $address->getBaseCustbalanceAmount());

        return $this;
    }
}
