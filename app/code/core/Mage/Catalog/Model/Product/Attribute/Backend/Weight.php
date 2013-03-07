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
 * Catalog product weight backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Weight extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     * Validate
     *
     * @param Mage_Catalog_Model_Product $object
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        $filterOptions = array('options' => array('min_range' => 0));
        if (!filter_var($object->getData('weight'), FILTER_VALIDATE_INT, $filterOptions)) {
            Mage::throwException(
                Mage::helper('Mage_Catalog_Helper_Data')->__('Please enter a number 0 or greater in this field.')
            );
        }
        return true;
    }
}
