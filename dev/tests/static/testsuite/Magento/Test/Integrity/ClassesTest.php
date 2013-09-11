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
class Magento_Test_Integrity_ClassesTest extends PHPUnit_Framework_TestCase
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
        $classes = Magento_TestFramework_Utility_Classes::getAllMatches($contents, '/
            # ::getResourceModel ::getBlockSingleton ::getModel ::getSingleton
            \:\:get(?:ResourceModel | BlockSingleton | Model | Singleton)?\(\s*[\'"]([a-z\d\\\\]+)[\'"]\s*[\),]

            # various methods, first argument
            | \->(?:initReport | addBlock | createBlock | setDataHelperName | _?initLayoutMessages
                | setAttributeModel | setBackendModel | setFrontendModel | setSourceModel | setModel
            )\(\s*\'([a-z\d\\\\]+)\'\s*[\),]

            # various methods, second argument
            | \->add(?:ProductConfigurationHelper | OptionsRenderCfg)\(.+?,\s*\'([a-z\d\\\\]+)\'\s*[\),]

            # Mage::helper ->helper
            | (?:Mage\:\:|\->)helper\(\s*\'([a-z\d\\\\]+)\'\s*\)

            # misc
            | function\s_getCollectionClass\(\)\s+{\s+return\s+[\'"]([a-z\d\\\\]+)[\'"]
            | \'resource_model\'\s*=>\s*[\'"]([a-z\d\\\\]+)[\'"]
            | (?:_parentResourceModelName | _checkoutType | _apiType)\s*=\s*\'([a-z\d\\\\]+)\'
            | \'renderer\'\s*=>\s*\'([a-z\d\\\\]+)\'
            /ix'
        );

        // without modifier "i". Starting from capital letter is a significant characteristic of a class name
        Magento_TestFramework_Utility_Classes::getAllMatches($contents, '/(?:\-> | parent\:\:)(?:_init | setType)\(\s*
                \'([A-Z][a-z\d][A-Za-z\d\\\\]+)\'(?:,\s*\'([A-Z][a-z\d][A-Za-z\d\\\\]+)\')
            \s*\)/x',
            $classes
        );

        $this->_collectResourceHelpersPhp($contents, $classes);

        $this->_assertClassesExist($classes, $file);
    }

    /**
     * @return array
     */
    public function phpFileDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getPhpFiles();
    }

    /**
     * Special case: collect resource helper references in PHP-code
     *
     * @param string $contents
     * @param array &$classes
     */
    protected function _collectResourceHelpersPhp($contents, &$classes)
    {
        $regex = '/(?:\:\:|\->)getResourceHelper\(\s*\'([a-z\d\\\\]+)\'\s*\)/ix';
        $matches = Magento_TestFramework_Utility_Classes::getAllMatches($contents, $regex);
        foreach ($matches as $moduleName) {
            $classes[] = "{$moduleName}\\Model\\Resource\\Helper\\Mysql4";
        }
    }

    /**
     * @param string $path
     * @dataProvider configFileDataProvider
     */
    public function testConfigFile($path)
    {
        //The following 6 lines are used to exclude */logging.xml files for now
        $relativePath = str_replace(Magento_TestFramework_Utility_Files::init()->getPathToSource(), "", $path);
        $fileParts = explode('/', $relativePath);
        $fileName = array_pop($fileParts);
        if ($fileName == "logging.xml") {
            return;
        }
        $classes = Magento_TestFramework_Utility_Classes::collectClassesInConfig(simplexml_load_file($path));
        $this->_assertClassesExist($classes, $path);
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getConfigFiles();
    }

    /**
     * @param string $path
     * @dataProvider layoutFileDataProvider
     */
    public function testLayoutFile($path)
    {
        $xml = simplexml_load_file($path);

        $classes = Magento_TestFramework_Utility_Classes::getXmlNodeValues($xml,
            '/layout//*[contains(text(), "\\\\Block\\\\") or contains(text(), "\\\\Model\\\\") or contains(text(), "\\\\Helper\\\\")]'
        );
        foreach (Magento_TestFramework_Utility_Classes::getXmlAttributeValues($xml,
            '/layout//@helper', 'helper') as $class) {
            $classes[] = Magento_TestFramework_Utility_Classes::getCallbackClass($class);
        }
        foreach (Magento_TestFramework_Utility_Classes::getXmlAttributeValues($xml,
            '/layout//@module', 'module') as $module) {
            $classes[] = str_replace('_', '\\', "{$module}_Helper_Data");
        }
        $classes = array_merge($classes, Magento_TestFramework_Utility_Classes::collectLayoutClasses($xml));

        $this->_assertClassesExist(array_unique($classes), $path);
    }

    /**
     * @return array
     */
    public function layoutFileDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getLayoutFiles();
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
    protected function _assertClassesExist($classes, $path = false)
    {
        if (!$classes) {
            return;
        }
        $badClasses = array();
        $badUsages = array();
        foreach ($classes as $class) {
            try {
                if ($path != false && strrchr($class, '\\') == false) {
                    $badUsages[] = $class;
                    continue;
                }
                else {
                    $this->assertTrue(isset(self::$_existingClasses[$class])
                        || Magento_TestFramework_Utility_Files::init()->classFileExists($class)
                        || Magento_TestFramework_Utility_Classes::isVirtual($class)
                        || Magento_TestFramework_Utility_Classes::isAutogenerated($class)
                    );
                }
                self::$_existingClasses[$class] = 1;
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $badClasses[] = $class;
            }
        }
        if ($badClasses) {
            $this->fail(
                "Files not found for following usages in $path:\n" . implode("\n", $badClasses)
            );
        }
        if ($badUsages) {
            $this->fail("Bad usages of classes in following *.xml files $path: \n" . implode("\n", $badUsages));
        }
    }

    /**
     * Assert PHP classes have valid formal namespaces according to file locations
     *
     *
     * @param array $file
     * @dataProvider phpClassDataProvider
     */
    public function testClassNamespace($file)
    {
        $contents = file_get_contents($file);
        $relativePath = str_replace(Magento_TestFramework_Utility_Files::init()->getPathToSource(), "", $file);

        $classPattern = '/^class\s[A-Z][^\s\/]+/m';

        $classNameMatch = array();
        $className = null;

        // exceptions made for the files from the blacklist
        $blacklist = require __DIR__ . '/Blacklist.php';
        if (in_array($relativePath, $blacklist)) {
            return;
        }

        // if no class declaration found for $file, then skip this file
        if (!preg_match($classPattern, $contents, $classNameMatch) != 0) {
            return;
        }

        $className = substr($classNameMatch[0], 6);
        $this->_assertClassNamespace($file, $relativePath, $contents, $className);
    }

    /**
     * Assert PHP classes have valid formal namespaces according to file locations
     *
     *
     * @param string $file
     * @param string $relativePath
     * @param string $contents
     * @param string $className
     */
    protected function _assertClassNamespace($file, $relativePath, $contents, $className)
    {
        $namespacePattern = '/(Maged|Magento|Zend)\/[a-zA-Z]+[^\.]+/';
        $formalPattern = '/^namespace\s[a-zA-Z]+(\\\\[a-zA-Z]+)*/m';

        $namespaceMatch = array();
        $formalNamespaceArray = array();
        $namespace = null;
        $namespaceFolders = null;

        if (preg_match($namespacePattern, $relativePath, $namespaceMatch) != 0) {
            $namespaceFolders = $namespaceMatch[0];
        }

        if (preg_match($formalPattern, $contents, $formalNamespaceArray) != 0) {
            $formalNamespace = substr($formalNamespaceArray[0], 10);
            $formalNamespace = str_replace('\\', '/', $formalNamespace);
            $formalNamespace .= '/'. $className;
            if ($namespaceFolders != null && $formalNamespace != null) {
                $this->assertEquals($formalNamespace, $namespaceFolders,
                    "Location of $file does not match formal namespace: $formalNamespace\n");
            }
        } else {
            $this->fail("Missing expected namespace \"$namespaceFolders\" for file: $file");
        }
    }

    /**
     * @return array
     */
    public function phpClassDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getClassFiles();
    }
}
