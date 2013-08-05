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
class Integrity_LayoutDependenciesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Rules to search dependencies
     *
     * @var array
     */
    protected $_rules = array(
        'ruleCheckAttributeModule',
        'ruleCheckElementBlock',
        'ruleCheckElementAction',
        'ruleCheckLayoutHandles',
        'ruleCheckLayoutHandlesParents',
        'ruleCheckLayoutHandlesUpdate',
        'ruleCheckLayoutReferences',
    );

    /**
     * Default modules list.
     *
     * @var array
     */
    protected $_defaultModules = array(
        'default'   => 'Mage_Install',
        'frontend'  => 'Mage_Page',
        'adminhtml' => 'Mage_Adminhtml',
    );

    /**
     * Dataset with layout files names, namespaces and modules
     *
     * @var array
     */
    protected $_dataset = array();

    /**
     * List of layout handles that was got by controllers
     *
     * Format: array('router_controller' => 'Namespace_Module')
     *
     * @var array
     */
    protected static $_mapLayoutHandlesByControllers = array();

    /**
     * List of layout handles that was got by layout files
     *
     * @var array
     */
    protected static $_mapLayoutHandlesByFiles = array();

    /**
     * List of layout blocks associated with modules
     *
     * Format: array('block' => ('area' => array('module', 'module')))
     *
     * @var array
     */
    protected static $_mapLayoutBlocks = array();

    /**
     * Unknown layout handle
     */
    const UNKNOWN_HANDLE = 'UNKNOWN_HANDLE';

    /**
     * Unknown layout block
     */
    const UNKNOWN_BLOCK = 'UNKNOWN_BLOCK';

    /**
     * Possible dependencies
     */
    const POSSIBLE_DEPENDENCIES = 'POSSIBLE_DEPENDENCIES';

    /**
     * Initialize map
     */
    public static function setUpBeforeClass()
    {
        self::_getMapLayoutHandles();
        self::_getMapLayoutBlocks();
    }

    /**
     * Execute all rules
     *
     * @param $file
     * @param $namespace
     * @param $module
     *
     * @dataProvider getDataset
     */
    public function testByRules($file, $namespace, $module)
    {
        $contents = file_get_contents($file);

        $dependencies = array();
        foreach ($this->_rules as $rule) {
            $currentModule = $namespace . '_' . $module;
            $result = $this->$rule($currentModule, null, $file, $contents);
            if (count($result)) {
                $dependencies = array_merge($dependencies, $result);
            }
        }

        if (count($dependencies)) {
            $this->fail("Undeclared dependency found in $file.\nDependencies: " . var_export($dependencies, true));
        }
    }

    /**
     * The rule to check dependencies for module="..." attribute
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function ruleCheckAttributeModule($currentModule, $fileType, $file, $contents)
    {
        $patterns = array(
            '/(?<source><.+module\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)[_](?<module>[A-Z][a-zA-Z]+)[\'"].*>)/'
        );
        return $this->_searchDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * The rule to check dependencies for <block> element
     *
     * Search dependencies for type="..." attribute.
     * Search dependencies for template="..." attribute.
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function ruleCheckElementBlock($currentModule, $fileType, $file, $contents)
    {
        $patterns = array(
            '/(?<source><block.*type\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-zA-Z]+_?){1,}[\'"].*>)/',
            '/(?<source><block.*template\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.]+[\'"].*>)/'
        );
        return $this->_searchDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * The rule to check dependencies for <action> element
     *
     * Search dependencies for <block> element.
     * Search dependencies for <template> element.
     * Search dependencies for <file> element.
     * Search dependencies for helper="..." attribute.
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function ruleCheckElementAction($currentModule, $fileType, $file, $contents)
    {
        $patterns = array(
            '/(?<source><block\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-zA-Z]+_?){1,}<\/block\s*>)/',
            '/(?<source><template\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.]+<\/template\s*>)/',
            '/(?<source><file\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.-]+<\/file\s*>)/',
            '/(?<source><.*helper\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-z]+_?){1,}::[\w]+[\'"].*>)/'
        );
        return $this->_searchDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * The rule to check layout handles
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function ruleCheckLayoutHandles($currentModule, $fileType, $file, $contents)
    {
        $xml = simplexml_load_file($file);

        $area = $this->_getAreaByFile($file);

        $dependencies = array();
        foreach ($xml->xpath('/layout/child::*') as $element) {
            $result = $this->_checkLayoutHandleDependency($currentModule, $area, $element->getName());

            $module = isset($result['module']) ? $result['module'] : null;
            $message = isset($result['message']) ? $result['message'] : null;

            if ($module || $message) {
                $dependencies[] = array(
                    'module' => $module,
                    'source' => $element->getName(),
                    'message' => $message,
                );
            }
        }
        return $dependencies;
    }

    /**
     * The rule to check layout handles parents
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function ruleCheckLayoutHandlesParents($currentModule, $fileType, $file, $contents)
    {
        $xml = simplexml_load_file($file);

        $area = $this->_getAreaByFile($file);

        $dependencies = array();
        foreach ($xml->xpath('/layout/child::*/@parent') as $element) {
            $result = $this->_checkLayoutHandleDependency($currentModule, $area, (string)$element);

            $module = isset($result['module']) ? $result['module'] : null;
            $message = isset($result['message']) ? $result['message'] : null;

            if ($module || $message) {
                $dependencies[] = array(
                    'module' => $module,
                    'source' => (string)$element,
                    'message' => $message,
                );
            }
        }
        return $dependencies;
    }

    /**
     * The rule to check layout handles updates
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function ruleCheckLayoutHandlesUpdate($currentModule, $fileType, $file, $contents)
    {
        $xml = simplexml_load_file($file);

        $area = $this->_getAreaByFile($file);

        $dependencies = array();
        foreach ($xml->xpath('//update/@handle') as $element) {
            $result = $this->_checkLayoutHandleDependency($currentModule, $area, (string)$element);

            $module = isset($result['module']) ? $result['module'] : null;
            $message = isset($result['message']) ? $result['message'] : null;

            if ($module || $message) {
                $dependencies[] = array(
                    'module' => $module,
                    'source' => (string)$element,
                    'message' => $message,
                );
            }
        }
        return $dependencies;
    }

    /**
     * The rule to check layout references
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function ruleCheckLayoutReferences($currentModule, $fileType, $file, $contents)
    {
        $xml = simplexml_load_file($file);

        $area = $this->_getAreaByFile($file);

        $dependencies = array();
        foreach ($xml->xpath('//reference/@name') as $element) {
            $result = $this->_checkLayoutBlockDependency($currentModule, $area, (string)$element);

            $module = isset($result['module']) ? $result['module'] : null;
            $message = isset($result['message']) ? $result['message'] : null;

            if ($module || $message) {
                $dependencies[] = array(
                    'module' => $module,
                    'source' => (string)$element,
                    'message' => $message,
                );
            }
        }
        return $dependencies;
    }

    /**
     * Search dependencies for defined patterns
     *
     * @param $currentModule
     * @param $contents
     * @param array $patterns
     * @return array
     */
    protected function _searchDependenciesByRegexp($currentModule, $contents, $patterns = array())
    {
        $dependencies = array();
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $item) {
                    $moduleName = $item['namespace'] . '_' . $item['module'];
                    if ($currentModule != $moduleName) {
                        $dependencies[] = array(
                            'module' => $moduleName,
                            'source' => $item['source'],
                            'message' => null,
                        );
                    }
                }
            }
        }
        return $dependencies;
    }

    /**
     * Retrieve default module name (by area)
     *
     * @param string $area
     * @return null
     */
    protected function _getDefaultModuleName($area = 'default')
    {
        if (isset($this->_defaultModules[$area])) {
            return $this->_defaultModules[$area];
        }
        return null;
    }

    /**
     * Get area from file path
     *
     * @param $file
     * @return string
     */
    protected function _getAreaByFile($file)
    {
        $area = 'default';
        if (preg_match('/[\/](?<area>adminhtml|frontend)[\/]/', $file, $matches)) {
            $area = $matches['area'];
        }
        return $area;
    }

    /**
     * Check layout handle dependency
     *
     * Return: array(
     *  'module' - dependent module
     *  'source' - source row
     *  'message' - possible dependent modules or other message
     * )
     *
     * @param $currentModule
     * @param $area
     * @param $handleName
     * @return array
     */
    protected function _checkLayoutHandleDependency($currentModule, $area, $handleName)
    {
        $chunks = explode('_', $handleName);
        if (count($chunks) > 1) {
            array_pop($chunks);
        }

        // Check controllers tree
        $handlePrefix = implode('_', $chunks);
        if (isset(self::$_mapLayoutHandlesByControllers[$handlePrefix])) {
            $moduleName = self::$_mapLayoutHandlesByControllers[$handlePrefix];
            if ($currentModule != $moduleName) {
                return array('module' => $moduleName);
            }
        }

        // Check global layout handles tree
        if (isset(self::$_mapLayoutHandlesByFiles[$area][$handleName])) {

            // No dependencies
            $modules = self::$_mapLayoutHandlesByFiles[$area][$handleName];
            if (isset($modules[$currentModule])) {
                return array('module' => null);
            }

            // Single dependency
            if (1 == count($modules)) {
                return array('module' => current($modules));
            }

            // Default module dependency
            $defaultModule = $this->_getDefaultModuleName($area);
            if (isset($modules[$defaultModule])) {
                return array('module' => $defaultModule);
            }

            return array('message' => self::POSSIBLE_DEPENDENCIES . ' (' . implode(', ', $modules) . ')');
        }

        return array('message' => self::UNKNOWN_HANDLE . ' (' . $handleName . ')');
    }

    /**
     * Check layout block dependency
     *
     * Return: array(
     *  'module' - dependent module
     *  'source' - source row
     *  'message' - possible dependent modules or other message
     * )
     *
     * @param $currentModule
     * @param $area
     * @param $blockName
     * @return array
     */
    protected function _checkLayoutBlockDependency($currentModule, $area, $blockName)
    {
        if (isset(self::$_mapLayoutBlocks[$area][$blockName])) {
            // No dependencies
            $modules = self::$_mapLayoutBlocks[$area][$blockName];
            if (isset($modules[$currentModule])) {
                return array('module' => null);
            }

            // Single dependency
            if (1 == count($modules)) {
                return array('module' => current($modules));
            }

            // Default module dependency
            $defaultModule = $this->_getDefaultModuleName($area);
            if (isset($modules[$defaultModule])) {
                return array('module' => $defaultModule);
            }

            return array('message' => self::POSSIBLE_DEPENDENCIES . ' (' . implode(', ', $modules) . ')');
        }

        return array('message' => self::UNKNOWN_BLOCK . ' (' . $blockName . ')');
    }

    /**
     * Retrieve map of layout handles
     */
    protected static function _getMapLayoutHandles()
    {
        $configFiles = array();

        // Prepare list of config.xml files
        $files = Utility_Files::init()->getConfigFiles('config.xml', array(), false);
        foreach ($files as $file) {
            if (preg_match('/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/', $file, $matches)) {
                $name = $matches['namespace'] . '_' . $matches['module'];
                $configFiles[$name] = $file;
            }
        }

        // Prepare layout handles by controllers
        $files = Utility_Files::init()->getPhpFiles(true, false, false, false);
        foreach ($files as $file) {
            if (preg_match('/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)\/controllers\/(?<path>[\/\w]*)Controller.php/', $file, $matches)) {

                $chunks = explode('/', strtolower($matches['path']));
                $name = $matches['namespace'] . '_' . $matches['module'];

                // Read module's config.xml file
                $config = simplexml_load_file($configFiles[$name]);

                if ('adminhtml' == $chunks[0]) {
                    array_shift($chunks);
                    $nodes = $config->xpath("/config/admin/routers/*") ?: array();
                }
                else {
                    $nodes = $config->xpath("/config/frontend/routers/*") ?: array();
                    foreach ($nodes as $nodeKey => $node) {
                        // Exclude overrided routers
                        if ('' == (string)$node->args->frontName) {
                            unset($nodes[$nodeKey]);
                        }
                    }
                }

                $controllerName = implode('_', $chunks);

                foreach ($nodes as $node) {
                    $path = $node->getName() ? $node->getName() . '_' . $controllerName : $controllerName;
                    self::$_mapLayoutHandlesByControllers[$path] = $name;
                }
            }
        }

        // Prepare layout handles by files
        $files = Utility_Files::init()->getLayoutFiles(array(), false);
        foreach ($files as $file) {
            $area = 'default';
            if (preg_match('/[\/](?<area>adminhtml|frontend)[\/]/', $file, $matches)) {
                $area = $matches['area'];
                if (!isset(self::$_mapLayoutHandlesByFiles[$area])) {
                    self::$_mapLayoutHandlesByFiles[$area] = array();
                }
            }
            if (preg_match('/app\/code\/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/', $file, $matches)) {
                $name = $matches['namespace'] . '_' . $matches['module'];

                $xml = simplexml_load_file($file);
                foreach ($xml->xpath('/layout/child::*') as $element) {
                    if (!isset(self::$_mapLayoutHandlesByFiles[$area][$element->getName()])) {
                        self::$_mapLayoutHandlesByFiles[$area][$element->getName()] = array();
                    }
                    self::$_mapLayoutHandlesByFiles[$area][$element->getName()][$name] = $name;
                }
            }
        }
    }

    /**
     * Retrieve map of layout blocks
     */
    protected function _getMapLayoutBlocks()
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
                $name = $matches['namespace'] . '_' . $matches['module'];

                $xml = simplexml_load_file($file);

                foreach ($xml->xpath('//container | //block') as $element) {
                    $attributes = $element->attributes();
                    $blockName = (string)$attributes->name;

                    if (!empty($blockName)) {
                        if (!isset(self::$_mapLayoutBlocks[$area][$blockName])) {
                            self::$_mapLayoutBlocks[$area][$blockName] = array();
                        }
                        self::$_mapLayoutBlocks[$area][$blockName][$name] = $name;
                    }
                }
            }
        }
    }

    /**
     * Retrieve dataset with layout files names, namespaces and modules
     *
     * Return: array(file, namespace, module).
     *
     * @return array
     */
    public function getDataset()
    {
        if (!count($this->_dataset)) {
            $files = Utility_Files::init()->getLayoutFiles(array(), false);
            foreach ($files as $file) {
                if (preg_match('/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/', $file, $matches)) {
                    $this->_dataset[$file] = array(
                        'file' => $file,
                        'namespace' => $matches['namespace'],
                        'module' => $matches['module']);
                }
            }
        }
        return $this->_dataset;
    }
}
