<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $_possibleLocales = array('de_DE', 'en_AU', 'en_GB', 'en_US', 'es_ES', 'es_XC', 'fr_FR', 'fr_XC',
        'it_IT', 'ja_JP', 'nl_NL', 'pl_PL', 'zh_CN', 'zh_XC', 'pt_BR');

    public function testExistingFilesDeclared()
    {
        $root = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $failures = array();
        foreach (glob("{$root}/app/code/*/*", GLOB_ONLYDIR) as $modulePath) {
            $localeFiles = glob("{$modulePath}/i18n/*.csv");
            foreach ($localeFiles as $file) {
                $file = realpath($file);
                $assertLocale = str_replace('.csv', '', basename($file));
                if (!in_array($assertLocale, $this->_possibleLocales)) {
                    $failures[] = $file;
                }
            }
        }
        $this->assertEmpty($failures,
            'Translation files exist, but not declared in configuration:' . "\n" . var_export($failures, 1));
    }

    /**
     * Verify whether all payment methods are declared in appropriate modules
     *
     * @dataProvider paymentMethodsDataProvider
     */
    public function testPaymentMethods($configFile, $moduleName)
    {
        $config = simplexml_load_file($configFile);
        $nodes = $config->xpath('/config/default/payment/*/model') ?: array();
        $formalModuleName = str_replace('_', '\\', $moduleName);
        foreach ($nodes as $node) {
            $this->assertStringStartsWith($formalModuleName . '\Model\\', (string)$node,
                "'$node' payment method is declared in '$configFile' module, but doesn't belong to '$moduleName' module"
            );
        }
    }

    public function paymentMethodsDataProvider()
    {
        $data = array();
        foreach ($this->_getConfigFilesPerModule() as $configFile => $moduleName) {
            $data[] = array($configFile, $moduleName);
        }
        return $data;
    }

    /**
     * Get list of configuration files associated with modules
     *
     * @return array
     */
    protected function _getConfigFilesPerModule()
    {
        $configFiles = \Magento\TestFramework\Utility\Files::init()->getConfigFiles('config.xml', array(), false);
        $data = array();
        foreach ($configFiles as $configFile) {
            preg_match('#/([^/]+?/[^/]+?)/etc/config\.xml$#', $configFile, $moduleName);
            $moduleName = str_replace('/', '_', $moduleName[1]);
            $data[$configFile] = $moduleName;
        }
        return $data;
    }
}
