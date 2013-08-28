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
class Legacy_ClassesEnterpriseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider phpCodeDataProvider
     */
    public function testPhpCode($file)
    {
        $classes = self::collectPhpCodeClasses(file_get_contents($file));
        $this->_assertDeprecatedEnterprise($classes);
    }

    /**
     * @return array
     */
    public function phpCodeDataProvider()
    {
        return Utility_Files::init()->getPhpFiles();
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
        Utility_Classes::getAllMatches($contents, '/
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
        Utility_Classes::getAllMatches($contents, '/
            (?:parent\:\: | \->)_init\(\s*[\'"]([^\'"]+)[\'"]\s*\)
            | (?:parent\:\: | \->)_init\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]((?!(' . $skipForInit . '))[^\'"]+)[\'"]\s*\)
            /Uix',
            $classes
        );
        return $classes;
    }

    /**
     * Check if the class contains the string 'Enterprise_'.
     * 'Enterprise_' has been refactored to the the Magento Namespace
     *
     * @param array $names
     */
    protected function _assertDeprecatedEnterprise($names)
    {
        if (!$names) {
            return;
        }
        $obsoleteClasses = array();
        $exceptions = array('Enterprise_Tag', 'Magento_Enterprise');
        foreach ($names as $name) {
            $excludeItem = false;
            foreach ($exceptions as $exception) {
                $result = strpos($name, $exception);
                if ($result !== false) {
                    $excludeItem = true;
                    break;
                }
            }
            if (!$excludeItem) {
                try {
                    $this->assertStringStartsNotWith('Enterprise',$name);
                }
                catch (PHPUnit_Framework_AssertionFailedError $e) {
                    $obsoleteClasses[] = $name;
                }
            }
        }
        if ($obsoleteClasses) {
            $this->fail('Obsolete Class name(s) detected:' . "\n" . implode("\n", $obsoleteClasses));
        }
    }

}
