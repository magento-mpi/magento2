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
        $result['logging']['actions'] = $this->_getActionTitles($xpath);

        $groups = $xpath->query('/logging/group');
        /** @var DOMNode $group */
        foreach ($groups as $group) {
            $groupId = $group->attributes->getNamedItem('name')->nodeValue;
            $result['logging'][$groupId] = $this->_convertGroup($group, $groupId);
        }

        return $result;
    }

    /**
     * Retrieves titles array from Xpath object
     *
     * @param DOMXPath $xpath
     * @return array
     */
    protected function _getActionTitles($xpath)
    {
        $result = array();
        $actions = $xpath->query('/logging/action');

        /** @var DOMNode $action */
        foreach ($actions as $action) {
            $actionId = $action->attributes->getNamedItem('id')->nodeValue;
            foreach ($action->childNodes as $label) {
                if ($label->nodeName == 'label') {
                    $result[$actionId]['label'] = $label->nodeValue;
                }
            }
        }
        return $result;
    }

    /**
     * Convert Group node to array
     *
     * @param DOMNode $event
     * @param string $groupId
     * @return array
     */
    protected function _convertGroup($group, $groupId)
    {
        $result = array();
        foreach ($group->childNodes as $groupParams) {
            switch ($groupParams->nodeName) {
                case 'label':
                    $result['label'] = $groupParams->nodeValue;
                    break;
                case 'expected_model':
                    $result['expected_models'][$groupParams->attributes->getNamedItem('class')->nodeValue] =
                        $this->_convertExpectedModel($groupParams);
                    break;
                case 'event':
                    $eventName = $groupParams->attributes->getNamedItem('controller_action')->nodeValue;
                    $result['actions'][$eventName] = $this->_convertEvent($groupParams, $groupId);
                    break;
            }
        }
        return $result;
    }

    /**
     * Convert Event node to array
     *
     * @param DOMNode $event
     * @param string $groupId
     * @return array
     */
    protected function _convertEvent($event, $groupId)
    {
        $result = array('group_name' => $groupId);
        $eventAttributes = $event->attributes;
        $eventAction = $eventAttributes->getNamedItem('action_alias');
        if (!is_null($eventAction)) {
            $result['action'] = $eventAction->nodeValue;
        }

        $postDispatch = $eventAttributes->getNamedItem('post_dispatch');
        if (!is_null($postDispatch)) {
            $result['post_dispatch'] = $postDispatch->nodeValue;
        }
        foreach ($event->childNodes as $eventData) {
            $eventDataAttributes = $eventData->attributes;
            switch ($eventData->nodeName) {
                case 'expected_model':
                    $result['expected_models'][$eventData->attributes->getNamedItem('class')->nodeValue] =
                        $this->_convertExpectedModel($eventData);
                    break;
                case 'post_dispatch':
                    $result['post_dispatch'][$eventData->attributes->getNamedItem('class')->nodeValue] =
                        $this->_convertExpectedModel($eventData);
                    break;
                case 'skip_on_back':
                    $result['skip_on_back'][] = $eventDataAttributes->getNamedItem('controller_action')->nodeValue;
                    break;
            }
        }
        $extendsExpected = $eventAttributes->getNamedItem('extends_expected_models');
        if (!is_null($extendsExpected) && $extendsExpected->nodeValue == 'true') {
            $result['expected_models']['@']['extends'] = 'merge';
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
        foreach ($expectedModel->childNodes as $parameter) {
            switch ($parameter->nodeName) {
                case 'skip_field':
                    $result['skip_data'][] = $parameter->attributes->getNamedItem('name')->nodeValue;
                    break;
                case 'additional_field':
                    $result['additional_data'][] = $parameter->attributes->getNamedItem('name')->nodeValue;
            }
        }
        return $result;
    }
}