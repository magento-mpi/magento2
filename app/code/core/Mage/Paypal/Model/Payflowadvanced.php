<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payments Advanced gateway model
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Paypal_Model_Payflowadvanced extends Mage_Paypal_Model_Payflowlink
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED;

    /**
     * Type of block that generates method form
     *
     * @var string
     */
    protected $_formBlockType = 'Mage_Paypal_Block_Payflow_Advanced_Form';

    /**
     * Type of block that displays method information
     *
     * @var string
     */
    protected $_infoBlockType = 'Mage_Paypal_Block_Payflow_Advanced_Info';

    /**
     * Instantiate state and set it to state object
     * PayflowAdvanced should always use full authorization amount
     *
     * @param string $paymentAction
     * @param Varien_Object $stateObject
     * @return Mage_Paypal_Model_Payflowadvanced
     */
    public function initialize($paymentAction, $stateObject)
    {
        $payment = $this->getInfoInstance();

        $salesDocument = $payment->getOrder();
        if (!$salesDocument) {
            $salesDocument = $payment->getQuote();
            $amount = $salesDocument->getBaseGrandTotal();
        } else {
            $amount = $salesDocument->getBaseTotalDue();
        }

        $this->_initialize($payment, $amount);

        $payment->setAdditionalInformation('authorization_amount',
            Mage_Paypal_Model_Config::AUTHORIZATION_AMOUNT_FULL);

        return $this;
    }
}
