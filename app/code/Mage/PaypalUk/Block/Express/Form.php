<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_PaypalUk_Block_Express_Form extends Mage_Paypal_Block_Express_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;

}
