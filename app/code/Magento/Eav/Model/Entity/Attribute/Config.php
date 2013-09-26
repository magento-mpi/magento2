<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute;

class Config extends \Magento\Config\Data
{
    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = "eav_attributes"
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Retrieve list of locked fields for attribute
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @return array
     */
    public function getLockedFields(\Magento\Catalog\Model\Resource\Eav\Attribute $attribute)
    {
        $allFields = $this->get(
            $attribute->getEntityType()->getEntityTypeCode() . '/attributes/' . $attribute->getAttributeCode()
        );

        $lockedFields = array();
        foreach (array_keys($allFields) as $fieldCode) {
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
