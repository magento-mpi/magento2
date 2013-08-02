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
    );

    /**
     * Dataset with layout files names, namespaces and modules
     *
     * @var array
     */
    protected $_dataset = array();

    /**
     * List layout handles associated with modules
     *
     * Format: array('router_controller' => 'Namespace_Module')
     *
     * @var array
     */
    protected static $_mapLayoutHandles = array();

    /**
     * Unknown layout handle mark
     */
    const UNKNOWN_HANDLE = 'UNKNOWN_HANDLE';

    /**
     * Unknown parent of layout handle mark
     */
    const UNKNOWN_HANDLE_PARENT = 'UNKNOWN_HANDLE_PARENT';

    /**
     * Initialize map
     */
    public static function setUpBeforeClass()
    {
        self::getMapLayoutHandles();
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
            $result = $this->$rule($file, $contents, $namespace, $module);
            if (count($result)) {
                $dependencies = array_merge($dependencies, $result);
            }
        }

        if (count($dependencies)) {
            $this->fail("Undeclared dependency found in $file.\nDependencies: " . implode(", ", $dependencies));
        }
    }

    /**
     * The rule to check dependencies for module="..." attribute
     *
     * @param $file
     * @param $contents
     * @param $namespace
     * @param $module
     * @return array
     */
    protected function ruleCheckAttributeModule($file, $contents, $namespace, $module)
    {
        $patterns = array(
            '/<.+module\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)[_](?<module>[A-Z][a-zA-Z]+)[\'"].*>/'
        );
        return $this->searchDependenciesByRegexp($contents, $namespace, $module, $patterns);
    }

    /**
     * The rule to check dependencies for <block> element
     *
     * Search dependencies for type="..." attribute.
     * Search dependencies for template="..." attribute.
     *
     * @param $file
     * @param $contents
     * @param $namespace
     * @param $module
     * @return array
     */
    protected function ruleCheckElementBlock($file, $contents, $namespace, $module)
    {
        $patterns = array(
            '/<block.*type\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-zA-Z]+_?){1,}[\'"].*>/',
            '/<block.*template\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.]+[\'"].*>/'
        );
        return $this->searchDependenciesByRegexp($contents, $namespace, $module, $patterns);
    }

    /**
     * The rule to check dependencies for <action> element
     *
     * Search dependencies for <block> element.
     * Search dependencies for <template> element.
     * Search dependencies for <file> element.
     * Search dependencies for helper="..." attribute.
     *
     * @param $file
     * @param $contents
     * @param $namespace
     * @param $module
     * @return array
     */
    protected function ruleCheckElementAction($file, $contents, $namespace, $module)
    {
        $patterns = array(
            '/<block\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-zA-Z]+_?){1,}<\/block\s*>/',
            '/<template\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.]+<\/template\s*>/',
            '/<file\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.-]+<\/file\s*>/',
            '<.*helper\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-z]+_?){1,}::[\w]+[\'"].*>'
        );
        return $this->searchDependenciesByRegexp($contents, $namespace, $module, $patterns);
    }

    /**
     * The rule to check layout handles
     *
     * @param $file
     * @param $contents
     * @param $namespace
     * @param $module
     * @return array
     */
    protected function ruleCheckLayoutHandles($file, $contents, $namespace, $module)
    {
        $xml = simplexml_load_file($file);

        $dependencies = array();
        foreach ($xml->xpath('/layout/child::*') as $element) {

            $chunks = explode('_', $element->getName());
            array_pop($chunks);
            $handlePrefix = implode('_', $chunks);

            if (isset(self::$_mapLayoutHandles[$handlePrefix])) {
                $name = self::$_mapLayoutHandles[$handlePrefix];
                if ($name != $namespace . '_' . $module) {
                    $dependencies[$name] = $name;
                }
            }
            else {
                $name = self::UNKNOWN_HANDLE . ' (' . $element->getName() . ')';
                $dependencies[$name] = $name;
            }
        }
        return $dependencies;
    }

    /**
     * The rule to check layout handles parents
     *
     * @param $file
     * @param $contents
     * @param $namespace
     * @param $module
     * @return array
     */
    protected function ruleCheckLayoutHandlesParents($file, $contents, $namespace, $module)
    {
        $xml = simplexml_load_file($file);

        $dependencies = array();
        foreach ($xml->xpath('/layout/child::*/@parent') as $element) {

            $parent = (string)$element;

            $chunks = explode('_', $parent);
            array_pop($chunks);
            $parentPrefix = implode('_', $chunks);

            if (isset(self::$_mapLayoutHandles[$parentPrefix])) {
                $name = self::$_mapLayoutHandles[$parentPrefix];
                if ($name != $namespace . '_' . $module) {
                    $dependencies[$name] = $name;
                }
            }
            else {
                $name = self::UNKNOWN_HANDLE_PARENT . ' (' . $parent . ')';
                $dependencies[$name] = $name;
            }
        }
        return $dependencies;
    }

    /**
     * Search dependencies for defined patterns
     *
     * @param $contents
     * @param $namespace
     * @param $module
     * @param array $patterns
     * @return array
     */
    protected function searchDependenciesByRegexp($contents, $namespace, $module, $patterns = array())
    {
        $dependencies = array();
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $item) {
                    if ($namespace != $item['namespace'] || $module != $item['module']) {
                        $name = $item['namespace'] . '_' . $item['module'];
                        $dependencies[$name] = $name;
                    }
                }
            }
        }
        return $dependencies;
    }

    /**
     * Retrieve map of layout handles
     *
     * @return array
     */
    protected function getMapLayoutHandles()
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

        // Process controllers files
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
                    self::$_mapLayoutHandles[$path] = $name;
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
