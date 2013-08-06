<?php
/**
 * Rule for searching dependencies in layout files
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_DependencyTest_LayoutRule implements Integrity_DependencyTest_RuleInterface
{
    /**
     * Cases to search dependencies
     *
     * @var array
     */
    protected $_cases = array(
        '_caseAttributeModule',
        '_caseElementBlock',
        '_caseElementAction',
        '_caseLayoutHandle',
        '_caseLayoutHandleParent',
        '_caseLayoutHandleUpdate',
        '_caseLayoutReference',
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
     * List of layout handles that was got by controllers
     *
     * Format: array(
     *  'handle_prefix' => 'module'
     * )
     *
     * @var array
     */
    protected $_mapLayoutHandlesByControllers = array();

    /**
     * List of layout handles that was got by layout files
     *
     * Format: array(
     *  'area' => array(
     *   'handle' => array('module' => 'module')
     * ))
     *
     * @var array
     */
    protected $_mapLayoutHandlesByFiles = array();

    /**
     * List of layout blocks associated with modules
     *
     * Format: array(
     *  'area' => array(
     *   'block' => array('module' => 'module')
     * ))
     *
     * @var array
     */
    protected $_mapLayoutBlocks = array();

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
     * Constructor
     */
    public function __construct()
    {
        $this->_prepareMapLayoutHandles();
        $this->_prepareMapLayoutBlocks();
    }

    /**
     * Retrieve dependencies information for current module
     *
     * @param string $currentModule
     * @param string $fileType
     * @param string $file
     * @param string $contents
     * @return array
     */
    public function getDependencyInfo($currentModule, $fileType, $file, &$contents)
    {
        if (!in_array($fileType, array('layout'))) {
            return array();
        }

        $dependencies = array();
        foreach ($this->_cases as $case) {
            $result = $this->$case($currentModule, $fileType, $file, $contents);
            if (count($result)) {
                $dependencies = array_merge($dependencies, $result);
            }
        }
        return $dependencies;
    }

    /**
     * Check dependencies for 'module' attribute
     *
     * Ex.: <element module="{module}">
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseAttributeModule($currentModule, $fileType, $file, $contents)
    {
        $patterns = array(
            '/(?<source><.+module\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)[_](?<module>[A-Z][a-zA-Z]+)[\'"].*>)/'
        );
        return $this->_checkDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * Check dependencies for <block> element
     *
     * Ex.: <block type="{name}">
     *      <block template="{path}">
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseElementBlock($currentModule, $fileType, $file, $contents)
    {
        $patterns = array(
            '/(?<source><block.*type\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-zA-Z]+_?){1,}[\'"].*>)/',
            '/(?<source><block.*template\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.]+[\'"].*>)/'
        );
        return $this->_checkDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * Check dependencies for <action> element
     *
     * Ex.: <block>{name}
     *      <template>{path}
     *      <file>{path}
     *      <element helper="{name}">
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseElementAction($currentModule, $fileType, $file, $contents)
    {
        $patterns = array(
            '/(?<source><block\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-zA-Z]+_?){1,}<\/block\s*>)/',
            '/(?<source><template\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.]+<\/template\s*>)/',
            '/(?<source><file\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.-]+<\/file\s*>)/',
            '/(?<source><.*helper\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-z]+_?){1,}::[\w]+[\'"].*>)/'
        );
        return $this->_checkDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * Check layout handles
     *
     * Ex.: <layout><{name}>...</layout>
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseLayoutHandle($currentModule, $fileType, $file, $contents)
    {
        $xml = simplexml_load_file($file);

        $area = $this->_getAreaByFile($file);

        $dependencies = array();
        foreach ($xml->xpath('/layout/child::*') as $element) {
            $result = $this->_checkDependencyLayoutHandle($currentModule, $area, $element->getName());

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
     * Check layout handles parents
     *
     * Ex.: <layout_name  parent="{name}">
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseLayoutHandleParent($currentModule, $fileType, $file, $contents)
    {
        $xml = simplexml_load_file($file);

        $area = $this->_getAreaByFile($file);

        $dependencies = array();
        foreach ($xml->xpath('/layout/child::*/@parent') as $element) {
            $result = $this->_checkDependencyLayoutHandle($currentModule, $area, (string)$element);

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
     * Check layout handles updates
     *
     * Ex.: <update handle="{name}" />
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseLayoutHandleUpdate($currentModule, $fileType, $file, $contents)
    {
        $xml = simplexml_load_file($file);

        $area = $this->_getAreaByFile($file);

        $dependencies = array();
        foreach ($xml->xpath('//update/@handle') as $element) {
            $result = $this->_checkDependencyLayoutHandle($currentModule, $area, (string)$element);

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
     * Check layout references
     *
     * Ex.: <reference name="{name}">
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseLayoutReference($currentModule, $fileType, $file, $contents)
    {
        $xml = simplexml_load_file($file);

        $area = $this->_getAreaByFile($file);

        $dependencies = array();
        foreach ($xml->xpath('//reference/@name') as $element) {
            $result = $this->_checkDependencyLayoutBlock($currentModule, $area, (string)$element);

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
     * Search dependencies by defined regexp patterns
     *
     * @param $currentModule
     * @param $contents
     * @param array $patterns
     * @return array
     */
    protected function _checkDependenciesByRegexp($currentModule, $contents, $patterns = array())
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
     * Check layout handle dependency
     *
     * Return: array(
     *  'module'  // dependent module
     *  'source'  // source text
     *  'message' // additional information
     * )
     *
     * @param $currentModule
     * @param $area
     * @param $handleName
     * @return array
     */
    protected function _checkDependencyLayoutHandle($currentModule, $area, $handleName)
    {
        $chunks = explode('_', $handleName);
        if (count($chunks) > 1) {
            array_pop($chunks); // Remove 'action' part from handle name
        }
        $handleBase = implode('_', $chunks);
        if (isset($this->_mapLayoutHandlesByControllers[$handleBase])) {
            // CASE 1: Single dependency
            $moduleName = $this->_mapLayoutHandlesByControllers[$handleBase];
            if ($currentModule != $moduleName) {
                return array('module' => $moduleName);
            }
        }

        if (isset($this->_mapLayoutHandlesByFiles[$area][$handleName])) {
            // CASE 2: No dependencies
            $modules = $this->_mapLayoutHandlesByFiles[$area][$handleName];
            if (isset($modules[$currentModule])) {
                return array('module' => null);
            }

            // CASE 3: Single dependency
            if (1 == count($modules)) {
                return array('module' => current($modules));
            }

            // CASE 4: Default module dependency
            $defaultModule = $this->_getDefaultModuleName($area);
            if (isset($modules[$defaultModule])) {
                return array('module' => $defaultModule);
            }

            // CASE 5: Additional information
            return array('message' => self::POSSIBLE_DEPENDENCIES . ' (' . implode(', ', $modules) . ')');
        }

        // CASE 6: Undefined handle name
        return array('message' => self::UNKNOWN_HANDLE . ' (' . $handleName . ')');
    }

    /**
     * Check layout block dependency
     *
     * Return: array(
     *  'module'  // dependent module
     *  'source'  // source text
     *  'message' // additional information
     * )
     *
     * @param $currentModule
     * @param $area
     * @param $blockName
     * @return array
     */
    protected function _checkDependencyLayoutBlock($currentModule, $area, $blockName)
    {
        if (isset($this->_mapLayoutBlocks[$area][$blockName])) {
            // CASE 1: No dependencies
            $modules = $this->_mapLayoutBlocks[$area][$blockName];
            if (isset($modules[$currentModule])) {
                return array('module' => null);
            }

            // CASE 2: Single dependency
            if (1 == count($modules)) {
                return array('module' => current($modules));
            }

            // CASE 3: Default module dependency
            $defaultModule = $this->_getDefaultModuleName($area);
            if (isset($modules[$defaultModule])) {
                return array('module' => $defaultModule);
            }

            // CASE 4: Additional information
            return array('message' => self::POSSIBLE_DEPENDENCIES . ' (' . implode(', ', $modules) . ')');
        }

        // CASE 5: Undefined block name
        return array('message' => self::UNKNOWN_BLOCK . ' (' . $blockName . ')');
    }

    /**
     * Prepare map of layout handles
     */
    protected function _prepareMapLayoutHandles()
    {
        $this->_mapLayoutHandlesByControllers = array();

        // Prepare list of config.xml files
        $configFiles = array();
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
                    $this->_mapLayoutHandlesByControllers[$path] = $name;
                }
            }
        }

        // Prepare layout handles by files
        $files = Utility_Files::init()->getLayoutFiles(array(), false);
        foreach ($files as $file) {
            $area = 'default';
            if (preg_match('/[\/](?<area>adminhtml|frontend)[\/]/', $file, $matches)) {
                $area = $matches['area'];
                if (!isset($this->_mapLayoutHandlesByFiles[$area])) {
                    $this->_mapLayoutHandlesByFiles[$area] = array();
                }
            }
            if (preg_match('/app\/code\/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/', $file, $matches)) {
                $name = $matches['namespace'] . '_' . $matches['module'];

                $xml = simplexml_load_file($file);
                foreach ($xml->xpath('/layout/child::*') as $element) {
                    if (!isset($this->_mapLayoutHandlesByFiles[$area][$element->getName()])) {
                        $this->_mapLayoutHandlesByFiles[$area][$element->getName()] = array();
                    }
                    $this->_mapLayoutHandlesByFiles[$area][$element->getName()][$name] = $name;
                }
            }
        }
    }

    /**
     * Prepare map of layout blocks
     */
    protected function _prepareMapLayoutBlocks()
    {
        $files = Utility_Files::init()->getLayoutFiles(array(), false);
        foreach ($files as $file) {
            $area = 'default';
            if (preg_match('/[\/](?<area>adminhtml|frontend)[\/]/', $file, $matches)) {
                $area = $matches['area'];
                if (!isset($this->_mapLayoutBlocks[$area])) {
                    $this->_mapLayoutBlocks[$area] = array();
                }
            }
            if (preg_match('/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/', $file, $matches)) {
                $name = $matches['namespace'] . '_' . $matches['module'];

                $xml = simplexml_load_file($file);

                foreach ($xml->xpath('//container | //block') as $element) {
                    $attributes = $element->attributes();
                    $blockName = (string)$attributes->name;

                    if (!empty($blockName)) {
                        if (!isset($this->_mapLayoutBlocks[$area][$blockName])) {
                            $this->_mapLayoutBlocks[$area][$blockName] = array();
                        }
                        $this->_mapLayoutBlocks[$area][$blockName][$name] = $name;
                    }
                }
            }
        }
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
}
