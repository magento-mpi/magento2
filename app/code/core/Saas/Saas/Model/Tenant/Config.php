<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Saas
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config model to work with xml strings
 *
 * @category   Saas
 * @package    Saas
 */
class Saas_Saas_Model_Tenant_Config extends Varien_Simplexml_Config
{
    /**
     * @var Varien_Simplexml_Config
     */
    protected $_prototype;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_prototype = new Varien_Simplexml_Config();
    }

    /**
     * Merge all xml-nodes from array into one
     *
     * @param array $configStringArray of valid xml documents
     * @return Saas_Saas_Model_Tenant_Config
     */
    public function merge(array $configStringArray)
    {
        $firstElement = array_shift($configStringArray);
        $this->loadString($firstElement);

        foreach ($configStringArray as $string) {
            $merge = clone $this->_prototype;
            $merge->loadString($string);
            $this->extend($merge);
        }
        return $this;
    }

    /**
     * Takes xml string $allModules, and removes all modules, that not inside $allowedModules
     *
     * @param string $allModules
     * @param array $allowedModules
     * @return string
     */
    public function getModulesConfig($allModules, $allowedModules)
    {
        $allModulesConfig = clone $this->_prototype;

        if ($allModules) {
            $allModulesConfig->loadString($allModules);
            if (isset($allModulesConfig->getNode()->modules)) {
                //Remove selected modules that not available to change
                foreach (array_keys((array)$allModulesConfig->getNode('modules')) as $key) {
                    if (!isset($allowedModules[$key])) {
                        unset($allModulesConfig->getNode('modules')->$key);
                    }
                }
            }
        }
        return $allModulesConfig->getXmlString();
    }

    /**
     * Load modules data as array from specific xml string
     *
     * @param  string $string
     * @return array
     */
    public function loadModulesFromString($string)
    {
        $nodeModulesConfig   = clone $this->_prototype;
        $nodeModules = array();

        if (!is_null($string)) {
            $nodeModulesConfig->loadString($string);
            if ($nodeModulesConfig->getNode('modules')) {
                $nodeModules = $nodeModulesConfig->getNode('modules')->asArray();
                if (!is_array($nodeModules)) {
                    $nodeModules = array();
                }
            }
        }
        return $nodeModules;
    }
}
