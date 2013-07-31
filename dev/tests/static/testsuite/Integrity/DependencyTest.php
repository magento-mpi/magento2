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
class Integrity_DependencyTest extends PHPUnit_Framework_TestCase
{
    /**
     * Modules dependencies map
     *
     * @var array
     */
    protected $_modulesDependencies = array();

    /**
     * Rule list
     *
     * @var array
     */
    protected $_rules = array(
        'Integrity_DependencyTest_PhpRule',
    );

    /**
     * Rule instances
     *
     * @var array
     */
    protected $_rulesInstances = array();

    /**
     * Sets up data
     *
     */
    protected function setUp()
    {
        $this->_buildModulesDependencies();
        $this->_instantiateRules();
    }

    /**
     * Build modules dependencies
     */
    protected function _buildModulesDependencies()
    {
        $this->_modulesDependencies = array();

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
    }

    /**
     * Create rules objects
     */
    protected function _instantiateRules()
    {
        $this->_rulesInstances = array();

        foreach ($this->_rules as $ruleClass) {
            if (class_exists($ruleClass)) {
                $rule = new $ruleClass();
                if ($rule instanceof Integrity_DependencyTest_RuleInterface)
                {
                    $this->_rulesInstances[$ruleClass] = $rule;
                }
            }
        }
    }

    /**
     * Return cleaned file contents
     *
     * @param string $fileType
     * @param string $file
     * @return string
     */
    protected function _getCleanedFileContents($fileType, $file)
    {
        $contents = (string)file_get_contents($file);
        switch ($fileType) {
            case 'php':
                //Removing php comments
                $contents = preg_replace('~/\*.*?\*/~s', '', $contents);
                $contents = preg_replace('~^\s*//.*$~s', '', $contents);
                break;
            case 'config':
                break;
            case 'layout':
                break;
            case 'template':
                break;
        }
        return $contents;
    }

    /**
     * Check undeclared or invalid modules dependencies for specified file
     *
     * @param string $fileType
     * @param string $file
     *
     * @dataProvider getModulesFiles
     */
    public function testFilesDependencies($fileType, $file)
    {
        $contents = $this->_getCleanedFileContents($fileType, $file);

        $pieces = explode('/', $this->_getRelativeFilename($file));
        $module = $pieces[2] . '_' . $pieces[3];

        $dependenciesInfo = array();
        foreach ($this->_rulesInstances as $rule) {
            $dependenciesInfo = array_merge($dependenciesInfo,
                $rule->getDependencyInfo($module, $fileType, $file, $contents));
        }

        $declaredDependencies = isset($this->_modulesDependencies[$module])
            ? $this->_modulesDependencies[$module]
            : array();

        $undeclaredDependencies = array();
        $undeclaredDependenciesInfo = array();
        foreach ($dependenciesInfo as $dependencyInfo) {
            if (!in_array($dependencyInfo['module'], $declaredDependencies)) {
                $undeclaredDependencies[] = $dependencyInfo['module'];
                $undeclaredDependenciesInfo[] = $dependencyInfo;
            }
        }
        $undeclaredDependencies = array_unique($undeclaredDependencies);

        if (count($undeclaredDependencies) > 0) {
            $this->fail('Undeclared dependencies in ' . $module . ':' . $file
                . ': ' . var_export($undeclaredDependenciesInfo, true));
        }
    }

    /**
     * Extract Magento relative filename from absolute filename
     *
     * @param string $absoluteFilename
     * @return string
     */
    protected function _getRelativeFilename($absoluteFilename)
    {
        $relativeFileName = str_replace(Utility_Files::init()->getPathToSource(), '', $absoluteFilename);
        return trim(str_replace('\\', '/', $relativeFileName), '/');
    }

    /**
     * Convert file list to data provider structure
     *
     * @param string $fileType
     * @param array $files
     * @return array
     */
    protected function _prepareFiles($fileType, $files) {
        $result = array();
        foreach (array_keys($files) as $file) {
            if (substr_count($this->_getRelativeFilename($file), '/') < 4) {
                continue;
            }
            $result[$file] = array($fileType, $file);
        }
        return $result;
    }

    /**
     * Return modules files
     *
     * @return array
     */
    public function getModulesFiles()
    {
        $files = $this->_prepareFiles('php', Utility_Files::init()->getPhpFiles(true, false, false, true));
        $files = array_merge($files, $this->_prepareFiles('config', Utility_Files::init()->getConfigFiles()));
        $files = array_merge($files, $this->_prepareFiles('layout', Utility_Files::init()->getLayoutFiles()));
        $files = array_merge($files, $this->_prepareFiles('template', Utility_Files::init()->getPhtmlFiles()));
        return $files;
    }
}
