<?php
/**
 * Attributes configuration converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Eav_Model_Entity_Attribute_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert config
     *
     * @param mixed $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();

        /** @var DOMNodeList $entities */
        $entities = $source->getElementsByTagName('entity');

        /** @var DOMNode $entity */
        foreach ($entities as $entity) {
            $entityConfig = array();
            $attributes = array();

            /** @var DOMNode $entityAttribute */
            foreach ($entity->getElementsByTagName('attribute') as $entityAttribute) {
                $attributeFields = array();
                foreach ($entityAttribute->getElementsByTagName('field') as $fieldData) {
                    $attributeFields[$fieldData->attributes->getNamedItem('code')->nodeValue] = array(
                        'code' => $fieldData->attributes->getNamedItem('code')->nodeValue,
                        'locked' => (bool)$fieldData->attributes->getNamedItem('locked')->nodeValue
                    );
                }
                $attributes[$entityAttribute->attributes->getNamedItem('code')->nodeValue] = $attributeFields;
            }
            $entityConfig['attributes'] = $attributes;
            $output[$entity->attributes->getNamedItem('type')->nodeValue] = $entityConfig;
        }

        return $output;
    }
}