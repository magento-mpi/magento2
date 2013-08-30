<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_PaypalUk_Block_Express_Form extends Magento_Paypal_Block_Express_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Magento_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;

}
