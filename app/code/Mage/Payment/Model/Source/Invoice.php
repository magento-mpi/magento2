<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Automatic invoice create source model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payment_Model_Source_Invoice
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('Magento_Core_Helper_Data')->__('Yes')
            ),
            array(
                'value' => '',
                'label' => Mage::helper('Magento_Core_Helper_Data')->__('No')
            ),
        );
    }
}
