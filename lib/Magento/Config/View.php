<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Config
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * View configuration files handler
 */
class Magento_Config_View extends Magento_Config_XmlAbstract
{
    /**
     * Path to view.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/view.xsd';
    }

    /**
     * Get a list of variables in scope of specified module
     *
     * Returns array(<var_name> => <var_value>)
     *
     * @param string $module
     * @return array
     */
    public function getVars($module)
    {
        $result = array();
        $xPath = new DOMXPath($this->_dom);
        /** @var DOMElement $item */
        foreach ($xPath->query("/view/vars[@module='{$module}']/var") as $item) {
            $result[$item->getAttribute('name')] = (string)$item->nodeValue;
        }
        return $result;
    }

    /**
     * Get value of a configuration option variable
     *
     * @param string $module
     * @param string $name
     * @return bool|string
     */
    public function getVarValue($module, $name)
    {
        $xPath = new DOMXPath($this->_dom);
        /** @var DOMElement $item */
        foreach ($xPath->query("/view/vars[@module='{$module}']/var[@name='{$name}']") as $item) {
            return (string)$item->nodeValue;
        }
        return false;
    }

    /**
     * Getter for initial view.xml contents
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><view></view>';
    }

    /**
     * Variables are identified by module and name
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array('/view/vars' => 'module', '/view/vars/var' => 'name');
    }
}
