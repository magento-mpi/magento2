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
 * Paypal Direct payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Paypal extends Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
{
    /**
     * Return url model class for adminhtml
     *
     *  @return string
     */
    protected function _getUrlModelClass()
    {
        return 'Mage_Adminhtml_Model_Url';
    }

    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_WPP_DIRECT;

}
