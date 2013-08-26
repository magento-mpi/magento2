<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Widget_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert dom node tree to magneto array config
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $widgets = array();
        $xpath = new DOMXPath($source);
        $xpath->registerNamespace('x', $source->lookupNamespaceUri($source->namespaceURI));
        /** @var $widget DOMNode */
        foreach ($xpath->query('/x:widgets/x:widget') as $widget) {
            $widgetAttributes = $widget->attributes;
            $widgetArray = array('@' => array());
            $widgetArray['@']['type'] = $widgetAttributes->getNamedItem('class')->nodeValue;
            $widgetArray['@']['module'] = $widgetAttributes->getNamedItem('module')->nodeValue;
            $isEmailCompatible = $widgetAttributes->getNamedItem('is_email_compatible');
            if (!is_null($isEmailCompatible)) {
                $widgetArray['is_email_compatible'] = $isEmailCompatible->nodeValue;
            }
            $placeholderImage = $widgetAttributes->getNamedItem('placeholder_image');
            if (!is_null($placeholderImage)) {
                $widgetArray['placeholder_image'] = $placeholderImage->nodeValue;
            }
            $widgetId = $widgetAttributes->getNamedItem('id');
            /** @var $widgetSubNode DOMNode */
            foreach ($widget->childNodes as $widgetSubNode) {
                switch ($widgetSubNode->nodeName) {
                    case 'label':
                        $widgetArray['name'] = $widgetSubNode->nodeValue;
                        break;
                    case 'description':
                        $widgetArray['description'] = $widgetSubNode->nodeValue;
                        break;
                    case 'parameter':
                        $subNodeAttributes = $widgetSubNode->attributes;
                        $parameterName = $subNodeAttributes->getNamedItem('name')->nodeValue;
                        $widgetArray['parameters'][$parameterName] = $this->_convertParameter($widgetSubNode);
                        break;
                    case 'container':
                        if (!isset($widgetArray['supported_containers'])) {
                            $widgetArray['supported_containers'] = array();
                        }
                        $widgetArray['supported_containers'] = array_merge($widgetArray['supported_containers'],
                            $this->_convertContainer($widgetSubNode));
                        break;
                }
            }
            $widgets[$widgetId->nodeValue] = $widgetArray;
        }
        return $widgets;
    }


    /**
     * Convert dom Container node to magneto array
     *
     * @param DOMNode $source
     * @return array
     */
    protected function _convertContainer($source)
    {
        $supportedContainers = array();
        $containerAttributes = $source->attributes;
        $template = array();
        foreach ($source->childNodes as $containerTemplate) {
            if (!$containerTemplate instanceof DOMElement) {
                continue;
            }
            $templateAttributes = $containerTemplate->attributes;
            $template[$templateAttributes->getNamedItem('name')->nodeValue] =
                $templateAttributes->getNamedItem('value')->nodeValue;
        }
        $supportedContainers[$containerAttributes->getNamedItem('section')->nodeValue] = array(
            'container_name' => $containerAttributes->getNamedItem('name')->nodeValue,
            'template' => $template
        );
        return $supportedContainers;
    }

    /**
     * Convert dom Parameter node to magneto array
     *
     * @param DOMNode $source
     * @return array
     */
    protected function _convertParameter($source)
    {
        $parameter = array();
        $sourceAttributes = $source->attributes;
        $xsiType = $sourceAttributes->getNamedItem('type')->nodeValue;
        if ($xsiType == 'value_renderer') {
            $parameter['type'] = 'label';
            $parameter['@'] = array();
            $parameter['@']['type'] = 'complex';
            foreach ($source->childNodes as $rendererSubNode) {
                switch ($rendererSubNode->nodeName) {
                    case 'renderer':
                        $parameter['helper_block'] = $this->_convertRenderer($rendererSubNode);
                        break;
                }
            }
        } else if ($xsiType == 'select' || $xsiType == 'multiselect') {
            $sourceModel = $sourceAttributes->getNamedItem('source_model');
            if (!is_null($sourceModel)) {
                $parameter['source_model'] = $sourceModel->nodeValue;
            }
            $parameter['type'] = $xsiType;
            $parameter['values'] = array();
            /** @var $paramSubNode DOMNode */
            foreach ($source->childNodes as $paramSubNode) {
                switch ($paramSubNode->nodeName) {
                    case 'option':
                        $optionAttributes = $paramSubNode->attributes;
                        $optionName = $optionAttributes->getNamedItem('name')->nodeValue;
                        $selected = $optionAttributes->getNamedItem('selected');
                        if (!is_null($selected)) {
                            $parameter['value'] = $optionAttributes->getNamedItem('value')->nodeValue;
                        }
                        $parameter['values'][$optionName] = $this->_convertOptions($paramSubNode);
                        break;
                }
            }

        } else {
            $parameter['type'] = $xsiType;
        }
        $visible = $sourceAttributes->getNamedItem('visible');
        if ($visible) {
            $parameter['visible'] = $visible->nodeValue;
        } else {
            $parameter['visible'] = 'true';
        }
        $required = $sourceAttributes->getNamedItem('required');
        if ($required) {
            $parameter['required'] = $required->nodeValue;
        }
        $translate = $sourceAttributes->getNamedItem('translate');
        if ($translate) {
            if (!isset($parameter['@'])) {
                $parameter['@'] = array();
            }
            $parameter['@']['translate'] = $translate->nodeValue;
        }
        $sortOrder = $sourceAttributes->getNamedItem('sort_order');
        if ($sortOrder) {
            $parameter['sort_order'] = $sortOrder->nodeValue;
        }
        foreach ($source->childNodes as $paramSubNode) {
            switch ($paramSubNode->nodeName) {
                case 'label':
                    $parameter['label'] = $paramSubNode->nodeValue;
                    break;
                case 'description':
                    $parameter['description'] = $paramSubNode->nodeValue;
                    break;
                case 'depends':

                    $parameter['depends'] = $this->_convertDepends($paramSubNode);
                    break;
            }
        }
        return $parameter;
    }

    /**
     * Convert dom Depends node to magneto array
     *
     * @param DOMNode $source
     * @return array
     */
    protected function _convertDepends($source)
    {
        $depends = array();
        foreach ($source->childNodes as $childNode) {
            switch ($childNode->nodeName) {
                case 'parameter':
                    $parameterAttributes = $childNode->attributes;
                    $depends[$parameterAttributes->getNamedItem('name')->nodeValue] =
                        $parameterAttributes->getNamedItem('value')->nodeValue;
            }
        }
        return $depends;
    }

    /**
     * Convert dom Renderer node to magneto array
     *
     * @param DOMNode $source
     * @return array
     */
    protected function _convertRenderer($source)
    {
        $helperBlock = array();
        $helperBlock['type'] = $source->attributes->getNamedItem('class')->nodeValue;
        foreach ($source->childNodes as $rendererSubNode) {
            switch ($rendererSubNode->nodeName) {
                case 'data':
                    $helperBlock['data'] = $this->_convertData($rendererSubNode);//$this->_compressElements($rendererSubNode);
                    break;
            }
        }

        return $helperBlock;
    }

    /**
     * Convert dom Data node to magneto array
     *
     * @param DOMElement $source
     * @return array
     */
    protected function _convertData($source)
    {
        $data = $this->_getAttributes($source);
        if (!$source->hasChildNodes()) {
            return $data;
        }
        foreach ($source->childNodes as $dataChild) {
            if ($dataChild instanceof DOMElement) {
                $data[$dataChild->tagName] = $this->_convertData($dataChild);
            } else {
                if (strlen(trim($dataChild->nodeValue))) {
                    $data[$source->tagName] = $dataChild->nodeValue;
                }
            }
        }
        return $data;
    }

    /**
     * Convert dom Data node to magneto array
     *
     * @param DOMElement $source
     * @return array
     */
    protected function _getAttributes($source)
    {
        $data = array();
        if (!$source->hasAttributes()) {
            return;
        }
        $attributes = $source->attributes;
        foreach ($attributes as $attribute) {
            $data[$attribute->nodeName] = $attribute->nodeValue;
        }
        return $data;
    }

    /**
     * Convert dom Option node to magneto array
     *
     * @param DOMNode $source
     * @return array
     */
    protected function _convertOptions($source)
    {
        $option = array('@' => array());
        $optionAttributes = $source->attributes;
        $translate = $optionAttributes->getNamedItem('translate');
        if (!is_null($translate)) {
            $option['@']['translate'] = $translate->nodeValue;
        }
        $option['@']['value'] = $optionAttributes->getNamedItem('value')->nodeValue;
        foreach ($source->childNodes as $childNode) {
            switch ($childNode->nodeName) {
                case 'label':
                    $option['label'] = $childNode->nodeValue;
                    break;
            }
        }
        return $option;
    }
}
