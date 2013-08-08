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
     * Name of errors report file
     */
    const ERROR_REPORT_FILE = 'dependencies_error.xml';

    /**
     * Name of dependencies report file
     */
    const DEPENDENCIES_REPORT_FILE = 'dependencies_report.xml';

    /**
     * Path to report directory
     *
     * @var string
     */
    protected static $_reportDir = '';

    /**
     * XML report structure
     *
     * @var DOMDocument
     */
    protected static $_document;

    /**
     * Root XML element of errors report
     *
     * @var DOMElement
     */
    protected static $_rootErrors;

    /**
     * Root XML element of results report
     *
     * @var DOMElement
     */
    protected static $_rootResults;

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
     * List of layout blocks
     *
     * Format: array(
     *  '{Area}' => array(
     *   '{Block_Name}' => array('{Module_Name}' => '{Module_Name}')
     * ))
     *
     * @var array
     */
    protected static $_mapLayoutBlocks = array();

    /**
     * List of layout handles
     *
     * Format: array(
     *  '{Area}' => array(
     *   '{Handle_Name}' => array('{Module_Name}' => '{Module_Name}')
     * ))
     *
     * @var array
     */
    protected static $_mapLayoutHandles = array();

    /**
     * Modules dependencies map
     *
     * @var array
     */
    protected static $_modulesDependencies = array();

    /**
     * Modules dependency errors
     *
     * @var array
     */
    protected static $_modulesDependencyErrors = array();

    /**
     * Modules dependency results
     *
     * @var array
     */
    protected static $_modulesDependencyResults = array();

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
        self::_initReportDir();
        self::_initReportXml();

        self::_prepareListConfigXml();

        self::_prepareMapRouters();
        self::_prepareMapLayoutBlocks();
        self::_prepareMapLayoutHandles();

        self::_instantiateConfiguration();
        self::_instantiateRules();
    }
    /**
     * Save XML reports
     */
    public static function tearDownAfterClass()
    {
        self::_processDependencyErrors();
        self::_processDependencyResults();
    }

    /**
     * Initialize report directory
     */
    protected static function _initReportDir()
    {
        self::$_reportDir = Utility_Files::init()->getPathToSource() . '/dev/tests/static/report';
        if (!is_dir(self::$_reportDir)) {
            mkdir(self::$_reportDir, 0777);
        }
    }

    /**
     * Initialize XML report
     */
    protected static function _initReportXml()
    {
        self::$_document = new DOMDocument('1.0', 'UTF-8');
        self::$_document->formatOutput = true;
        self::$_rootErrors = self::$_document->createElement('errors');
        self::$_rootResults = self::$_document->createElement('results');
    }


    /**
     *  Process dependency errors and build XML report
     */
    protected static function _processDependencyErrors()
    {
        if (empty(self::$_modulesDependencyErrors)) {
            return;
        }
        foreach (self::$_modulesDependencyErrors as $moduleName => $moduleData) {
            $moduleNode = self::$_document->createElement('module');
            $moduleNode->setAttribute('name', $moduleName);
            foreach ($moduleData as $error) {
                $fileNode = self::$_document->createElement('file');
                $fileNode->setAttribute('path', $error['file']);
                foreach ($error['dependencies'] as $dependency) {
                    $dependencyNode = self::$_document->createElement('dependency');
                    $dependencyNode->setAttribute('module', $dependency['module']);
                    self::$_modulesDependencyResults[$moduleName][] = $dependency['module'];
                    self::$_modulesDependencyResults[$moduleName] =
                        array_unique(self::$_modulesDependencyResults[$moduleName]);
                    $sourceCdata = self::$_document->createCDATASection($dependency['source']);
                    $dependencyNode->appendChild($sourceCdata);
                    $fileNode->appendChild($dependencyNode);
                }
                $moduleNode->appendChild($fileNode);

            }
            self::$_rootErrors->appendChild($moduleNode);
        }
        self::$_document->appendChild(self::$_rootErrors);
        self::$_document->save(self::$_reportDir . DIRECTORY_SEPARATOR . self::ERROR_REPORT_FILE);
    }

    /**
     *  Process dependency results and build XML report
     */
    protected static function _processDependencyResults()
    {
        if (empty(self::$_modulesDependencyResults)) {
            return;
        }
        foreach (self::$_modulesDependencyResults as $moduleName => $moduleData) {
            $moduleNode = self::$_document->createElement('module');
            $moduleNode->setAttribute('name', $moduleName);
            $dependsNode = self::$_document->createElement('depends');
            foreach ($moduleData as $depends) {
                $dependencyNode = self::$_document->createElement($depends);
                $dependsNode->appendChild($dependencyNode);
            }
            $moduleNode->appendChild($dependsNode);
            self::$_rootResults->appendChild($moduleNode);
        }
        //self::$_document->appendChild(self::$_rootResults);
        $rootNodeList = self::$_document->getElementsByTagName('errors');
        foreach ($rootNodeList as $domElement) {
            self::$_document->replaceChild(self::$_rootResults, $domElement);
        }
        self::$_document->save(self::$_reportDir . DIRECTORY_SEPARATOR . self::DEPENDENCIES_REPORT_FILE);
    }

    /**
     * Build modules dependencies
     */
    protected static function _instantiateConfiguration()
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
    protected static function _instantiateRules()
    {
        self::$_rulesInstances = array();

        foreach (self::$_rules as $ruleClass) {
            if (class_exists($ruleClass)) {
                /** @var Integrity_DependencyTest_RuleInterface $rule */
                $rule = new $ruleClass(array(
                    'mapRouters'        => self::$_mapRouters,
                    'mapLayoutBlocks'   => self::$_mapLayoutBlocks,
                    'mapLayoutHandles'  => self::$_mapLayoutHandles,
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
        $filename = self::_getRelativeFilename($file);
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
            self::$_modulesDependencyErrors[$module][] =
                array(
                    'file' => self::_getRelativeFilename($file),
                    'dependencies' => $undeclaredDependenciesInfo
                );
            $this->fail();
        }
    }

    /**
     * Extract Magento relative filename from absolute filename
     *
     * @param string $absoluteFilename
     * @return string
     */
    static protected function _getRelativeFilename($absoluteFilename)
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
        $filename = self::_getRelativeFilename($absoluteFilename);
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
            if (substr_count(self::_getRelativeFilename($file), '/') < 4) {
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
    protected static function _prepareListConfigXml()
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
    protected static function _prepareMapRouters()
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

                if ($module == 'Mage_Adminhtml') {
                    $nodes = $config->xpath("/config/admin/routers/*") ?: array();
                } elseif ('adminhtml' == $chunks[0]) {
                    array_shift($chunks);
                    $nodes = $config->xpath("/config/admin/routers/*") ?: array();
                } else {
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
                    /** @var SimpleXMLElement $node */
                    $path = $node->getName() ? $node->getName() . '_' . $controllerName : $controllerName;
                    if (isset(self::$_mapRouters[$path]) && (self::$_mapRouters[$path] == 'Mage_Adminhtml')) {
                        continue;
                    }
                    self::$_mapRouters[$path] = $module;
                }
            }
        }
    }

    /**
     * Prepare map of layout blocks
     */
    protected static function _prepareMapLayoutBlocks()
    {
        $files = Utility_Files::init()->getLayoutFiles(array(), false);
        foreach ($files as $file) {
            $area = 'default';
            if (preg_match('/[\/](?<area>adminhtml|frontend)[\/]/', $file, $matches)) {
                $area = $matches['area'];
                if (!isset(self::$_mapLayoutBlocks[$area])) {
                    self::$_mapLayoutBlocks[$area] = array();
                }
            }

            if (preg_match('/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/', $file, $matches)) {
                $module = $matches['namespace'] . '_' . $matches['module'];

                $xml = simplexml_load_file($file);
                foreach ((array)$xml->xpath('//container | //block') as $element) {
                    /** @var SimpleXMLElement $element */
                    $attributes = $element->attributes();

                    $block = (string)$attributes->name;
                    if (!empty($block)) {
                        if (!isset(self::$_mapLayoutBlocks[$area][$block])) {
                            self::$_mapLayoutBlocks[$area][$block] = array();
                        }
                        self::$_mapLayoutBlocks[$area][$block][$module] = $module;
                    }
                }
            }
        }
    }

    /**
     * Prepare map of layout handles
     */
    protected static function _prepareMapLayoutHandles()
    {
        $files = Utility_Files::init()->getLayoutFiles(array(), false);
        foreach ($files as $file) {
            $area = 'default';
            if (preg_match('/\/(?<area>adminhtml|frontend)\//', $file, $matches)) {
                $area = $matches['area'];
                if (!isset(self::$_mapLayoutHandles[$area])) {
                    self::$_mapLayoutHandles[$area] = array();
                }
            }

            if (preg_match('/app\/code\/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/', $file, $matches)) {
                $module = $matches['namespace'] . '_' . $matches['module'];

                $xml = simplexml_load_file($file);
                foreach ((array)$xml->xpath('/layout/child::*') as $element) {
                    /** @var SimpleXMLElement $element */
                    $handle = $element->getName();
                    if (!isset(self::$_mapLayoutHandles[$area][$handle])) {
                        self::$_mapLayoutHandles[$area][$handle] = array();
                    }
                    self::$_mapLayoutHandles[$area][$handle][$module] = $module;
                }
            }
        }
    }
}
