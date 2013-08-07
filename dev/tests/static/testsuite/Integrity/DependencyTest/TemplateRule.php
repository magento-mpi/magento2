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
     *  'router' => 'module'
     * )
     *
     * @var array
     */
    protected $_mapRouters = array();

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

        $dependencies = array();
        foreach ($this->_cases as $case) {
            if (method_exists($this, $case)) {
                $result = $this->$case($currentModule, $fileType, $file, $contents);
                if (count($result)) {
                    $dependencies = array_merge($dependencies, $result);
                }
            }
        }
        return $dependencies;
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
                            'message' => null,
                        );
                    }
                }
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

        // Prepare output
        $dependencies = array();
        foreach ($result as $key => $val) {
            $dependencies[] = array(
                'module' => $key,
                'source' => $val,
            );
        }
        return $dependencies;
    }
}
