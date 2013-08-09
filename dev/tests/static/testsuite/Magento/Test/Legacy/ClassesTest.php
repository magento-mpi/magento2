<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scans source code for references to classes and see if they indeed exist
 */
class Magento_Test_Legacy_ClassesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider phpCodeDataProvider
     */
    public function testPhpCode($file)
    {
        $classes = self::collectPhpCodeClasses(file_get_contents($file));
        $this->_assertNonFactoryName($classes);
    }

    /**
     * @return array
     */
    public function phpCodeDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getPhpFiles();
    }

    /**
     * Scan contents as PHP-code and find class name occurrences
     *
     * @param string $contents
     * @param array &$classes
     * @return array
     */
    public static function collectPhpCodeClasses($contents, &$classes = array())
    {
        Magento_TestFramework_Utility_Classes::getAllMatches($contents, '/
            # ::getModel ::getSingleton ::getResourceModel ::getResourceSingleton
            \:\:get(?:Resource)?(?:Model | Singleton)\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # addBlock createBlock getBlockSingleton
            | (?:addBlock | createBlock | getBlockSingleton)\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # Mage::helper ->helper
            | (?:Mage\:\:|\->)helper\(\s*[\'"]([^\'"]+)[\'"]\s*\)

            # various methods, first argument
            | \->(?:initReport | setDataHelperName | setEntityModelClass | _?initLayoutMessages
                | setAttributeModel | setBackendModel | setFrontendModel | setSourceModel | setModel
            )\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # various methods, second argument
            | \->add(?:ProductConfigurationHelper | OptionsRenderCfg)\(.+,\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # models in install or setup
            | [\'"](?:resource_model | attribute_model | entity_model | entity_attribute_collection
                | source | backend | frontend | input_renderer | frontend_input_renderer
            )[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]

            # misc
            | function\s_getCollectionClass\(\)\s+{\s+return\s+[\'"]([a-z\d_\/]+)[\'"]
            | (?:_parentResourceModelName | _checkoutType | _apiType)\s*=\s*\'([a-z\d_\/]+)\'
            | \'renderer\'\s*=>\s*\'([a-z\d_\/]+)\'
            | protected\s+\$_(?:form|info|backendForm|iframe)BlockType\s*=\s*[\'"]([^\'"]+)[\'"]

            /Uix',
            $classes
        );

        // check ->_init | parent::_init
        $skipForInit = implode('|',
            array(
                'id', '[\w\d_]+_id', 'pk', 'code', 'status', 'serial_number',
                'entity_pk_value', 'currency_code', 'unique_key',
            )
        );
        Magento_TestFramework_Utility_Classes::getAllMatches($contents, '/
            (?:parent\:\: | \->)_init\(\s*[\'"]([^\'"]+)[\'"]\s*\)
            | (?:parent\:\: | \->)_init\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]((?!(' . $skipForInit . '))[^\'"]+)[\'"]\s*\)
            /Uix',
            $classes
        );
        return $classes;
    }

    /**
     * @param string $path
     * @dataProvider configFileDataProvider
     */
    public function testConfiguration($path)
    {
        $xml = simplexml_load_file($path);

        $classes = Magento_TestFramework_Utility_Classes::collectClassesInConfig($xml);
        $this->_assertNonFactoryName($classes);

        $modules = Magento_TestFramework_Utility_Classes::getXmlAttributeValues($xml, '//@module', 'module');
        $this->_assertNonFactoryName(array_unique($modules));
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
    public function testLayouts($path)
    {
        $xml = simplexml_load_file($path);
        $classes = Magento_TestFramework_Utility_Classes::collectLayoutClasses($xml);
        foreach (Magento_TestFramework_Utility_Classes::getXmlAttributeValues($xml, '/layout//@helper', 'helper') as $class) {
            $classes[] = Magento_TestFramework_Utility_Classes::getCallbackClass($class);
        }
        $classes = array_merge($classes, Magento_TestFramework_Utility_Classes::getXmlAttributeValues($xml, '/layout//@module', 'module'));
        $this->_assertNonFactoryName(array_unique($classes));

        $tabs = Magento_TestFramework_Utility_Classes::getXmlNodeValues($xml, '/layout//action[@method="addTab"]/block');
        $this->_assertNonFactoryName(array_unique($tabs), true);
    }

    /**
     * @return array
     */
    public function layoutFileDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getLayoutFiles();
    }

    /**
     * Check whether specified classes or module names correspond to a file according PSR-0 standard
     *
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $names
     * @param bool $softComparison
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertNonFactoryName($names, $softComparison = false)
    {
        if (!$names) {
            return;
        }
        $factoryNames = array();
        foreach ($names as $name) {
            try {
                if ($softComparison) {
                    $this->assertNotRegExp('/\//', $name);
                } else {
                    $this->assertFalse(false === strpos($name, '_'));
                    $this->assertRegExp('/^([A-Z][A-Za-z\d_]+)+$/', $name);
                }
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $factoryNames[] = $name;
            }
        }
        if ($factoryNames) {
            $this->fail('Obsolete factory name(s) detected:' . "\n" . implode("\n", $factoryNames));
        }
    }
}
