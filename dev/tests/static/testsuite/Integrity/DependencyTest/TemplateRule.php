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
class Integrity_DependencyTest_TemplateRule implements Integrity_DependencyTest_RuleInterface
{
    /**
     * Cases to search dependencies
     *
     * @var array
     */
    protected $_cases = array(
        '_caseModelSingleton',
        '_caseHelper',
        '_caseCreateBlock',
        '_caseConstant',
        '_caseAddFile',
        '_caseGetUrl',
        '_caseLayoutBlock',
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
     * Namespaces to analyze
     *
     * Format: {Namespace}|{Namespace}|...
     *
     * @var string
     */
    protected $_namespaces;

    /**
     * List of routers
     *
     * Format: array(
     *  '{Router}' => '{Module_Name}'
     * )
     *
     * @var array
     */
    protected $_mapRouters = array();

    /**
     * List of layout blocks associated with modules
     *
     * Format: array(
     *  '{Area}' => array(
     *   '{Block_Name}' => array('{Module_Name}' => '{Module_Name}')
     * ))
     *
     * @var array
     */
    protected $_mapLayoutBlocks = array();

    /**
     * List of exceptions
     *
     * Format: array(
     *  '{Exception_Type}' => '{Source}'
     * )
     *
     * @var array
     */
    protected $_exceptions = array();

    /**
     * Exceptions flag
     *
     * @var bool
     */
    protected $_isExceptionsAllowed = false;

    /**
     * Unknown layout block
     */
    const EXCEPTION_TYPE_UNKNOWN_BLOCK = 'UNKNOWN_BLOCK';

    /**
     * Undefined dependency
     */
    const EXCEPTION_TYPE_UNDEFINED_DEPENDENCY = 'UNDEFINED_DEPENDENCY';

    /**
     * Constructor
     */
    public function __construct()
    {
        $args = func_get_args();
        if (count($args)) {
            if (isset($args[0]['mapRouters'])) {
                $this->_mapRouters = $args[0]['mapRouters'];
            }
            if (isset($args[0]['mapLayoutBlocks'])) {
                $this->_mapLayoutBlocks = $args[0]['mapLayoutBlocks'];
            }
        }

        $this->_namespaces = implode('|', Utility_Files::init()->getNamespaces());
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
        if (!in_array($fileType, array('template'))) {
            return array();
        }

        $this->_exceptions = array();

        $dependencies = array();
        foreach ($this->_cases as $case) {
            if (method_exists($this, $case)) {
                $result = $this->$case($currentModule, $fileType, $file, $contents);
                if (count($result)) {
                    $dependencies = array_merge($dependencies, $result);
                }
            }
        }
        return array_merge($dependencies, $this->_applyExceptions());
    }

    /**
     * Apply exceptions
     *
     * @return array
     */
    protected function _applyExceptions()
    {
        if (!$this->_isExceptionsAllowed) {
            return array();
        }

        $result = array();
        foreach ($this->_exceptions as $type => $source) {
            if (is_array($source)) {
                $source = array_keys($source);
            }
            $result[] = array(
                'exception' => $type,
                'module' => '',
                'source' => $source,
            );
        }
        return $result;
    }

    /**
     * Check models calls
     *
     * Ex.: Mage::getModel('{Class_Name}')
     *      Mage::getSingleton('{Class_Name}')
     *      Mage::getBlockSingleton('{Class_Name}')
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseModelSingleton($currentModule, $fileType, $file, &$contents)
    {
        $patterns = array(
            '/(?<source>Mage::(?:getModel|getSingleton|getBlockSingleton)+\([\'"]'
                . '(?<namespace>' . $this->_namespaces . ')_'
                . '(?<module>[A-Z][a-zA-Z]+)\w*[\'"]\))/',
        );
        return $this->_checkDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * Check helpers calls
     *
     * Ex.: Mage::helper('{Class_Name}')
     *      $this->helper('{Class_Name}')
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseHelper($currentModule, $fileType, $file, &$contents)
    {
        $patterns = array(
            '/(?<source>[$a-zA-Z0-9_\->:]+helper\([\'"](?<namespace>' . $this->_namespaces . ')_'
                . '(?<module>[A-Z][a-zA-Z]+)\w*[\'"]\))/',
        );
        return $this->_checkDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * Check createBlock() methods
     *
     * Ex.: createBlock('{Class_Name}')
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseCreateBlock($currentModule, $fileType, $file, &$contents)
    {
        $patterns = array(
            '/[\->:]+(?<source>createBlock\([\'"](?<namespace>' . $this->_namespaces . ')_'
                . '(?<module>[A-Z][a-zA-Z]+)\w*[\'"]\))/',
        );
        return $this->_checkDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * Check using of constants
     *
     * Ex.: {Class_Name}::{Constant_Name}
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseConstant($currentModule, $fileType, $file, &$contents)
    {
        $patterns = array(
            '/(?<source>(?<namespace>' . $this->_namespaces . ')_(?<module>[A-Z][a-zA-Z]+)_'
                . '(?:[A-Z][a-z]+_?){1,}::[A-Z_]+)/',
        );
        return $this->_checkDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * Check adding additional files
     *
     * Ex.: $this->getViewFileUrl('{Module_name}::{File_Name}')
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseAddFile($currentModule, $fileType, $file, &$contents)
    {
        $patterns = array(
            '/(?<source>[$a-zA-Z0-9_\->:]+getViewFileUrl\([\'"](?<namespace>' . $this->_namespaces . ')_'
                . '(?<module>[A-Z][a-zA-Z]+)::[\w\/\.-]+[\'"]\))/',
        );
        return $this->_checkDependenciesByRegexp($currentModule, $contents, $patterns);
    }

    /**
     * Check get URL method
     *
     * Ex.: getUrl('{path}')
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseGetUrl($currentModule, $fileType, $file, &$contents)
    {
        $pattern = '/[\->:]+(?<source>getUrl\([\'"](?<router>[\w\/*]+)[\'"])/';

        $dependencies = array();
        if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $item) {
                $router = str_replace('/', '_', $item['router']);
                if (isset($this->_mapRouters[$router])) {
                    $moduleName = $this->_mapRouters[$router];
                    if ($currentModule != $moduleName) {
                        $dependencies[] = array(
                            'module' => $moduleName,
                            'source' => $item['source'],
                        );
                    }
                }
            }
        }
        return $dependencies;
    }

    /**
     * Check layout blocks
     *
     * @param $currentModule
     * @param $fileType
     * @param $file
     * @param $contents
     * @return array
     */
    protected function _caseLayoutBlock($currentModule, $fileType, $file, &$contents)
    {
        $pattern = '/[\->:]+(?<source>(?:getBlock|getBlockHtml)\([\'"](?<block>[\w\.\-]+)[\'"]\))/';

        $area = $this->_getAreaByFile($file);

        $result = array();
        if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $check = $this->_checkDependencyLayoutBlock($currentModule, $area, $match['block']);
                $module = isset($check['module']) ? $check['module'] : null;
                $exception = isset($check['exception']) ? $check['exception'] : null;
                if ($module) {
                    $result[$module] = $match['source'];
                } elseif ($exception) {
                    $this->_exceptions[] = array($exception, $match['source']);
                }
            }
        }
        return $this->_getUniqueDependencies($result);
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
        $result = array();
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $module = $match['namespace'] . '_' . $match['module'];
                    if ($currentModule != $module) {
                        $result[$module] = $match['source'];
                    }
                }
            }
        }
        return $this->_getUniqueDependencies($result);
    }

    /**
     * Check layout block dependency
     *
     * Return: array(
     *  'module'  // dependent module
     *  'source'  // source text
     * )
     *
     * @param $currentModule
     * @param $area
     * @param $block
     * @return array
     */
    protected function _checkDependencyLayoutBlock($currentModule, $area, $block)
    {
        if (isset($this->_mapLayoutBlocks[$area][$block])) {
            // CASE 1: No dependencies
            $modules = $this->_mapLayoutBlocks[$area][$block];
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

            // CASE 4: Exception - Undefined dependency
            $this->_exceptions[self::EXCEPTION_TYPE_UNDEFINED_DEPENDENCY][] = implode('|', $modules);
        }

        // CASE 5: Exception - Undefined block
        $this->_exceptions[self::EXCEPTION_TYPE_UNKNOWN_BLOCK][$block] = $block;
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
        if (preg_match('/\/(?<area>adminhtml|frontend)\//', $file, $matches)) {
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

    /**
     * Retrieve unique dependencies
     *
     * @param array $dependencies
     * @return array
     */
    protected function _getUniqueDependencies($dependencies = array())
    {
        $result = array();
        foreach ($dependencies as $key => $val) {
            $result[] = array(
                'module' => $key,
                'source' => $val,
            );
        }
        return $result;
    }
}
