<?php
/**
 * ObjectManager configuration DOM mapper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ObjectManager_Config_Mapper_Dom
{
    /**
     * Convert configuration in DOM format to assoc array that can be used by object manager
     *
     * @param DOMDocument $config
     * @return array
     * @throws Exception
     * @todo this method has high cyclomatic complexity in order to avoid performance issues
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function map(DOMDocument $config)
    {
        $output = array();
        /** @var DOMNode $node */
        foreach ($config->documentElement->childNodes as $node) {
            if ($node->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            switch ($node->nodeName) {
                case 'preference':
                    $output['preferences'][$node->attributes->getNamedItem('for')->nodeValue] = $node->attributes
                        ->getNamedItem('type')
                        ->nodeValue;
                    break;
                case 'type':
                case 'virtualType':
                    $typeData = array();
                    $typeNodeAttributes = $node->attributes;
                    $typeNodeShared = $typeNodeAttributes->getNamedItem('shared');
                    if (!is_null($typeNodeShared)) {
                        $typeData['shared'] = ($typeNodeShared->nodeValue == 'false') ? false : true;
                    }
                    if ($node->nodeName == 'virtualType') {
                        $attributeType = $typeNodeAttributes->getNamedItem('type');
                        // attribute type is required for virtual type only in merged configuration
                        if (!is_null($attributeType)) {
                            $typeData['type'] = $attributeType->nodeValue;
                        }
                    }
                    $typeParameters = array();
                    $typePlugins = array();
                    /** @var DOMNode $typeChildNode */
                    foreach ($node->childNodes as $typeChildNode) {
                        if ($typeChildNode->nodeType != XML_ELEMENT_NODE) {
                            continue;
                        }
                        switch ($typeChildNode->nodeName) {
                            case 'param':
                                $paramData = array();
                                /** @var DOMNode $paramChildNode */
                                foreach ($typeChildNode->childNodes as $paramChildNode) {
                                    if ($paramChildNode->nodeType != XML_ELEMENT_NODE) {
                                        continue;
                                    }
                                    switch ($paramChildNode->nodeName) {
                                        case 'instance':
                                            $instanceSharedNode = $paramChildNode->attributes->getNamedItem('shared');
                                            $paramData = array(
                                                'instance' => $paramChildNode->attributes
                                                    ->getNamedItem('type')
                                                    ->nodeValue,
                                            );
                                            if ($instanceSharedNode) {
                                                $paramData['shared'] = ($instanceSharedNode->nodeValue == 'false')
                                                    ? false : true;
                                            }
                                            break;
                                        case 'argument':
                                            $paramData['argument'] = $paramChildNode->attributes
                                                ->getNamedItem('name')
                                                ->nodeValue;
                                            break;
                                        case 'value':
                                            $paramData = $this->_processValueNode($paramChildNode);
                                            break;
                                        default:
                                            throw new Exception(
                                                "Invalid application config. Unknown node: {$paramChildNode->nodeName}."
                                            );
                                    }
                                }
                                $typeParameters[$typeChildNode->attributes->getNamedItem('name')->nodeValue]
                                    = $paramData;
                                break;
                            case 'plugin':
                                $pluginAttributes = $typeChildNode->attributes;
                                $pluginDisabledNode = $pluginAttributes->getNamedItem('disabled');
                                $pluginSortOrderNode = $pluginAttributes->getNamedItem('sortOrder');
                                $pluginTypeNode = $pluginAttributes->getNamedItem('type');
                                $pluginData = array(
                                    'sortOrder' => ($pluginSortOrderNode) ? (int)$pluginSortOrderNode->nodeValue : 0,
                                );
                                if ($pluginDisabledNode) {
                                    $pluginData['disabled'] = ($pluginDisabledNode->nodeValue == 'true') ? true : false;
                                }
                                if ($pluginTypeNode) {
                                    $pluginData['instance'] = $pluginTypeNode->nodeValue;
                                }
                                $typePlugins[$pluginAttributes->getNamedItem('name')->nodeValue] = $pluginData;
                                break;
                            default:
                                throw new Exception(
                                    "Invalid application config. Unknown node: {$typeChildNode->nodeName}."
                                );
                        }
                    }

                    $typeData['parameters'] = $typeParameters;
                    if (!empty($typePlugins)) {
                        $typeData['plugins'] = $typePlugins;
                    }
                    $output[$typeNodeAttributes->getNamedItem('name')->nodeValue] = $typeData;
                    break;
                default:
                    throw new Exception("Invalid application config. Unknown node: {$node->nodeName}.");
            }
        }

        return $output;
    }

    /**
     * Retrieve value of the given node
     *
     * Treat all child nodes as an assoc array
     *
     * @param DOMNode $valueNode
     * @return array|string
     */
    protected function _processValueNode(DOMNode $valueNode)
    {
        $output = array();
        $childNodesCount = $valueNode->childNodes->length;
        /** @var DOMNode $node */
        foreach ($valueNode->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $output[$node->nodeName] = $this->_processValueNode($node);
            } elseif (($node->nodeType == XML_TEXT_NODE || $node->nodeType == XML_CDATA_SECTION_NODE)
                && $childNodesCount == 1
            ) {
                // process DomText or DOMCharacterData node only if it is a single child of its parent
                return trim($node->nodeValue);
            }
        }
        return $output;
    }
}
