<?php
/**
 * Scan source code for incorrect or undeclared modules dependencies
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_CircularDependencyTest extends PHPUnit_Framework_TestCase
{
    /**
     * Modules dependencies map
     *
     * @var array
     */
    protected $_modulesDependencies = array();

    /**
     * Circular dependencies
     *
     * @var array
     */
    protected $_circularDependencies = array();

    /**
     * Build modules dependencies
     */
    protected function _buildModulesDependencies()
    {
        if (!empty($this->_modulesDependencies)) {
            return true;
        }
        $configFiles = Utility_Files::init()->getConfigFiles('config.xml', array(), false);

        foreach ($configFiles as $configFile) {
            preg_match('#/([^/]+?/[^/]+?)/etc/config\.xml$#', $configFile, $moduleName);
            $moduleName = str_replace('/', '_', $moduleName[1]);
            $config = simplexml_load_file($configFile);
            $nodes = $config->xpath("/config/modules/$moduleName/depends/*") ?: array();
            foreach ($nodes as $node) {
                $this->_modulesDependencies[$moduleName][] = $node->getName();
            }
            foreach (array_keys($this->_modulesDependencies) as $module) {
                $this->_expandDependencies($module, array($module));
            }
        }
    }

    /**
     * Expand modules dependencies from modules chain
     *
     * @param string $module
     * @param array $modulesCheckChain - used to track already checked modules
     * @param int $level nesting level
     * @return array
     */
    protected function _expandDependencies($module, $modulesCheckChain = array(), $level =0)
    {
        if (empty($this->_modulesDependencies[$module])) {
            return;
        }

        $level++;
        foreach ($this->_modulesDependencies[$module] as $dependency) {
            if (isset($this->_circularDependencies[$dependency])) {
                continue;
            }
            $keyResult = array_search($dependency, $modulesCheckChain);
            $tmp = $modulesCheckChain;
            array_push($tmp, $dependency);
            if ($keyResult !== false) {
                $this->_circularDependencies[$dependency] = array_slice($tmp, $keyResult);
                continue $level-$keyResult-3;
            }

            $this->_expandDependencies($dependency, $tmp, $level);
        }
    }

    /**
     * Check Magento modules structure for circular dependencies
     */
    public function testCircularDependencies()
    {
        $result = '';
        $this->_buildModulesDependencies();
        if (!empty($this->_circularDependencies)) {
            foreach($this->_circularDependencies as $circularDependency) {
                $result .= implode('->', $circularDependency) . PHP_EOL;
            }
            $this->fail("Circular dependencies:" . PHP_EOL . $result);
        }
    }
}
