<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Converts gift registry attributes from DOMDocument to array
 */
class Magento_GiftRegistry_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Converting data to array type
     *
     * @param mixed $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();

        if (!$source instanceof DOMDocument) {
            return $output;
        }

        $output['attribute_types'] = $this->_getAttributeTypes($source);
        $output['attribute_groups'] = $this->_getAttributeGroups($source);

        $output = array_merge_recursive($output, $this->_getAttributes($source, 'static_attribute'));
        $output = array_merge_recursive($output, $this->_getAttributes($source, 'custom_attribute'));

        return $output;
    }

    /**
     * Get attribute types from config xml
     *
     * @param DOMDocument $source
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getAttributeTypes($source)
    {
        $result = array();

        /** @var DOMNodeList $attributeTypes */
        $attributeTypes = $source->getElementsByTagName('attribute_type');

        /** @var DOMElement $attributeType */
        foreach ($attributeTypes as $attributeType) {

            $attributeTypeName = $attributeType->getAttribute('name');

            if (!$attributeTypeName) {
                throw new InvalidArgumentException('Attribute "name" of one of "attribute_type"s does not exist');
            }
            /** @var @var DOMElement $label */
            $label = $attributeType->getElementsByTagName('label')->item(0);
            $translateLabel = $label->getAttribute('translate');
            if ($translateLabel === 'true') {
                $labelText = __($label->firstChild->nodeValue);
            } else {
                $labelText = $label->firstChild->nodeValue;
            }

            $result[$attributeTypeName] = array('label' => $labelText);
        }

        return $result;
    }

    /**
     * Get attribute groups from config xml
     *
     * @param DOMDocument $source
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getAttributeGroups($source)
    {
        $result = array();

        /** @var DOMNodeList $attributeGroups */
        $attributeGroups = $source->getElementsByTagName('attribute_group');

        /** @var DOMElement $attributeGroup */
        foreach ($attributeGroups as $attributeGroup) {

            $attributeGroupName = $attributeGroup->getAttribute('name');
            $groupSortOrder = $attributeGroup->getAttribute('sort_order');
            $groupIsVisible = $attributeGroup->getAttribute('visible');

            if (!$attributeGroupName) {
                throw new InvalidArgumentException('Attribute "name" of one of "attribute_group"s does not exist');
            }

            /** @var @var DOMElement $label */
            $label = $attributeGroup->getElementsByTagName('label')->item(0);
            $translateLabel = $label->getAttribute('translate');
            if ($translateLabel === 'true') {
                $labelText = __($label->firstChild->nodeValue);
            } else {
                $labelText = $label->firstChild->nodeValue;
            }

            $result[$attributeGroupName] = array(
                'sortOrder' => $groupSortOrder,
                'visible' => $groupIsVisible,
                'label'     => $labelText
            );
        }

        return $result;
    }

    /**
     * Get all static|custom attributes for registry and registrant
     * depending on the $typeOfAttributes param
     *
     * @param DOMDocument $source
     * @param string $typeOfAttributes
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getAttributes($source, $typeOfAttributes)
    {
        $registry = array();
        $registrant = array();

        /** @var DOMNodeList $staticAttributes */
        $attributes = $source->getElementsByTagName($typeOfAttributes);

        /** @var DOMElement $staticAttribute */
        foreach ($attributes as $attribute) {
            $parentNode = $attribute->parentNode->tagName;

            $attributeName = $attribute->getAttribute('name');

            if (!$attributeName) {
                throw new InvalidArgumentException('Attribute "name" of one of "static_attribute"s does not exist');
            }

            /** @var @var DOMElement $label */
            $label = $attribute->getElementsByTagName('label')->item(0);
            $translateLabel = $label->getAttribute('translate');
            if ($translateLabel === 'true') {
                $labelText = __($label->firstChild->nodeValue);
            } else {
                $labelText = $label->firstChild->nodeValue;
            }

            $attributeArray = array(
                'type' => $attribute->getAttribute('type'),
                'visible' => $attribute->getAttribute('visible'),
                'group' => $attribute->getAttribute('group'),
                'label' => $labelText
            );

            if ($parentNode == 'registry') {
                $registry[$typeOfAttributes . 's'][$attributeName] = $attributeArray;
            } elseif ($parentNode == 'registrant') {
                $registrant[$typeOfAttributes . 's'][$attributeName] = $attributeArray;
            }
        }

        $result = array(
            'registry' => $registry,
            'registrant' => $registrant
        );

        return $result;
    }
}
