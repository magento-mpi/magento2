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

        $output = array_merge_recursive($output, $this->_getStaticAttributes($source));
        $output = array_merge_recursive($output, $this->_getCustomAttributes($source));

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
            $attributeGroupSortOrder = $attributeGroup->getAttribute('sort_order');
            $attributeGroupIsVisible = $attributeGroup->getAttribute('visible');

            if ($attributeGroupName === null) {
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
                'sortOrder' => $attributeGroupSortOrder,
                'visible' => $attributeGroupIsVisible,
                'label'     => $labelText
            );
        }

        return $result;
    }

    /**
     * Get all static attributes for registry and registrant
     *
     * @param DOMDocument $source
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getStaticAttributes($source)
    {
        $registry = array();
        $registrant = array();

        /** @var DOMNodeList $staticAttributes */
        $staticAttributes = $source->getElementsByTagName('static_attribute');

        /** @var DOMElement $staticAttribute */
        foreach ($staticAttributes as $staticAttribute) {
            $parentNode = $staticAttribute->parentNode->tagName;

            $attributeName = $staticAttribute->getAttribute('name');

            if ($attributeName === null) {
                throw new InvalidArgumentException('Attribute "name" of one of "static_attribute"s does not exist');
            }

            /** @var @var DOMElement $label */
            $label = $staticAttribute->getElementsByTagName('label')->item(0);
            $translateLabel = $label->getAttribute('translate');
            if ($translateLabel === 'true') {
                $labelText = __($label->firstChild->nodeValue);
            } else {
                $labelText = $label->firstChild->nodeValue;
            }

            $attribute = array(
                'type' => $staticAttribute->getAttribute('type'),
                'visible' => $staticAttribute->getAttribute('visible'),
                'group' => $staticAttribute->getAttribute('group'),
                'label' => $labelText
            );

            if ($parentNode == 'registry') {
                $registry['static_attributes'][$attributeName] = $attribute;
            } elseif ($parentNode == 'registrant') {
                $registrant['static_attributes'][$attributeName] = $attribute;
            }
        }

        $result = array(
            'registry' => $registry,
            'registrant' => $registrant
        );

        return $result;
    }

    /**
     * Get all custom attributes for registry and registrant
     *
     * @param DOMDocument $source
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getCustomAttributes($source)
    {
        $registry = array();
        $registrant = array();

        /** @var DOMNodeList $customAttributes */
        $customAttributes = $source->getElementsByTagName('custom_attribute');

        /** @var DOMElement $customAttribute */
        foreach ($customAttributes as $customAttribute) {
            $parentNode = $customAttribute->parentNode->tagName;

            $attributeName = $customAttribute->getAttribute('name');

            if ($attributeName === null) {
                throw new InvalidArgumentException('Attribute "name" of one of "custom_attribute"s does not exist');
            }

            /** @var @var DOMElement $label */
            $label = $customAttribute->getElementsByTagName('label')->item(0);
            $translateLabel = $label->getAttribute('translate');
            if ($translateLabel === 'true') {
                $labelText = __($label->firstChild->nodeValue);
            } else {
                $labelText = $label->firstChild->nodeValue;
            }

            $attribute = array(
                'type' => $customAttribute->getAttribute('type'),
                'visible' => $customAttribute->getAttribute('visible'),
                'group' => $customAttribute->getAttribute('group'),
                'label' => $labelText
            );

            if ($parentNode == 'registry') {
                $registry['custom_attributes'][$attributeName] = $attribute;
            } elseif ($parentNode == 'registrant') {
                $registrant['custom_attributes'][$attributeName] = $attribute;
            }
        }

        $result = array(
            'registry' => $registry,
            'registrant' =>$registrant
        );

        return $result;
    }
}
