<?php
/**
 * Scan source code for references to classes and see if they indeed exist
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_ClassesTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of already found classes to avoid checking them over and over again
     *
     * @var array
     */
    protected static $_existingClasses = array();

    /**
     * @param string $file
     * @dataProvider phpFileDataProvider
     */
    public function testPhpFile($file)
    {
        $contents = file_get_contents($file);
        $classes = Utility_Classes::getAllMatches($contents, '/
            # ::getResourceModel ::getBlockSingleton ::getModel ::getSingleton
            \:\:get(?:ResourceModel | BlockSingleton | Model | Singleton)?\(\s*[\'"]([a-z\d_]+)[\'"]\s*[\),]

            # various methods, first argument
            | \->(?:initReport | addBlock | createBlock | setDataHelperName | _?initLayoutMessages
                | setAttributeModel | setBackendModel | setFrontendModel | setSourceModel | setModel
            )\(\s*\'([a-z\d_]+)\'\s*[\),]

            # various methods, second argument
            | \->add(?:ProductConfigurationHelper | OptionsRenderCfg)\(.+?,\s*\'([a-z\d_]+)\'\s*[\),]

            # Mage::helper ->helper
            | (?:Mage\:\:|\->)helper\(\s*\'([a-z\d_]+)\'\s*\)

            # misc
            | function\s_getCollectionClass\(\)\s+{\s+return\s+[\'"]([a-z\d_]+)[\'"]
            | \'resource_model\'\s*=>\s*[\'"]([a-z\d_]+)[\'"]
            | (?:_parentResourceModelName | _checkoutType | _apiType)\s*=\s*\'([a-z\d_]+)\'
            | \'renderer\'\s*=>\s*\'([a-z\d_]+)\'
            /ix'
        );

        // without modifier "i". Starting from capital letter is a significant characteristic of a class name
        Utility_Classes::getAllMatches($contents, '/(?:\-> | parent\:\:)(?:_init | setType)\(\s*
                \'([A-Z][a-z\d][A-Za-z\d_]+)\'(?:,\s*\'([A-Z][a-z\d][A-Za-z\d_]+)\')
            \s*\)/x',
            $classes
        );

        $this->_collectResourceHelpersPhp($contents, $classes);

        $this->_assertClassesExist($classes);

        $this->_assertValidNamespace($file, $contents);
    }

    /**
     * @return array
     */
    public function phpFileDataProvider()
    {
        return Utility_Files::init()->getPhpFiles();
    }

    /**
     * Special case: collect resource helper references in PHP-code
     *
     * @param string $contents
     * @param array &$classes
     */
    protected function _collectResourceHelpersPhp($contents, &$classes)
    {
        $regex = '/(?:\:\:|\->)getResourceHelper\(\s*\'([a-z\d_]+)\'\s*\)/ix';
        $matches = Utility_Classes::getAllMatches($contents, $regex);
        foreach ($matches as $moduleName) {
            $classes[] = "{$moduleName}_Model_Resource_Helper_Mysql4";
        }
    }

    /**
     * @param string $path
     * @dataProvider configFileDataProvider
     */
    public function testConfigFile($path)
    {
        $classes = Utility_Classes::collectClassesInConfig(simplexml_load_file($path));
        $this->_assertClassesExist($classes);
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return Utility_Files::init()->getConfigFiles();
    }

    /**
     * @param string $path
     * @dataProvider layoutFileDataProvider
     */
    public function testLayoutFile($path)
    {
        $xml = simplexml_load_file($path);

        $classes = Utility_Classes::getXmlNodeValues($xml,
            '/layout//*[contains(text(), "_Block_") or contains(text(), "_Model_") or contains(text(), "_Helper_")]'
        );
        foreach (Utility_Classes::getXmlAttributeValues($xml, '/layout//@helper', 'helper') as $class) {
            $classes[] = Utility_Classes::getCallbackClass($class);
        }
        foreach (Utility_Classes::getXmlAttributeValues($xml, '/layout//@module', 'module') as $module) {
            $classes[] = "{$module}_Helper_Data";
        }
        $classes = array_merge($classes, Utility_Classes::collectLayoutClasses($xml));

        $this->_assertClassesExist(array_unique($classes));
    }

    /**
     * @return array
     */
    public function layoutFileDataProvider()
    {
        return Utility_Files::init()->getLayoutFiles();
    }

    /**
     * Check whether specified classes correspond to a file according PSR-0 standard
     *
     * Cyclomatic complexity is because of temporary marking test as incomplete
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $classes
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertClassesExist($classes)
    {
        if (!$classes) {
            return;
        }
        $badClasses = array();
        $isBug = false;
        foreach ($classes as $class) {
            try {
                if ('Magento_Catalog_Model_Resource_Convert' == $class) {
                    $isBug = true;
                    continue;
                }
                $this->assertTrue(isset(self::$_existingClasses[$class])
                    || Utility_Files::init()->classFileExists($class)
                    || Utility_Classes::isVirtual($class)
                    || Utility_Classes::isAutogenerated($class)
                );
                self::$_existingClasses[$class] = 1;
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $badClasses[] = $class;
            }
        }
        if ($badClasses) {
            $this->fail("Missing files with declaration of classes:\n" . implode("\n", $badClasses));
        }
        if ($isBug) {
            $this->markTestIncomplete('Bug MAGE-4763');
        }
    }

    /**
     * Assert PHP classes have valid pseudo-namespaces according to file locations
     *
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $file
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertValidNamespace($file, $contents)
    {
        $relativePath = str_replace(Utility_Files::init()->getPathToSource(), "", $file);

        $classPattern = '/class\s[A-Z][^\s\/]+/';
        $namespacePattern = '/[A-Z][a-z]+\/[a-zA-Z]+[^\.]+/';

        $classNameMatch = array();
        $namespaceMatch = array();
        $className = null;
        $namespace = null;

        // exception made because the file is already using formal namespace
        if ($relativePath == "/app/code/Zend/Soap/Wsdl.php") {
            return;
        }

        // if no class declaration found for $file, then skip this file
        if (!preg_match($classPattern, $contents, $classNameMatch) != 0) {
            return;
        }
        $className = substr($classNameMatch[0], 6);

        if (preg_match($namespacePattern, $relativePath, $namespaceMatch) != 0) {
            $namespace = str_replace('/', '_', $namespaceMatch[0]);
        }
        if ($className != null && $namespace != null) {
            $this->assertEquals($className, $namespace);
        }
    }
}
