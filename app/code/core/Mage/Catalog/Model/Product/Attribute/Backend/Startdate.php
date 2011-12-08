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
 *
 * Speical Start Date attribute backend
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Catalog_Model_Product_Attribute_Backend_Startdate extends Mage_Eav_Model_Entity_Attribute_Backend_Datetime
{
    public function beforeSave($object)
    {
        $attributeName  = $this->getAttribute()->getName();
        $startDate      = $object->getData($attributeName);
        if ($startDate === false) {
            return $this;
        }
        if ($startDate == '' && $object->getSpecialPrice()) {
            $startDate = Mage::app()->getLocale()->date();
        }

        $object->setData($attributeName, $startDate);

        parent::beforeSave($object);
        return $this;
    }

}
