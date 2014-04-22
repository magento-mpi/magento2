<?php
/**
 * Attributes configuration converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
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

        /** @var \DOMNodeList $entities */
        $entities = $source->getElementsByTagName('entity');

        /** @var DOMNode $entity */
        foreach ($entities as $entity) {
            $entityConfig = array();
            $attributes = array();

            /** @var DOMNode $entityAttribute */
            foreach ($entity->getElementsByTagName('attribute') as $entityAttribute) {
                $attributeFields = array();
                foreach ($entityAttribute->getElementsByTagName('field') as $fieldData) {
                    $locked = $fieldData->attributes->getNamedItem('locked')->nodeValue == "true" ? true : false;
                    $attributeFields[$fieldData->attributes->getNamedItem(
                        'code'
                    )->nodeValue] = array(
                        'code' => $fieldData->attributes->getNamedItem('code')->nodeValue,
                        'locked' => $locked
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
