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
class Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Paypaluk
    extends Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Paypal
{
    /**
     * Paypal UK payment code
     *
     * @var string
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_WPP_PE_DIRECT;

    /**
     * Return 3D validation flag
     * @return bool
     */
    public function is3dSecureEnabled()
    {
        return (bool)$this->getMethod()->getConfigData('centinel')
            && (bool)$this->getMethod()->getConfigData('centinel_backend');
    }
}
