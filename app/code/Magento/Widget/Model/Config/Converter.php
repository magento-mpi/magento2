<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Widget_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convert($source)
    {
        $widgets = array();
        $xpath = new DOMXPath($source);
        /** @var $widget DOMNode */
        foreach ($xpath->query('/widgets/widget') as $widget) {
            $widgetAttributes = $widget->attributes;
            $widgetArray = array('@' => array());
            $widgetArray['@']['type'] = $widgetAttributes->getNamedItem('class')->nodeValue;
            $translate = $widgetAttributes->getNamedItem('translate');
            if (!is_null($translate)) {
                $widgetArray['@']['translate'] = $translate->nodeValue;
            }

            $isEmailCompatible = $widgetAttributes->getNamedItem('is_email_compatible');
            if (!is_null($isEmailCompatible)) {
                $widgetArray['is_email_compatible'] = $isEmailCompatible->nodeValue == 'true' ? '1' : '0';
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
                        $widgetArray['supported_containers'] = array_merge(
                            $widgetArray['supported_containers'],
                            $this->_convertContainer($widgetSubNode)
                        );
                        break;
                    case "#text": 
                        break;
                    case '#comment': 
                        break;
                    default:
                        throw new LogicException(
                            sprintf(
                                "Unsupported child xml node '%s' found in the 'widget' node",
                                $widgetSubNode->nodeName
                            )
                        );
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
     * Convert dom Parameter node to Magento array
     *
     * @param DOMNode $source
     * @return array
     * @throws LogicException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _convertParameter($source)
    {
        $parameter = array();
        $sourceAttributes = $source->attributes;
        $xsiType = $sourceAttributes->getNamedItem('type')->nodeValue;
        if ($xsiType == 'block') {
            $parameter['type'] = 'label';
            $parameter['@'] = array();
            $parameter['@']['type'] = 'complex';
            foreach ($source->childNodes as $blockSubNode) {
                if ($blockSubNode->nodeName == 'block') {
                    $parameter['helper_block'] = $this->_convertBlock($blockSubNode);
                    break;
                }
            }
        } elseif ($xsiType == 'select' || $xsiType == 'multiselect') {
            $sourceModel = $sourceAttributes->getNamedItem('source_model');
            if (!is_null($sourceModel)) {
                $parameter['source_model'] = $sourceModel->nodeValue;
            }
            $parameter['type'] = $xsiType;

            /** @var $paramSubNode DOMNode */
            foreach ($source->childNodes as $paramSubNode) {
                if ($paramSubNode->nodeName == 'option') {
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
                }
            }
        } elseif ($xsiType == 'text') {
            $parameter['type'] = $xsiType;
            foreach ($source->childNodes as $textSubNode) {
                if ($textSubNode->nodeName == 'value') {
                    $parameter['value'] = $textSubNode->nodeValue;
                }
            }
        } else {
            $parameter['type'] = $xsiType;
        }
        $visible = $sourceAttributes->getNamedItem('visible');
        if ($visible) {
            $parameter['visible'] = $visible->nodeValue == 'true' ? '1' : '0';
        } else {
            $parameter['visible'] = true;
        }
        $required = $sourceAttributes->getNamedItem('required');
        if ($required) {
            $parameter['required'] = $required->nodeValue == 'false' ? '0' : '1';
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
     * Convert dom Depends node to Magento array
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
                throw new LogicException(
                    sprintf(
                        "Only 'parameter' node can be child of 'depends' node, %s found",
                        $childNode->nodeName
                    )
                );
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
    protected function _convertBlock($source)
    {
        $helperBlock = array();
        $helperBlock['type'] = $source->attributes->getNamedItem('class')->nodeValue;
        foreach ($source->childNodes as $blockSubNode) {
            if ($blockSubNode->nodeName == '#text') {
                continue;
            }
            if ($blockSubNode->nodeName !== 'data') {
                throw new LogicException(
                    sprintf(
                        "Only 'data' node can be child of 'block' node, %s found",
                        $blockSubNode->nodeName
                    )
                );
            }
            $helperBlock['data'] = $this->_convertData($blockSubNode);
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
        $data = array();
        if (!$source->hasChildNodes()) {
            return $data;
        }
        foreach ($source->childNodes as $dataChild) {
            if ($dataChild instanceof DOMElement) {
                $data[$dataChild->attributes->getNamedItem('name')->nodeValue] = $this->_convertData($dataChild);
            } else {
                if (strlen(trim($dataChild->nodeValue))) {
                    $data = $dataChild->nodeValue;
                }
            }
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
