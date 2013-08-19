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
     * Types of dependencies between modules
     */
    const TYPE_SOFT = 'soft';
    const TYPE_HARD = 'hard';

    /**
     * Types of dependencies map arrays
     */
    const MAP_TYPE_DECLARED      = 'declared';
    const MAP_TYPE_FOUND         = 'found';
    const MAP_TYPE_REDUNDANT     = 'redundant';

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
     * List of dependencies
     *
     * Format: array(
     *  '{Module_Name}' => array(
     *   '{Type}' => array(
     *    'declared'  = array('{Dependency}', ...)
     *    'found'     = array('{Dependency}', ...)
     *    'redundant' = array('{Dependency}', ...)
     * )))
     * @var array
     */
    protected static $_mapDependencies = array();

    /**
     * Regex pattern for validation file path of theme
     *
     * @var string
     */
    protected static $_defaultThemes = '';

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
        self::_prepareListConfigXml();

        self::_initDependencies();

        self::_prepareMapRouters();
        self::_prepareMapLayoutBlocks();
        self::_prepareMapLayoutHandles();

        self::_instantiateConfiguration();
        self::_instantiateRules();
    }

    /**
     * Build modules dependencies
     */
    protected static function _instantiateConfiguration()
    {
        $defaultThemes = array();
        foreach (self::$_listConfigXml as $module => $file) {
            $config = simplexml_load_file($file);

            $nodes = $config->xpath("/config/modules/$module/depends/*") ?: array();
            foreach ($nodes as $node) {
                /** @var SimpleXMLElement $node */
                $type = ($node->attributes()->type == self::TYPE_SOFT) ? self::TYPE_SOFT : self::TYPE_HARD;
                self::_addDependencies($module, $type, self::MAP_TYPE_DECLARED, $node->getName());
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
    public function testUndeclared($fileType, $file)
    {
        if (strpos($file, 'app/code') === false && !$this->_isThemeFile($file)) {
            return;
        }

        $module = $this->_getModuleName($file);
        $contents = $this->_getCleanedFileContents($fileType, $file);

        // Apply rules
        $dependencies = array();
        foreach (self::$_rulesInstances as $rule) {
            /** @var Integrity_DependencyTest_RuleInterface $rule */
            $dependencies = array_merge($dependencies,
                $rule->getDependencyInfo($module, $fileType, $file, $contents));
        }

        // Collect dependencies
        $undeclared = $this->_collectDependencies($module, $dependencies);

        // Prepare output message
        $result = array();
        foreach ($undeclared as $type => $modules) {
            $modules = array_unique($modules);
            if (!count($modules)) {
                continue;
            }
            $result[] = sprintf("%s [%s]", $type, implode(', ', $modules));
        }
        if (count($result)) {
            $this->fail('Undeclared module dependencies found: ' . implode(', ', $result));
        }
    }

    /**
     * Collect dependencies
     *
     * @param $currentModule
     * @param array $dependencies
     * @return array
     */
    protected function _collectDependencies($currentModule, $dependencies = array())
    {
        if (!count($dependencies)) {
            return array();
        }

        $undeclared = array();
        foreach ($dependencies as $dependency) {
            $module = $dependency['module'];
            $type = isset($dependency['type']) ? $dependency['type'] : self::TYPE_HARD;

            $declared = $this->_getDependencies($module, $type, self::MAP_TYPE_DECLARED);
            if (!in_array($module, $declared)) {
                $undeclared[$type][] = $module;
            }

            $this->_addDependencies($currentModule, $type, self::MAP_TYPE_FOUND, $module);
        }
        return $undeclared;
    }

    /**
     * Collect redundant dependencies
     *
     * @test
     * @depends testUndeclared
     */
    public function collectRedundant()
    {
        foreach (array_keys(self::$_mapDependencies) as $module) {

            // Override 'soft' dependencies with 'hard'
            $soft = $this->_getDependencies($module, self::TYPE_SOFT, self::MAP_TYPE_FOUND);
            $hard = $this->_getDependencies($module, self::TYPE_HARD, self::MAP_TYPE_FOUND);

            $this->_setDependencies($module, self::TYPE_SOFT, self::MAP_TYPE_FOUND,
                array_diff($soft, $hard));

            foreach ($this->_getTypes() as $type) {
                $declared = $this->_getDependencies($module, $type, self::MAP_TYPE_DECLARED);
                $found = $this->_getDependencies($module, $type, self::MAP_TYPE_FOUND);

                $this->_setDependencies($module, $type, self::MAP_TYPE_REDUNDANT,
                    array_diff($declared, $found));
            }
        }
    }

    /**
     * Check redundant dependencies
     *
     * @depends collectRedundant
     */
    public function testRedundant()
    {
        $output = array();
        foreach (array_keys(self::$_mapDependencies) as $module) {
            $result = array();
            foreach ($this->_getTypes() as $type) {
                $redundant = $this->_getDependencies($module, $type, self::MAP_TYPE_REDUNDANT);
                if (count($redundant)) {
                    $result[] = sprintf("\r\nModule %s: %s [%s]",
                        $module, $type, implode(', ', array_values($redundant)));
                }
            }
            $output[] = implode(', ', $result);
        }
        if (count($output)) {
            $this->fail("Redundant dependencies found!\r\n" . implode(' ', $output));
        }
    }

    /**
     * Generate XML file for all dependencies
     *
     * @test
     * @depends collectRedundant
     */
    public function generateXml()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $configNode = $dom->createElement('config');
        $modulesNode =  $dom->createElement('modules');

        foreach (array_keys(self::$_mapDependencies) as $module) {

            $moduleNameNode = $dom->createElement($module);
            foreach ($this->_getTypes() as $type) {

                $declared = $this->_getDependencies($module, $type, self::MAP_TYPE_DECLARED);
                $found = $this->_getDependencies($module, $type, self::MAP_TYPE_FOUND);
                $redundant = $this->_getDependencies($module, $type, self::MAP_TYPE_REDUNDANT);

                $realModules = array_diff($declared, $redundant);
                $realModules = array_merge($realModules, $found);

                if (count($realModules)) {
                    $dependsNode = $dom->createElement('depends');
                    $dependsNode->setAttribute('type', $type);

                    foreach ($realModules as $realModule) {
                        $dependencyNode = $dom->createElement($realModule);
                        $dependsNode->appendChild($dependencyNode);
                    }
                    $moduleNameNode->appendChild($dependsNode);
                }
            }
            $modulesNode->appendChild($moduleNameNode);
        }

        $configNode->appendChild($modulesNode);
        $dom->appendChild($configNode);

        $dom->save('modules.xml');
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
        $files = array_merge($files,
            $this->_prepareFiles('config', Utility_Files::init()->getConfigFiles()));

        //Get all layout updates files
        $files = array_merge($files,
            $this->_prepareFiles('layout', Utility_Files::init()->getLayoutFiles()));

        // Get all template files
        $files = array_merge($files,
            $this->_prepareFiles('template', Utility_Files::init()->getPhtmlFiles()));

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

    /**
     * Retrieve dependency types array
     *
     * @return array
     */
    protected function _getTypes()
    {
        return array(
            self::TYPE_HARD,
            self::TYPE_SOFT,
        );
    }

    /**
     * Initialise map of dependencies
     */
    protected function _initDependencies()
    {
        foreach (array_keys(self::$_listConfigXml) as $module) {
            if (!isset(self::$_mapDependencies[$module])) {
                self::$_mapDependencies[$module] = array();
            }
            foreach (self::_getTypes() as $type) {
                if (!isset(self::$_mapDependencies[$module][$type])) {
                    self::$_mapDependencies[$module][$type] = array(
                        self::MAP_TYPE_DECLARED      => array(),
                        self::MAP_TYPE_FOUND         => array(),
                        self::MAP_TYPE_REDUNDANT     => array(),
                    );
                }
            }
        }
    }

    /**
     * Add dependency map items
     *
     * @param $module
     * @param $type
     * @param $mapType
     * @param $dependencies
     */
    protected function _addDependencies($module, $type, $mapType, $dependencies)
    {
        if (!is_array($dependencies)) {
            $dependencies = array($dependencies);
        }
        foreach ($dependencies as $dependency) {
            if (isset(self::$_mapDependencies[$module][$type][$mapType])) {
                self::$_mapDependencies[$module][$type][$mapType][$dependency] = $dependency;
            }
        }
    }

    /**
     * Retrieve array of dependency items
     *
     * @param $module
     * @param $type
     * @param $mapType
     * @return array
     */
    protected function _getDependencies($module, $type, $mapType)
    {
        if (isset(self::$_mapDependencies[$module][$type][$mapType])) {
            return self::$_mapDependencies[$module][$type][$mapType];
        }
        return array();
    }

    /**
     * Set dependency map items
     *
     * @param $module
     * @param $type
     * @param $mapType
     * @param $dependencies
     */
    protected function _setDependencies($module, $type, $mapType, $dependencies)
    {
        if (!is_array($dependencies)) {
            $dependencies = array($dependencies);
        }
        if (isset(self::$_mapDependencies[$module][$type][$mapType])) {
            self::$_mapDependencies[$module][$type][$mapType] = $dependencies;
        }
    }
}
