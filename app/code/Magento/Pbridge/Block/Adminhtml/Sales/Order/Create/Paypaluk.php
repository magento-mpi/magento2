<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Paypal UK Direct payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Paypaluk extends Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Paypal
{
    /**
     * Paypal UK payment code
     *
     * @var string
     */
    protected $_code = Magento_Paypal_Model_Config::METHOD_WPP_PE_DIRECT;
}
