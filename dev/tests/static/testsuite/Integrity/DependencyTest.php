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
     * List of config.xml files by modules
     *
     * Format: array(
     *  '{Module_Name}' => '{Filename}'
     * )
     *
     * @var array
     */
    protected static $_listConfigXml = array();

    /**
     * List of routers
     *
     * Format: array(
     *  '{Router}' => '{Module_Name}'
     * )
     *
     * @var array
     */
    protected static $_mapRouters = array();

    /**
     * Modules dependencies map
     *
     * @var array
     */
    protected static $_modulesDependencies = array();

    /**
     * Regex pattern for validation file path of theme
     *
     * @var string
     */
    protected static $_defaultThemes = '';

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
        'Integrity_DependencyTest_LayoutRule',
        'Integrity_DependencyTest_TemplateRule',
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
        self::prepareListConfigXml();
        self::prepareMapRouters();

        self::instantiateConfiguration();
        self::instantiateRules();
    }

    /**
     * Build modules dependencies
     */
    public static function instantiateConfiguration()
    {
        self::$_modulesDependencies = array();

        $defaultThemes = array();
        foreach (self::$_listConfigXml as $module => $file) {
            $config = simplexml_load_file($file);

            $nodes = $config->xpath("/config/modules/$module/depends/*") ?: array();
            foreach ($nodes as $node) {
                /** @var SimpleXMLElement $node */
                self::$_modulesDependencies[$module][] = $node->getName();
            }

            $nodes = $config->xpath("/config/*/design/theme/full_name") ?: array();
            foreach ($nodes as $node) {
                $defaultThemes[] = (string)$node;
            }
        }
        self::$_defaultThemes = sprintf('#app/design.*/(%s)/.*#', implode('|', array_unique($defaultThemes)));
    }

    /**
     * Create rules objects
     */
    public static function instantiateRules()
    {
        self::$_rulesInstances = array();

        foreach (self::$_rules as $ruleClass) {
            if (class_exists($ruleClass)) {
                /** @var Integrity_DependencyTest_RuleInterface $rule */
                $rule = new $ruleClass(array(
                    'mapRouters' => self::$_mapRouters,
                ));
                if ($rule instanceof Integrity_DependencyTest_RuleInterface) {
                    self::$_rulesInstances[$ruleClass] = $rule;
                }
            }
        }
    }

    /**
     * Validates file when it is belonged to default themes
     *
     * @param $file string
     * @return bool
     */
    protected function _isThemeFile($file)
    {
        $filename = $this->_getRelativeFilename($file);
        return (bool)preg_match(self::$_defaultThemes, $filename);
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
                $contents = preg_replace('~^\s*//.*$~m', '', $contents);
                break;
            case 'layout':
            case 'config':
                //Removing xml comments
                $contents = preg_replace('~\<!\-\-/.*?\-\-\>~s', '', $contents);
                break;
            case 'template':
                //Removing html
                $contentsWithoutHtml = '';
                preg_replace_callback(
                    '~(<\?php\s+.*\?>)~sU',
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
        if (strpos($file, 'app/code') === false && !$this->_isThemeFile($file)) {
            return;
        }

        $module = $this->_getModuleName($file);

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
     * Extract module name from file path
     *
     * @param $absoluteFilename
     * @return string
     */
    protected function _getModuleName($absoluteFilename)
    {
        $filename = $this->_getRelativeFilename($absoluteFilename);
        $pieces = explode('/', $filename);
        $moduleName = $pieces[2] . '_' . $pieces[3];
        if ($this->_isThemeFile($absoluteFilename)) {
            $moduleName = $pieces[5];
        }
        return $moduleName;
    }

    /**
     * Convert file list to data provider structure
     *
     * @param string $fileType
     * @param array $files
     * @return array
     */
    protected function _prepareFiles($fileType, $files)
    {
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

    /**
     * Prepare list of config.xml files (by modules)
     */
    public static function prepareListConfigXml()
    {
        $files = Utility_Files::init()->getConfigFiles('config.xml', array(), false);
        foreach ($files as $file) {
            if (preg_match('/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/', $file, $matches)) {
                $module = $matches['namespace'] . '_' . $matches['module'];
                self::$_listConfigXml[$module] = $file;
            }
        }
    }

    /**
     * Prepare map of routers
     */
    public static function prepareMapRouters()
    {
        $pattern = '/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)\/controllers\/'
            . '(?<path>[\/\w]*)Controller.php/';

        $files = Utility_Files::init()->getPhpFiles(true, false, false, false);
        foreach ($files as $file) {
            if (preg_match($pattern, $file, $matches)) {

                $chunks = explode('/', strtolower($matches['path']));
                $module = $matches['namespace'] . '_' . $matches['module'];

                // Read module's config.xml file
                $config = simplexml_load_file(self::$_listConfigXml[$module]);

                if ('adminhtml' == $chunks[0]) {
                    array_shift($chunks);
                    $nodes = $config->xpath("/config/admin/routers/*") ?: array();
                }
                else {
                    $nodes = $config->xpath("/config/frontend/routers/*") ?: array();
                    foreach ($nodes as $nodeKey => $node) {
                        // Exclude overridden routers
                        if ('' == (string)$node->args->frontName) {
                            unset($nodes[$nodeKey]);
                        }
                    }
                }

                $controllerName = implode('_', $chunks);
                foreach ($nodes as $node) {
                    $path = $node->getName() ? $node->getName() . '_' . $controllerName : $controllerName;
                    self::$_mapRouters[$path] = $module;
                }
            }
        }
    }
}
