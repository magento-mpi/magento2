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
        $this->_prepareMapRouters();
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
            $result = $this->$case($currentModule, $fileType, $file, $contents);
            if (count($result)) {
                $dependencies = array_merge($dependencies, $result);
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
            '/(?<source>Mage::(?:getModel|getSingleton|getBlockSingleton)+\([\'"](?<namespace>[A-Z][a-z]+)[_]'
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
            '/(?<source>[$a-zA-Z0-9_\->:]+helper\([\'"](?<namespace>[A-Z][a-z]+)[_]'
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
            '/[\->:]+(?<source>createBlock\([\'"](?<namespace>[A-Z][a-z]+)[_](?<module>[A-Z][a-zA-Z]+)\w*[\'"]\))/',
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
            '/(?<source>(?<namespace>[A-Z][a-z]+)[_](?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-z]+_?){1,}::[A-Z_]+)/',
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
            '/(?<source>[$a-zA-Z0-9_\->:]+getViewFileUrl\([\'"](?<namespace>[A-Z][a-z]+)[_]'
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
     * Prepare map of modules routers
     */
    protected function _prepareMapRouters()
    {
        $this->_mapRouters = array();

        // Prepare list of config.xml files
        $configFiles = array();
        $files = Utility_Files::init()->getConfigFiles('config.xml', array(), false);
        foreach ($files as $file) {
            if (preg_match('/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/', $file, $matches)) {
                $name = $matches['namespace'] . '_' . $matches['module'];
                $configFiles[$name] = $file;
            }
        }

        // Prepare routers
        $pattern = '/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)\/controllers\/'
            . '(?<path>[\/\w]*)Controller.php/';

        $files = Utility_Files::init()->getPhpFiles(true, false, false, false);
        foreach ($files as $file) {
            if (preg_match($pattern, $file, $matches)) {

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
                        // Exclude overridden routers
                        if ('' == (string)$node->args->frontName) {
                            unset($nodes[$nodeKey]);
                        }
                    }
                }

                $controllerName = implode('_', $chunks);
                foreach ($nodes as $node) {
                    $path = $node->getName() ? $node->getName() . '_' . $controllerName : $controllerName;
                    $this->_mapRouters[$path] = $name;
                }
            }
        }
    }
}
