<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote address attribute backend region resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Address_Attribute_Backend_Region
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set region to the attribute
     *
     * @param \Magento\Object $object
     * @return Magento_Sales_Model_Resource_Quote_Address_Attribute_Backend_Region
     */
    public function beforeSave($object)
    {
        if (is_numeric($object->getRegion())) {
            $region = Mage::getModel('Magento_Directory_Model_Region')->load((int)$object->getRegion());
            if ($region) {
                $object->setRegionId($region->getId());
                $object->setRegion($region->getCode());
            }
        }

        return $this;
    }
}
