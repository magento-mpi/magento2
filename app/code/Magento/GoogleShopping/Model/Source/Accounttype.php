<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Data Api account types Source
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Source_Accounttype implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve option array with account types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'HOSTED_OR_GOOGLE', 'label' => __('Hosted or Google')),
            array('value' => 'GOOGLE', 'label' => __('Google')),
            array('value' => 'HOSTED', 'label' => __('Hosted'))
        );
    }
}
