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
    protected static $_modulesDependencies = array();

    /**
     * Corrected modules dependencies map
     *
     * @var array
     */
    protected static $_correctedModulesDependencies = array();

    /**
     * Rule list
     *
     * @var array
     */
    protected static $_rules = array(
        'Integrity_DependencyTest_PhpRule',
        'Integrity_DependencyTest_DbRule',
    );

    /**
     * Rule instances
     *
     * @var array
     */
    protected static $_rulesInstances = array();

    /**
     * Sets up data
     *
     */
    public static function setUpBeforeClass()
    {
        self::buildModulesDependencies();
        self::instantiateRules();
    }

    /**
     * Build modules dependencies
     */
    public static function buildModulesDependencies()
    {
        self::$_modulesDependencies = array();

        $configFiles = Utility_Files::init()->getConfigFiles('config.xml', array(), false);
        foreach ($configFiles as $configFile) {
            preg_match('#/([^/]+?/[^/]+?)/etc/config\.xml$#', $configFile, $moduleName);
            $moduleName = str_replace('/', '_', $moduleName[1]);
            $config = simplexml_load_file($configFile);
            $nodes = $config->xpath("/config/modules/$moduleName/depends/*") ?: array();
            foreach ($nodes as $node) {
                /** @var SimpleXMLElement $node */
                self::$_modulesDependencies[$moduleName][] = $node->getName();
            }
        }
    }

    /**
     * Create rules objects
     */
    public static function instantiateRules()
    {
        self::$_rulesInstances = array();

        foreach (self::$_rules as $ruleClass) {
            if (class_exists($ruleClass)) {
                $rule = new $ruleClass();
                if ($rule instanceof Integrity_DependencyTest_RuleInterface) {
                    self::$_rulesInstances[$ruleClass] = $rule;
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
                $contents = preg_replace('~^\s*//.*$~sm', '', $contents);
                break;
            case 'config': case 'layout':
                //Removing xml comments
                $contents = preg_replace('~\<!\-\-/.*?\-\-\>~s', '', $contents);
                break;
            case 'template':
                //Removing html
                $contentsWithoutHtml = '';
                preg_replace_callback(
                    '~(<\?php\s+.*\?>)~U',
                    function ($matches) use ($contents, &$contentsWithoutHtml) {
                        $contentsWithoutHtml .= $matches[1];
                        return $contents;
                    },
                    $contents
                );
                $contents = $contentsWithoutHtml;
                //Removing php comments
                $contents = preg_replace('~/\*.*?\*/~s', '', $contents);
                $contents = preg_replace('~^\s*//.*$~s', '', $contents);
                break;
        }
        return $contents;
    }

    /**
     * Check undeclared modules dependencies for specified file
     *
     * @param string $fileType
     * @param string $file
     *
     * @dataProvider getAllFiles
     */
    public function testDependencies($fileType, $file)
    {
        $contents = $this->_getCleanedFileContents($fileType, $file);

        $pieces = explode('/', $this->_getRelativeFilename($file));
        $module = $pieces[2] . '_' . $pieces[3];

        $dependenciesInfo = array();
        foreach (self::$_rulesInstances as $rule) {
            /** @var Integrity_DependencyTest_RuleInterface $rule */
            $dependenciesInfo = array_merge($dependenciesInfo,
                $rule->getDependencyInfo($module, $fileType, $file, $contents));
        }

        $declaredDependencies = isset(self::$_modulesDependencies[$module])
            ? self::$_modulesDependencies[$module]
            : array();

        $undeclaredDependencies = array();
        $undeclaredDependenciesInfo = array();
        foreach ($dependenciesInfo as $dependencyInfo) {
            if (!in_array($dependencyInfo['module'], $declaredDependencies)) {
                $undeclaredDependencies[] = $dependencyInfo['module'];
                $undeclaredDependenciesInfo[] = $dependencyInfo;

            }
            if (!isset(self::$_correctedModulesDependencies[$module])) {
                self::$_correctedModulesDependencies[$module] = array();
            }
            if (!in_array($dependencyInfo['module'], self::$_correctedModulesDependencies[$module])) {
                self::$_correctedModulesDependencies[$module][] = $dependencyInfo['module'];
            }
        }
        $undeclaredDependencies = array_unique($undeclaredDependencies);

        if (count($undeclaredDependencies) > 0) {
            $this->fail('Undeclared dependencies in ' . $module . ':' . $file
                . ': ' . var_export($undeclaredDependenciesInfo, true));
        }
    }

    /**
     * Check undeclared modules dependencies for specified file
     */
    public function testShowCorrectedDependencies()
    {
        $this->fail('Corrected modules dependencies:'
            . var_export(self::$_correctedModulesDependencies, true));
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
     * Return all files
     *
     * @return array
     */
    public function getAllFiles()
    {
        $files = array();
        // Get all php files
        $files = array_merge($files,
            $this->_prepareFiles('php', Utility_Files::init()->getPhpFiles(true, false, false, true)));
        // Get all configuration files
        $files = array_merge($files, $this->_prepareFiles('config', Utility_Files::init()->getConfigFiles()));
        //Get all layout updates files
        $files = array_merge($files, $this->_prepareFiles('layout', Utility_Files::init()->getLayoutFiles()));
        // Get all template files
        $files = array_merge($files, $this->_prepareFiles('template', Utility_Files::init()->getPhtmlFiles()));
        return $files;
    }
}
