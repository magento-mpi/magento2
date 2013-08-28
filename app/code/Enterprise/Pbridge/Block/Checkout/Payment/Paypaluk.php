<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Paypal UK Direct payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Checkout_Payment_Paypaluk extends Enterprise_Pbridge_Block_Checkout_Payment_Paypal
{
    /**
     * Paypal UK payment code
     *
     * @var string
     */
    protected $_code = Magento_Paypal_Model_Config::METHOD_WPP_PE_DIRECT;
}
