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
        $allFields = $this->get(
            $attribute->getEntityType()->getEntityTypeCode() . '/attributes/' . $attribute->getAttributeCode()
        );

        $lockedFields = array();
        foreach ($allFields as $fieldCode => $fieldConfig) {
            $lockedFields[$fieldCode] = $fieldCode;
        }

        return $lockedFields;
    }

    /**
     * Retrieve attributes list with config for entity
     *
     * @param string $entityCode
     * @return array
     */
    public function getEntityAttributesLockedFields($entityCode)
    {
        $lockedFields = array();

        $entityAttributes = $this->get($entityCode . '/attributes');
        foreach ($entityAttributes as $attributeCode => $attributeData) {
            foreach ($attributeData as $attributeField) {
                if ($attributeField['locked']) {
                    $lockedFields[$attributeCode][] = $attributeField['code'];
                }
            }
        }

        return $lockedFields;
    }
}
