<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for baseprice amount attribute
 *
 * @category   Saas
 * @package    Saas_UnitPrice
 */
class Saas_UnitPrice_Model_Entity_Backend_Unitprice_Amount
    extends Magento_Eav_Model_Entity_Attribute_Backend_Default
{

    /**
     * Before save method
     *
     * @param Magento_Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);
        if ($data) {
            if (!is_numeric($data)) {
                $object->setData($attributeCode, sprintf('%01.2f', $data));
            }
        }

        return parent::beforeSave($object);
    }
}
