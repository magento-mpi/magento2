<?php
/**
 * Logging configuration Converter
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Logging_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $result = array('logging' => array());
        $xpath = new DOMXPath($source);
        $result['logging']['actions'] = $this->_getTitles($xpath);

        $events = $xpath->query('/logging/event');
        /** @var DOMNode $event */
        foreach ($events as $event) {
            $eventId = $event->attributes->getNamedItem('id')->nodeValue;
            $result['logging'][$eventId] = $this->_convertEvent($event);
        }

        return $result;
    }

    /**
     * Retrieves titles array from Xpath object
     *
     * @param DOMXPath $xpath
     * @return array
     */
    protected function _getTitles($xpath)
    {
        $result = array();
        $titles = $xpath->query('/logging/title');

        /** @var DOMNode $title */
        foreach ($titles as $title) {
            $action = $title->attributes->getNamedItem('action')->nodeValue;
            $result[$action]['label'] = $title->nodeValue;
        }
        return $result;
    }

    /**
     * Convert Event node to array
     *
     * @param DOMNode $event
     * @return array
     */
    protected function _convertEvent($event)
    {
        $result = array();
        foreach ($event->childNodes as $eventData) {
            switch ($eventData->nodeName) {
                case 'label':
                    $result['label'] = $eventData->nodeValue;
                    break;
                case 'expected_model':
                    $result['expected_models'][$eventData->attributes->getNamedItem('class')->nodeValue] =
                        $this->_convertExpectedModel($eventData);
                    break;
                case 'handle':
                    $handleName = $eventData->attributes->getNamedItem('name')->nodeValue;
                    $result['actions'][$handleName] = $this->_convertHandle($eventData);
                    break;
            }
        }
        return $result;
    }

    /**
     * Convert Handle node to array
     *
     * @param DOMNode $event
     * @return array
     */
    protected function _convertHandle($handle)
    {
        $result = array();
        $handleAttributes = $handle->attributes;
        $handleAction = $handleAttributes->getNamedItem('action');
        if (!is_null($handleAction)) {
            $result['action'] = $handleAction->nodeValue;
        }
        $skipOnBackHandles = $handleAttributes->getNamedItem('skip_on_back');
        if (!is_null($skipOnBackHandles)) {
            $result['skip_on_back'] = explode(' ', $skipOnBackHandles->nodeValue);
        }
        $postDispatch = $handleAttributes->getNamedItem('post_dispatch');
        if (!is_null($postDispatch)) {
            $result['post_dispatch'] = $postDispatch->nodeValue;
        }
        foreach ($handle->childNodes as $handleData) {
            switch ($handleData->nodeName) {
                case 'expected_model':
                    $result['expected_models'][$handleData->attributes->getNamedItem('class')->nodeValue] =
                        $this->_convertExpectedModel($handleData);
                    break;
                case 'post_dispatch':
                    $result['post_dispatch'][$handleData->attributes->getNamedItem('class')->nodeValue] =
                        $this->_convertExpectedModel($handleData);
                    break;
            }
        }
        $skipOnBackHandles = $handleAttributes->getNamedItem('extends_expected_models');
        if (!is_null($skipOnBackHandles) && $skipOnBackHandles->nodeValue == 'true') {
            $result['expected_models']['@'] = 'merge';
        }
        return $result;
    }

    /**
     * Convert Expected Model node to array
     *
     * @param DOMNode $event
     * @return array
     */
    protected function _convertExpectedModel($expectedModel)
    {
        $result = array();
        $expectedModelAttributes = $expectedModel->attributes;
        $skipFields = $expectedModelAttributes->getNamedItem('skip_fields');
        if (!is_null($skipFields)) {
            $result['skip_data'] = explode(' ', $skipFields->nodeValue);
        }
        $additionalFields = $expectedModelAttributes->getNamedItem('additional_fields');
        if (!is_null($additionalFields)) {
            $result['additional_data'] = explode(' ', $additionalFields->nodeValue);
        }
        return $result;
    }
}