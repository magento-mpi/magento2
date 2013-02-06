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
     * Load modules data as array from specific xml string
     *
     * @param  string $string
     * @return array
     */
    public function loadModulesFromString($string)
    {
        $nodeModulesConfig = clone $this->_prototype;
        $nodeModules = array();

        $nodeModulesConfig->loadString($string);
        if ($nodeModulesConfig->getNode('modules')) {
            $nodeModules = $nodeModulesConfig->getNode('modules')->asArray();
            if (!is_array($nodeModules)) {
                $nodeModules = array();
            }
        }
        return $nodeModules;
    }
}
