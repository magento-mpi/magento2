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
namespace Magento\Test\Integrity;

class ClassesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * List of already found classes to avoid checking them over and over again
     *
     * @var array
     */
    protected static $_existingClasses = array();

    protected static $_keywordsBlacklist = array("String", "Array", "Boolean", "Element");

    /**
     * @param string $file
     * @dataProvider phpFileDataProvider
     */
    public function testPhpFile($file)
    {
        $contents = file_get_contents($file);
        $classes = \Magento\TestFramework\Utility\Classes::getAllMatches($contents, '/
        # ::getResourceModel ::getBlockSingleton ::getModel ::getSingleton
        \:\:get(?:ResourceModel | BlockSingleton | Model | Singleton)?\(\s*[\'"]([a-z\d\\\\]+)[\'"]\s*[\),]

        # various methods, first argument
        | \->(?:initReport | addBlock | createBlock | setDataHelperName | _?initLayoutMessages
            | setAttributeModel | setBackendModel | setFrontendModel | setSourceModel | setModel
        )\(\s*\'([a-z\d\\\\]+)\'\s*[\),]

        # various methods, second argument
        | \->add(?:ProductConfigurationHelper | OptionsRenderCfg)\(.+?,\s*\'([a-z\d\\\\]+)\'\s*[\),]

        # \Mage::helper ->helper
        | (?:Mage\:\:|\->)helper\(\s*\'([a-z\d\\\\]+)\'\s*\)

        # misc
        | function\s_getCollectionClass\(\)\s+{\s+return\s+[\'"]([a-z\d\\\\]+)[\'"]
        | \'resource_model\'\s*=>\s*[\'"]([a-z\d\\\\]+)[\'"]
        | (?:_parentResourceModelName | _checkoutType | _apiType)\s*=\s*\'([a-z\d\\\\]+)\'
        | \'renderer\'\s*=>\s*\'([a-z\d\\\\]+)\'
        /ix'
        );

        // without modifier "i". Starting from capital letter is a significant characteristic of a class name
        \Magento\TestFramework\Utility\Classes::getAllMatches($contents, '/(?:\-> | parent\:\:)(?:_init | setType)\(\s*
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
        return \Magento\TestFramework\Utility\Files::init()->getPhpFiles();
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
        $matches = \Magento\TestFramework\Utility\Classes::getAllMatches($contents, $regex);
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
        $classes = \Magento\TestFramework\Utility\Classes::collectClassesInConfig(simplexml_load_file($path));
        $this->_assertClassesExist($classes, $path);
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getMainConfigFiles();
    }

    /**
     * @param string $path
     * @dataProvider layoutFileDataProvider
     */
    public function testLayoutFile($path)
    {
        $xml = simplexml_load_file($path);

        $classes = \Magento\TestFramework\Utility\Classes::getXmlNodeValues($xml,
            '/layout//*[contains(text(), "\\\\Block\\\\") or contains(text(),
                "\\\\Model\\\\") or contains(text(), "\\\\Helper\\\\")]'
        );
        foreach (\Magento\TestFramework\Utility\Classes::getXmlAttributeValues($xml,
            '/layout//@helper', 'helper') as $class) {
            $classes[] = \Magento\TestFramework\Utility\Classes::getCallbackClass($class);
        }
        foreach (\Magento\TestFramework\Utility\Classes::getXmlAttributeValues($xml,
            '/layout//@module', 'module') as $module) {
            $classes[] = str_replace('_', '\\', "{$module}_Helper_Data");
        }
        $classes = array_merge($classes, \Magento\TestFramework\Utility\Classes::collectLayoutClasses($xml));

        $this->_assertClassesExist(array_unique($classes), $path);
    }

    /**
     * @return array
     */
    public function layoutFileDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getLayoutFiles();
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
    protected function _assertClassesExist($classes, $path)
    {
        if (!$classes) {
            return;
        }
        $badClasses = array();
        $badUsages = array();
        foreach ($classes as $class) {
            try {
                if (strrchr($class, '\\') == false) {
                    $badUsages[] = $class;
                    continue;
                } else {
                    $this->assertTrue(isset(self::$_existingClasses[$class])
                        || \Magento\TestFramework\Utility\Files::init()->classFileExists($class)
                        || \Magento\TestFramework\Utility\Classes::isVirtual($class)
                        || \Magento\TestFramework\Utility\Classes::isAutogenerated($class)
                    );
                }
                self::$_existingClasses[$class] = 1;
            } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                $badClasses[] = $class;
            }
        }
        if ($badClasses) {
            $this->fail(
                "Files not found for following usages in $path:\n" . implode("\n", $badClasses)
            );
        }
        if ($badUsages) {
            $this->fail("Bad usages of classes in $path: \n" . implode("\n", $badUsages));
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
        $relativePath = str_replace(\Magento\TestFramework\Utility\Files::init()->getPathToSource(), "", $file);

        $classPattern = '/^(abstract\s)?class\s[A-Z][^\s\/]+/m';

        $classNameMatch = array();
        $className = null;

        // exceptions made for the files from the blacklist
        $blacklist = require __DIR__ . '/Blacklist.php';
        if (in_array($relativePath, $blacklist)) {
            return;
        }

        // if no class declaration found for $file, then skip this file
        if (preg_match($classPattern, $contents, $classNameMatch) == 0) {
            return;
        }

        $classParts = explode(' ', $classNameMatch[0]);
        $className = array_pop($classParts);
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
        $namespacePattern = '/(Magento|Zend)\/[a-zA-Z]+[^\.]+/';
        $formalPattern = '/^namespace\s[a-zA-Z]+(\\\\[a-zA-Z0-9]+)*/m';

        $namespaceMatch = array();
        $formalNamespaceArray = array();
        $namespaceFolders = null;

        // if no namespace pattern found according to the path of the file, skip the file
        if (preg_match($namespacePattern, $relativePath, $namespaceMatch) == 0) {
            return;
        }

        $namespaceFolders = $namespaceMatch[0];
        $classParts = explode('/', $namespaceFolders);
        array_pop($classParts);
        $expectedNamespace = implode('\\', $classParts);

        if (preg_match($formalPattern, $contents, $formalNamespaceArray) != 0) {
            $foundNamespace = substr($formalNamespaceArray[0], 10);
            $foundNamespace = str_replace('\\', '/', $foundNamespace);
            $foundNamespace .= '/'. $className;
            if ($namespaceFolders != null && $foundNamespace != null) {
                $this->assertEquals($namespaceFolders, $foundNamespace,
                    "Location of $file does not match formal namespace: $expectedNamespace\n");
            }
        } else {
            $this->fail("Missing expected namespace \"$expectedNamespace\" for file: $file");
        }
    }

    /**
     * @return array
     */
    public function phpClassDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getClassFiles();
    }

    /**
     *
     * @param array $file
     * @dataProvider classReferencesDataProvider
     */
    public function testClassReferences($file)
    {
        //$file = "/Users/yuxzheng/env/polarseals-develop/dev/tools/Magento/Tools/I18n/Code/Parser/Adapter/Php/Tokenizer.php";
        //$file = "/Users/yuxzheng/env/polarseals-develop/dev/tools/Magento/Tools/Di/Definition/Compressor.php";
        $contents = file_get_contents($file);
        $formalPattern = '/^namespace\s[a-zA-Z]+(\\\\[a-zA-Z0-9]+)*/m';

        // Skip the file if the class is not defined using formal namespace
        if (preg_match($formalPattern, $contents) == 0) {
            return;
        }
        // Instantiation of new object, for example: "return new Foo();"
        $newObjectPattern = '/^'
            . '.*new\s(?<venderClass>\\\\Magento(?:\\\\[a-zA-Z0-9_]+)+)\(.*\)'
            . '|.*new\s(?<badClass>[A-Z][a-zA-Z0-9]+[a-zA-Z0-9_\\\\]*)\(.*\)\;'
            . '/m';
        $result1 = array();
        preg_match_all($newObjectPattern, $contents, $result1);

        // Static function/variable, for example: "Foo::someStaticFunction();"
        $staticCallPattern = '/^'
            . '.*(?<venderClass>\\\\Magento(?:\\\\[a-zA-Z0-9_]+)+)\:\:.*'
            . '|[^\\\\^a-z^A-Z^0-9^_](?<badClass>[A-Z][a-zA-Z0-9_]+)\:\:.*\(.*\)'
            . '/m';
        $result2 = array();
        preg_match_all($staticCallPattern, $contents, $result2);

        // Annotation, for example: "*throws \Magento\Foo\Bar" or "* @throws Exception" or "* @return Foo"
        $annotationPattern = '/^'
            . '[\s]*\*\s\@return\s(?<venderClass>\\\\Magento(?:\\\\[a-zA-Z0-9_]+)+)'
            . '|[\s]*\*\s\@return\s(?<badClass>[A-Z][a-zA-Z0-9_]+)'
            . '|[\s]*\*\s\@throws\s(?<exception>Exception)'
            . '/m';
        $result3 = array();
        preg_match_all($annotationPattern, $contents, $result3);

        $vendorClasses = array_unique(
            array_merge_recursive($result1['venderClass'], $result2['venderClass'], $result3['venderClass'])
        );

        $badClasses = array_unique(
            array_merge_recursive($result1['badClass'], $result2['badClass'], $result3['badClass'])
        );

        $vendorClasses = array_filter($vendorClasses, 'strlen');
        if (!empty($vendorClasses)) {
            $this->_assertClassesExist($vendorClasses, $file);
        }

        if (!empty($result3['exception']) && $result3['exception'][0] != "") {
            array_push($badClasses, $result3['exception'][0]);
        }

        $badClasses = array_filter($badClasses, 'strlen');
        if (!empty($badClasses)) {
            $badClasses = $this->removeSpecialCases($badClasses, $file, $contents);
            $this->_assertClassReferences($badClasses, $file);
        }
    }

    /**
     * This function is to remove special cases (if any) from the list of found bad classes
     * @param array $badClasses
     * @param string $file
     * @param string $contents
     * @returns array $badClasses
     */
    protected function removeSpecialCases($badClasses, $file, $contents)
    {
        foreach ($badClasses as $badClass) {
            // Remove valid usages of Magento modules from the list
            // for example: 'Magento_Sales::actions_edit'
            if (preg_match('/Magento_[A-Z0-9][a-z0-9]*/', $badClass)) {
                unset($badClasses[array_search($badClass, $badClasses)]);
            }

            // Remove usage of key words such as "Array", "String", and "Boolean"
            if (in_array($badClass, self::$_keywordsBlacklist)) {
                unset($badClasses[array_search($badClass, $badClasses)]);
            }

            $classParts = explode('/', $file);
            $className = array_pop($classParts);
            // Remove usage of the class itself from the list
            if ($badClass . '.php' == $className) {
                unset($badClasses[array_search($badClass, $badClasses)]);
            }

            // Remove usage of classes that do NOT using fully-qualified class names (possibly under same namespace)
            $referenceFile = implode('/', $classParts) . '/' . str_replace('\\', '/', $badClass) . '.php';
            if (file_exists($referenceFile)) {
                unset($badClasses[array_search($badClass, $badClasses)]);
            }

            // Remove usage of classes that have been declared as "use" or "include"
            if (preg_match('/use\s.*' . str_replace('\\', '\\\\', $badClass) . '/', $contents)) {
                unset($badClasses[array_search($badClass, $badClasses)]);
            }
        }
        return $badClasses;
    }

    /**
     * Assert any found class name resolves into a file name and corresponds to an existing file
     *
     * @param array $badClasses
     * @param string $file
     */
    protected function _assertClassReferences($badClasses, $file)
    {
        if (empty($badClasses)) {
            return;
        }
        $this->fail(
            "Incorrect namespace usage(s) found in file $file:\n" . implode("\n", $badClasses)
        );
    }

    /**
     * Returns all php classes from lib/Magento
     * @return array
     */
    public function classReferencesDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()
            ->getClassFiles();
    }
}
