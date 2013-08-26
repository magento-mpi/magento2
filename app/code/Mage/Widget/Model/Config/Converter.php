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
     * {@inheritdoc}
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
            $translate = $widgetAttributes->getNamedItem('translate');
            if (!is_null($translate)) {
                $widgetArray['@']['translate'] = $translate->nodeValue;
            }

            $isEmailCompatible = $widgetAttributes->getNamedItem('is_email_compatible');
            if (!is_null($isEmailCompatible)) {
                $widgetArray['is_email_compatible'] = ($isEmailCompatible->nodeValue == 'true');
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
                    case "#text": break;
                    case '#comment': break;
                    default:
                        throw new LogicException(sprintf("Unsupported child xml node '%s' found in the 'widget' node",
                            $widgetSubNode->nodeName));
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
     * @throws LogicException
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
            if ($containerTemplate->nodeName !== 'template') {
                throw new LogicException("Only 'template' node can be child of 'container' node");
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
     * @throws LogicException
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
                        if (!isset($parameter['values'])) {
                            $parameter['values'] = array();
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
            if (((string)$visible->nodeValue) == 'false') {
                $parameter['visible'] = '0';
            } else {
                $parameter['visible'] = '1';
            }
        } else {
            $parameter['visible'] = '1';
        }
        $required = $sourceAttributes->getNamedItem('required');
        if ($required) {
            if (((string)$required->nodeValue) == 'false') {
                $parameter['required'] = '0';
            } else {
                $parameter['required'] = '1';
            }
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
                case 'value' :
                    $parameter['value'] = $paramSubNode->nodeValue;
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
     * @throws LogicException
     */
    protected function _convertDepends($source)
    {
        $depends = array();
        foreach ($source->childNodes as $childNode) {
            if ($childNode->nodeName == '#text') {
                continue;
            }
            if ($childNode->nodeName !== 'parameter') {
                throw new LogicException(sprintf("Only 'parameter' node can be child of 'depends' node, %s found",
                    $childNode->nodeName));
            }
            $parameterAttributes = $childNode->attributes;
            $depends[$parameterAttributes->getNamedItem('name')->nodeValue] =
                array('value' => $parameterAttributes->getNamedItem('value')->nodeValue);
        }
        return $depends;
    }

    /**
     * Convert dom Renderer node to magneto array
     *
     * @param DOMNode $source
     * @return array
     * @throws LogicException
     */
    protected function _convertRenderer($source)
    {
        $helperBlock = array();
        $helperBlock['type'] = $source->attributes->getNamedItem('class')->nodeValue;
        foreach ($source->childNodes as $rendererSubNode) {
            if ($rendererSubNode->nodeName == '#text') {
                continue;
            }
            if ($rendererSubNode->nodeName !== 'data') {
                throw new LogicException(sprintf("Only 'data' node can be child of 'renderer' node, %s found",
                    $rendererSubNode->nodeName));
            }
            $helperBlock['data'] = $this->_convertData($rendererSubNode);
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
                    $data = $dataChild->nodeValue;
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
        $data = array('@' => array());
        if (!$source->hasAttributes()) {
            return array();
        }
        $attributes = $source->attributes;
        foreach ($attributes as $attribute) {
            $data['@'][$attribute->nodeName] = $attribute->nodeValue;
        }
        return $data;
    }

    /**
     * Convert dom Option node to magneto array
     *
     * @param DOMNode $source
     * @return array
     * @throws LogicException
     */
    protected function _convertOptions($source)
    {
        $option = array('@' => array());
        $optionAttributes = $source->attributes;
        $translate = $optionAttributes->getNamedItem('translate');
        if (!is_null($translate)) {
            $option['@']['translate'] = $translate->nodeValue;
        }
        $option['value'] = $optionAttributes->getNamedItem('value')->nodeValue;
        foreach ($source->childNodes as $childNode) {
            if ($childNode->nodeName == '#text') {
                continue;
            }
            if ($childNode->nodeName !== 'label') {
                throw new LogicException("Only 'label' node can be child of 'option' node");
            }
            $option['label'] = $childNode->nodeValue;
        }
        return $option;
    }
}
