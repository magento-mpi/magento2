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
class Integrity_ModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Modules dependencies map
     *
     * @var array
     */
    protected $_modulesDependencies = array();

    /**
     * Dynamic regexp for matching class names in modules (based on namespaces list)
     *
     * @var string
     */
    protected static $_pattern = '';

    /**
     *  Define analysis rules
     */
    public static function setUpBeforeClass()
    {
        self::$_pattern = '~[\'"\s]((' . implode('_|', Utility_Files::init()->getNamespaces()) . '_)[a-zA-Z0-9_]+)~s';
    }

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
        }

        foreach (array_keys($this->_modulesDependencies) as $module) {
            $this->_expandDependencies($module);
        }
    }

    /**
     * Expand modules dependencies from modules chain
     *
     * @param string $module
     * @param array $modulesCheckChain - used to track already checked modules
     * @return array
     * @throws Exception
     */
    protected function _expandDependencies($module, $modulesCheckChain = array())
    {
        if (empty($this->_modulesDependencies[$module])) {
            return array();
        }

        $modulesCheckChain[] = $module;

        foreach ($this->_modulesDependencies[$module] as $dependency) {
            if (in_array($dependency, $modulesCheckChain)) {
                throw new Exception("Circular dependency is not allowed in $module on $dependency");
            }

            $mergeResult = array_unique(array_merge($this->_modulesDependencies[$module],
                $this->_expandDependencies($dependency, $modulesCheckChain)
            ));
            $this->_modulesDependencies[$module] = $mergeResult;
        }

        return $this->_modulesDependencies[$module];
    }

    /**
     * Check Magento modules structure for circular dependencies
     */
    public function testModulesDependencies()
    {
        try {
            $this->_buildModulesDependencies();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

    }


    /**
     * Check undeclared or invalid modules dependencies
     *
     * @dataProvider getModulesPhpFiles
     * @depends testModulesDependencies
     */
    public function testPhpFilesDependencies($file)
    {
        $this->_buildModulesDependencies();

        $pieces = explode('/', self::_getRelativeFilename($file));
        $package = $pieces[2] . '_' . $pieces[3];

        $contents = file_get_contents($file);

        //Removing php comments
        $contents = preg_replace('~/\*.*?\*/~s', '', $contents);
        $contents = preg_replace('~^\s*//.*$~s', '', $contents);

        if (preg_match_all(self::$_pattern, $contents, $matches)) {
            foreach ($matches[1] as $class) {
                $class = trim($class, "'\" ");

                //Skipping own module
                if (0 === strpos($class, $package)) {
                    continue;
                }

                $classPieces = explode('_', $class);
                $referencePackage = $classPieces[0] . '_' . $classPieces[1];

                if (empty($this->_modulesDependencies[$package])
                    || !in_array($referencePackage, $this->_modulesDependencies[$package])
                ) {
                    $this->fail("Undeclared dependency on $referencePackage found in $file");
                }
            }
        }
    }

    /**
     * Extract Magento relative filename from absolute filename
     *
     * @param string $absoluteFilename
     * @return string
     */
    protected static function _getRelativeFilename($absoluteFilename)
    {
        $relativeFileName = str_replace(Utility_Files::init()->getPathToSource(), '', $absoluteFilename);
        return  trim(str_replace('\\', '/', $relativeFileName), '/');
    }

    /**
     * Return modules php files
     *
     * @return array
     */
    public static function getModulesPhpFiles()
    {
        $files = Utility_Files::init()->getPhpFiles(true, false, false, true);
        $result = array();

        //Skipping files outside of modules
        foreach (array_keys($files) as $file) {
            if (substr_count(self::_getRelativeFilename($file), '/') < 4) {
                continue;
            }
            $result[$file] = array($file);
        }

        return $result;
    }
}
