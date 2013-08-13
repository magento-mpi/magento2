<?php
/**
 * Converter of event observers configuration from DOMDocument to tree array
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Enterprise_Queue_Model_Event_Config_Converter extends Mage_Core_Model_Event_Config_Converter
{
    /**
     * Convert observer configuration
     *
     * @param DOMNode $observerConfig
     * @return array
     * @throws InvalidArgumentException
     */
    public function _convertObserverConfig($observerConfig)
    {
        $output = parent::_convertObserverConfig($observerConfig);

        $asynchronousNode = $observerConfig->attributes->getNamedItem('asynchronous');
        if ($asynchronousNode && $asynchronousNode->nodeValue == 'true') {
            $output['asynchronous'] = true;
        }

        $priorityNode = $observerConfig->attributes->getNamedItem('priority');
        if ($priorityNode) {
            $output['priority'] = $priorityNode->nodeValue;
        }

        $params = array();
        /** @var DOMNode $param */
        foreach ($observerConfig->childNodes as $param) {
            if ($param->nodeName != 'param' || $param->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $nameNode = $param->attributes->getNamedItem('name');
            $valueNode = $param->attributes->getNamedItem('value');
            if (!$nameNode || !$valueNode) {
                throw new InvalidArgumentException('Invalid parameters format');
            }
            $params[$nameNode->nodeValue] = $valueNode->nodeValue;
        }

        if (count($params)) {
            $output['params'] = $params;
        }

        return $output;
    }

}
