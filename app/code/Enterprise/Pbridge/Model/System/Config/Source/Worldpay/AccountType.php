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
 * Worldpay Payment CC Types Source Model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_System_Config_Source_Worldpay_AccountType
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'business',
                'label' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Business')
            ),
            array(
                'value' => 'corporate',
                'label' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Corporate')
            )
        );
    }
}
