<?php
/**
 * Integrity test used to check, that all classes, written as direct class names in code, really exist
 *
 * {license_notice}
 *
 * @category    tests
 * @package     integration
 * @subpackage  integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_ClassesTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of methods in this class, that are designed to check file content.
     * Filled automatically via reflection.
     *
     * @var array
     */
    protected $_visitorMethods = null;

    /**
     * @param string $className
     * @dataProvider classExistsDataProvider
     */
    public function testClassExists($className)
    {
        if ($className == 'Mage_Catalog_Model_Resource_Convert') {
            $this->markTestSkipped('Bug MAGE-4763');
        }
        $this->assertTrue(class_exists($className), 'Class ' . $className . ' does not exist');
    }

    /**
     * @return array
     */
    public function classExistsDataProvider()
    {
        $classNames = $this->_findAllClassNames();

        $result = array();
        foreach ($classNames as $className) {
            $result[] = array($className);
        }
        return $result;
    }

    /**
     * Gathers all class name definitions in Magento
     *
     * @return array
     */
    protected function _findAllClassNames()
    {
        $directory  = new RecursiveDirectoryIterator(Mage::getRoot());
        $iterator = new RecursiveIteratorIterator($directory);
        $regexIterator = new RegexIterator($iterator, '/(\.php|\.phtml|\.xml)$/');

        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $classNames = $this->_findClassNamesInFile($fileInfo);
            $result = array_merge($result, $classNames);
        }
        return $result;
    }

    /**
     * Gathers all class name definitions in a class
     *
     * @param SplFileInfo $fileInfo
     * @return array
     */
    protected function _findClassNamesInFile($fileInfo)
    {
        $content = file_get_contents((string) $fileInfo);

        $result = array();
        $visitorMethods = $this->_getVisitorMethods();
        foreach ($visitorMethods as $method) {
            $classNames = $this->$method($fileInfo, $content);
            if (!$classNames) {
                continue;
            }
            $classNames = array_combine($classNames, $classNames); // Thus array_merge will not have duplicates
            $result = array_merge($result, $classNames);
        }

        return $result;
    }

    /**
     * Returns all methods in this class, that are designed to visit the file content.
     * Protected methods starting with '_visit' are considered to be visitor methods.
     *
     * @return array
     */
    protected function _getVisitorMethods()
    {
        if ($this->_visitorMethods === null) {
            $this->_visitorMethods = array();
            $reflection = new ReflectionClass($this);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PROTECTED) as $method) {
                if (substr($method->name, 0, 6) == '_visit') {
                    $this->_visitorMethods[] = $method->name;
                }
            }
        }

        return $this->_visitorMethods;
    }

    /**
     * Finds usage of Mage::getResourceModel('Class_Name'), Mage::getResourceSingleton('Class_Name')
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitMageGetResource($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $funcNames = array('Mage::getResourceModel', 'Mage::getResourceSingleton');
        $result = array();
        foreach ($funcNames as $funcName) {
            $classNames = $this->_getFuncStringArguments($funcName, $content);
            $result = array_merge($result, $classNames);
        }

        return $result;
    }

    /**
     * Checks whether file path has required extension
     *
     * @param string|array $extensions
     * @return bool
     */
    protected function _fileHasExtensions($fileInfo, $extensions)
    {
        if (is_string($extensions)) {
            $extensions = array($extensions);
        }

        $fileExtension = pathinfo($fileInfo->getBasename(), PATHINFO_EXTENSION);
        $key = array_search($fileExtension, $extensions);
        return ($key !== false);
    }

    /**
     * Finds all usages of function $funcName in $content, where it has only one constant string argument.
     * Returns array of all these arguments.
     *
     * @param string $funcName
     * @param string $content
     * @return array
     */
    protected function _getFuncStringArguments($funcName, $content)
    {
        $result = array();
        $matched = preg_match_all('/' . $funcName . '\([\'"]([^\'"]+)[\'"]\)/', $content, $matches);
        if ($matched) {
            $result = $matches[1];
        }
        return $result;
    }

    /**
     * Finds usage of Mage::getResourceHelper('Module_Name')
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitMageGetResourceHelper($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $modules = $this->_getFuncStringArguments('Mage::getResourceHelper', $content);
        if (!$modules) {
            return array();
        }

        $result = array();
        $dbSuffixes = array('Mysql4', 'Mssql', 'Oracle');
        foreach ($modules as $module) {
            foreach ($dbSuffixes as $dbSuffix) {
                $result[] = $module . '_Model_Resource_Helper_' . $dbSuffix;
            }
        }

        return $result;
    }

    /**
     * Finds usage of staging resource adapters across xml configs
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitStagingResourceAdapters($fileInfo, $content)
    {
        if ($fileInfo->getBasename() != 'config.xml') {
            return array();
        }

        $xml = new SimpleXMLElement($content);
        $resourceAdapters = $xml->xpath('/config/global/enterprise/staging/staging_items/*/resource_adapter');
        if (!$resourceAdapters) {
            return array();
        }

        $result = array();
        foreach ($resourceAdapters as $resourceAdapter) {
            $result[] = (string) $resourceAdapter;
        }

        return $result;
    }

    /**
     * Finds usage of helpers in php files through helper function
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitHelperFunctionCalls($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $result = array();
        $modules = $this->_getFuncStringArguments('helper', $content);
        if ($modules) {
            $result = array_merge($result, $modules);
        }

        $modules = $this->_getFuncStringArguments('setDataHelperName', $content);
        if ($modules) {
            $result = array_merge($result, $modules);
        }

        $combine = array();
        $matched = preg_match_all('/addProductConfigurationHelper\(.*,[\'"](.*)[\'"]\)/', $content, $matches);
        if ($matched) {
            $combine = array_merge($combine, $matches[1]);
        }

        $matched = preg_match_all('/addOptionsRenderCfg\(.*,[\'"](.*)[\'"],.*\)/', $content, $matches);
        if ($matched) {
            $combine = array_merge($combine, $matches[1]);
        }

        foreach ($combine as $match) {
            if (strpos($match, '$') === FALSE) {
                continue;
            }
            $result[] = $match;
        }

        return $result;
    }

    /**
     * Finds usage of helpers in layout files through attributes and layouts
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitXmlAttributeDefinitions($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('xml'))) {
            return array();
        }

        $result = array();
        $matched = preg_match_all('/helper="(.*)::.*"/Us', $content, $matches);
        if ($matched) {
            $result = array_merge($result, $matches[1]);
        }

        $matched = preg_match_all('/module="(.*)"/Us', $content, $matches);
        if ($matched) {
            foreach ($matches[1] as $module) {
                $result[] = $module . '_Helper_Data';
            }
        }

        $matched = preg_match_all('/method="addProductConfigurationHelper"><type>.*<\/type><name>(.*)<\/name>/',
            $content, $matches);
        if ($matched) {
            $result = array_merge($result, $matches[1]);
        }

        $matched = preg_match_all('/method="addOptionsRenderCfg"><type>.*<\/type><helper>(.*)<\/helper>/',
            $content, $matches);
        if ($matched) {
            $result = array_merge($result, $matches[1]);
        }

        return $result;
    }

    /**
     * Finds methods that return collection names for grid blocks
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitCollectionNamesInGridBlocks($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, 'php')) {
            return array();
        }

        $regexp = ' _getCollectionClass\(\)\s+';
        $regexp .= '{\s+';
        $regexp .= 'return\s+[\'"]([a-zA-Z_\/]+)[\'"];';
        $matched = preg_match_all('/' . $regexp . '/', $content, $matches);

        if (!$matched) {
            return array();
        }
        return $matches[1];
    }

    /**
     * Finds usage of initReport('Class_Name')
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitInitReport($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        return $this->_getFuncStringArguments('->initReport', $content);
    }
}
