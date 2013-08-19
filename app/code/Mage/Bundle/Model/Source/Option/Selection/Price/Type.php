<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Extended Attributes Source Model
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Source_Option_Selection_Price_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => __('Fixed')),
            array('value' => '1', 'label' => __('Percent')),
        );
    }
}
