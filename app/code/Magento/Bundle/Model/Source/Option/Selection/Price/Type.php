<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Extended Attributes Source Model
 *
 * @category   Magento
 * @package    Magento_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Model_Source_Option_Selection_Price_Type implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => __('Fixed')),
            array('value' => '1', 'label' => __('Percent')),
        );
    }
}
