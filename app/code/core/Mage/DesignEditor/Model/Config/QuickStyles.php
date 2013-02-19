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
 * Quick styles configuration
 */
class Mage_DesignEditor_Model_Config_QuickStyles extends Magento_Config_XmlAbstract
{
    /**
     * Path to quick_styles.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/../../etc/quick_styles.xsd';
    }

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
        }
        return $result;
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
     * Return group data
     *
     * @param string $groupName
     * @return array
     * @throws Magento_Exception
     */
    public function getGroupData($groupName)
    {
        if (!isset($this->_data[$groupName])) {
           throw new Magento_Exception("Unknown group: \"{$groupName}\"");
       }
        return $this->_data[$groupName];
    }

    /**
     * Getter for initial view.xml contents
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><quick-styles></quick-styles>';
    }

    /**
     * Variables are identified by module and name
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array('/quick-styles/control' => 'name', '/quick-styles/control/components/control' => 'name');
    }
}
