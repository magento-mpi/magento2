<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Fieldset_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $fieldsets = array();
        $xpath = new DOMXPath($source);
        /** @var DOMNode $fieldset */
        foreach ($xpath->query('/config/scope') as $scope) {
            $scopeId = $scope->attributes->getNamedItem('id')->nodeValue;
            $fieldsets[$scopeId] = $this->_convertScope($scope);
        }
        return $fieldsets;
    }

    /**
     * Convert Scope node to Magento array
     *
     * @param DOMNode $node
     * @return array
     */
    protected function _convertScope($scope)
    {
        $result = array();
        if (!$scope->hasChildNodes()) {
            return $result;
        }
        foreach ($scope->childNodes as $fieldset) {
            if (!$fieldset instanceof DOMElement) {
                continue;
            }
            $fieldsetName = $fieldset->attributes->getNamedItem('id')->nodeValue;
            $result[$fieldsetName] = $this->_convertFieldset($fieldset);
        }
        return $result;
    }

    /**
     * Convert Fieldset node to Magento array
     *
     * @param DOMNode $node
     * @return array
     */
    protected function _convertFieldset($fieldset)
    {
        $result = array();
        if (!$fieldset->hasChildNodes()) {
            return $result;
        }
        foreach ($fieldset->childNodes as $field) {
            if (!$field instanceof DOMElement) {
                continue;
            }
            $fieldName = $field->attributes->getNamedItem('name')->nodeValue;
            $result[$fieldName] = $this->_convertField($field);
        }
        return $result;
    }

    /**
     * Convert Field node to Magento array
     *
     * @param DOMNode $node
     * @return array
     */
    protected function _convertField($field)
    {
        $result = array();
        if (!$field->hasChildNodes()) {
            return $result;
        }
        foreach ($field->childNodes as $aspect) {
            if (!$aspect instanceof DOMElement) {
                continue;
            }
            /** @var DOMNamedNodeMap $aspectdAttributes */
            $aspectdAttributes = $aspect->attributes;
            $aspectName = $aspectdAttributes->getNamedItem('name')->nodeValue;
            $targetField = $aspectdAttributes->getNamedItem('targetField');
            $result[$aspectName] = is_null($targetField) ? '*' : $targetField->nodeValue;
        }
        return $result;
    }
}
