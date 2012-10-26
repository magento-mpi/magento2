<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal MECL Shopping cart review xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Paypal_Mecl_Review extends Mage_Paypal_Block_Express_Review
{
    /**
     * Render PayPal MECL details xml
     *
     * @return string xml
     */
    protected function _toHtml()
    {
        /** @var $reviewXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $reviewXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<mecl_cart_details></mecl_cart_details>'));

        if ($this->getPaypalMessages()) {
            $reviewXmlObj->addChild('paypal_message', implode(PHP_EOL, $this->getPaypalMessages()));
        }

        if ($this->getShippingAddress()) {
            $reviewXmlObj->addCustomChild(
                'shipping_address',
                Mage::helper('Mage_XmlConnect_Helper_Data')->trimLineBreaks($this->getShippingAddress()->format('text')),
                array('label' => $this->__('Shipping Address'))
            );
        }

        if ($this->_quote->isVirtual()) {
            $reviewXmlObj->addCustomChild('shipping_method', null, array(
                'label' => $this->__('No shipping method required.')
            ));
        } elseif ($this->getCanEditShippingMethod() || !$this->getCurrentShippingRate()) {
            if ($groups = $this->getShippingRateGroups()) {
                $currentRate = $this->getCurrentShippingRate();
                foreach ($groups as $code => $rates) {
                    foreach ($rates as $rate) {
                        if ($currentRate === $rate) {
                            $reviewXmlObj->addCustomChild('shipping_method', null, array(
                                'rate' => strip_tags($this->renderShippingRateOption($rate)),
                                'label' => $this->getCarrierName($code)
                            ));
                            break(2);
                        }
                    }
                }
            }
        }
        $reviewXmlObj->addCustomChild('payment_method', $this->escapeHtml($this->getPaymentMethodTitle()), array(
            'label' => $this->__('Payment Method')
        ));

        $reviewXmlObj->addCustomChild(
            'billing_address',
            Mage::helper('Mage_XmlConnect_Helper_Data')->trimLineBreaks($this->getBillingAddress()->format('text')),
            array(
                'label'         => $this->__('Billing Address'),
                'payer_email'   => $this->__('Payer Email: %s', $this->getBillingAddress()->getEmail())
        ));

        $this->getChildBlock('details')->addDetailsToXmlObj($reviewXmlObj);

        return $reviewXmlObj->asNiceXml();
    }
}
