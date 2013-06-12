<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form block for PayPal Express Checkout Permissions
 */
class Saas_Paypal_Block_Boarding_Express_Form extends Mage_Paypal_Block_Express_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING;

    /**
     * Set Permissions Pro model
     */
    public function _construct()
    {
        parent::_construct();
        $this->_config = Mage::getModel('Saas_Paypal_Model_Boarding_Config')->setMethod($this->getMethodCode());
    }
}
