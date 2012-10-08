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
 * Check / Money order Payment method xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Purchaseorder extends Mage_Payment_Block_Form_Purchaseorder
{
    /**
     * Prevent any rendering
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '';
    }

    /**
     * Retrieve payment method model
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethod()
    {
        $method = $this->getData('method');
        if (!$method) {
            $method = Mage::getModel('Mage_Payment_Model_Method_Purchaseorder');
            $this->setData('method', $method);
        }

        return $method;
    }

    /**
     * Add payment method form to payment XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $paymentItemXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addPaymentFormToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $paymentItemXmlObj)
    {
        $method = $this->getMethod();
        if (!$method) {
            return $paymentItemXmlObj;
        }
        $formXmlObj = $paymentItemXmlObj->addChild('form');
        $formXmlObj->addAttribute('name', 'payment_form_' . $method->getCode());
        $formXmlObj->addAttribute('method', 'post');

        $poNumber = $this->getInfoData('po_number');
        $poNumberText = $this->__('Purchase Order Number');
        $xml = <<<EOT
<fieldset>
    <field name="payment[po_number]" type="text" label="{$poNumberText}" value="$poNumber" required="true" />
</fieldset>
EOT;
        $fieldsetXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => $xml));
        $formXmlObj->appendChild($fieldsetXmlObj);

        return $paymentItemXmlObj;
    }
}
