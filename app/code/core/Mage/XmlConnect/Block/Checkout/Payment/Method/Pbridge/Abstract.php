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
 * Abstract Pbridge Payment method xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Block_Checkout_Payment_Method_Pbridge_Abstract
    extends Enterprise_Pbridge_Block_Payment_Form_Abstract
{
    /**
     * Retrieve payment method model
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethod()
    {
        $method = $this->getData('method');
        if (!$method) {
            $method = Mage::getModel('Enterprise_Pbridge_Model_Payment_Method_' . $this->_model);
            $this->setData('method', $method);
        }
        return $method;
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('xmlconnect/pbridge/result', array('_current' => true, '_secure' => true));
    }

    /**
     * Add payment method through Pbridge iframe XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $paymentItemXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addPaymentFormToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $paymentItemXmlObj)
    {
        $paymentItemXmlObj->addAttribute('is_pbridge', 1);
        $paymentItemXmlObj->addChild('pb_iframe', $paymentItemXmlObj->xmlentities($this->createIframe()));
        return $paymentItemXmlObj;
    }

    /**
     * Create html page with iframe for devices
     *
     * @return string html
     */
    protected function createIframe()
    {
        $code = $this->getMethodCode();
        $body = <<<EOT
<div id="payment_form_{$code}" style="margin:0 auto; max-width:500px;">
    {$this->getIframeBlock()->toHtml()}
</div>
EOT;
        return $this->helper('Mage_XmlConnect_Helper_Data')->htmlize($body);
    }
}
