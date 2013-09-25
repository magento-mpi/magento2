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
 * Catalog product weight backend attribute model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Weight extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     * Validate
     *
     * @param Magento_Catalog_Model_Product $object
     * @throws Magento_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!empty($value) && !Zend_Validate::is($value, 'Between', array('min' => 0, 'max' => 99999999.9999))) {
            throw new Magento_Core_Exception(
                __('Please enter a number 0 or greater in this field.')
            );
        }
        return true;
    }
}
