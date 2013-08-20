<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute source model for enable/disable option
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Source_Boolean extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => __('Yes'),
                    'value' => 1
                ),
                array(
                    'label' => __('No'),
                    'value' => 0
                ),
                array(
                    'label' => __('Use config'),
                    'value' => 2
                )
            );
        }
        return $this->_options;
    }
}
