<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Eav_Model_Entity_Attribute_Config extends Magento_Config_Data
{
    /**
     * @param Magento_Eav_Model_Entity_Attribute_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Eav_Model_Entity_Attribute_Config_Reader $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = "eav_attributes"
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Retrieve list of locked fields for attribute
     *
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return array
     */
    public function getLockedFields(Magento_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        return $this->get($attribute->getEntityType()->getEntityTypeCode() . '/' . $attribute->getAttributeCode());
    }
}
