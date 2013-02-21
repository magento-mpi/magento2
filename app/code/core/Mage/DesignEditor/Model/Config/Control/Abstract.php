<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controls configuration
 */
abstract class Mage_DesignEditor_Model_Config_Control_Abstract extends Magento_Config_XmlAbstract
{
    /**
     * Keys of layout params attributes
     *
     * @var array
     */
    protected $_controlAttributes = array();

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array
     */
    protected function _extractData(DOMDocument $dom)
    {
        return $this->_extractControls($dom->childNodes->item(0)->childNodes);
    }

    /**
     * Extract all controls
     *
     * @param DOMNodeList $controls
     * @return array
     */
    protected function _extractControls(DOMNodeList $controls)
    {
        $result = array();
        /** @var $control DOMElement */
        foreach ($controls as $control) {
            if (!$control instanceof DOMElement) {
                continue;
            }
            $controlName = $control->getAttribute('name');
            $result[$controlName]['type'] = $control->getElementsByTagName('type')->item(0)->nodeValue;
            /** @var $components DOMElement */
            $components = $control->getElementsByTagName('components')->item(0);
            if ($components && $components->childNodes->length) {
                $result[$controlName]['components'] = $this->_extractControls($components->childNodes);
            } else {
                $result[$controlName] =  $this->_extractParams($control);
            }
            $controlLayoutParams = $this->_extractLayoutParams($control);
            if (!empty($controlLayoutParams)) {
                $result[$controlName]['layoutParams'] = $controlLayoutParams;
            }
        }
        return $result;
    }

    /**
     * Extract layout parameters which declare position of controls in layout
     *
     * @param DOMElement $control
     * @return array
     */
    protected function _extractLayoutParams(DOMElement $control)
    {
        $layoutParams = array();
        foreach ($this->_controlAttributes as $attributeName) {
            $controlTitle = $control->getAttribute($attributeName);
            if (!empty($controlTitle)) {
                $layoutParams[$attributeName] = $controlTitle;
            }
        }
        return $layoutParams;
    }

    /**
     * Extract params data
     *
     * @param DOMElement $control
     * @param bool $useKeyIdentifier
     * @return array
     */
    protected function _extractParams(DOMElement $control, $useKeyIdentifier = true)
    {
        $result = array();
        /** @var $paramNode DOMElement */
        foreach ($control->childNodes as $paramNode) {
            if (!$paramNode instanceof DOMElement) {
                continue;
            }
            $param = $paramNode->childNodes->length > 1 ? $this->_extractParams($paramNode, false)
                : trim($paramNode->nodeValue);
            if ($useKeyIdentifier) {
                $result[$paramNode->nodeName] = $param;
            } else {
                $result[] = $param;
            }
        }
        return $result;
    }

    /**
     * Return control data
     *
     * @param string $controlName
     * @return array
     * @throws Magento_Exception
     */
    public function getControlData($controlName)
    {
        if (!isset($this->_data[$controlName])) {
           throw new Magento_Exception("Unknown control: \"{$controlName}\"");
        }
        return $this->_data[$controlName];
    }

    /**
     * Return all controls data
     *
     * @return array
     */
    public function getAllControlsData()
    {
        return $this->_data;
    }
}
