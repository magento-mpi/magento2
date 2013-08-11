<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute source model for enable/disable option
 *
 * @category   Mage
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Source_Boolean extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
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
                    'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Yes'),
                    'value' => 1
                ),
                array(
                    'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('No'),
                    'value' => 0
                ),
                array(
                    'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Use config'),
                    'value' => 2
                )
            );
        }
        return $this->_options;
    }
}
