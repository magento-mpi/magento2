<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Catalog Product Attributes Backend Model
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 */
class Enterprise_TargetRule_Model_Catalog_Product_Attribute_Backend_Rule
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Before attribute save prepare data
     *
     * @param Magento_Catalog_Model_Product $object
     * @return Enterprise_TargetRule_Model_Catalog_Product_Attribute_Backend_Rule
     */
    public function beforeSave($object)
    {
        $attributeName  = $this->getAttribute()->getName();
        $useDefault     = $object->getData($attributeName . '_default');

        if ($useDefault == 1) {
            $object->setData($attributeName, null);
        }

        return $this;
    }
}
