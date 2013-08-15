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

class Integrity_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function testDeclaredLocales()
    {
        $verifiedFiles = array();
        foreach ($this->_getConfigFilesPerModule() as $configFile => $moduleName) {
            $config = simplexml_load_file($configFile);
            $nodes = $config->xpath("/config/*/translate/modules/{$moduleName}/files/*") ?: array();
            foreach ($nodes as $node) {
                $localeFile = dirname($configFile) . '/../locale/en_US/' . (string)$node;
                $this->assertFileExists($localeFile);
                $verifiedFiles[realpath($localeFile)] = $moduleName;
            }
        }
        return $verifiedFiles;
    }

    /**
     * @depends testDeclaredLocales
     */
    public function testExistingFilesDeclared($verifiedFiles)
    {
        $root = Utility_Files::init()->getPathToSource();
        $failures = array();
        foreach (glob("{$root}/app/code/*/*", GLOB_ONLYDIR) as $modulePath) {
            $localeFiles = glob("{$modulePath}/locale/*/*.csv");
            foreach ($localeFiles as $file) {
                $file = realpath($file);
                $assertFile = dirname(dirname($file)) . DIRECTORY_SEPARATOR . 'en_US' . DIRECTORY_SEPARATOR
                    . basename($file);
                if (!isset($verifiedFiles[$assertFile])) {
                    $failures[] = $file;
                }
            }
        }
        $this->assertEmpty($failures,
            'Translation files exist, but not declared in configuration:' . "\n" . var_export($failures, 1)
        );
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
        foreach ($nodes as $node) {
            $this->assertStringStartsWith($moduleName . '_Model_', (string)$node,
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
        $configFiles = Utility_Files::init()->getConfigFiles('config.xml', array(), false);
        $data = array();
        foreach ($configFiles as $configFile) {
            preg_match('#/([^/]+?/[^/]+?)/etc/config\.xml$#', $configFile, $moduleName);
            $moduleName = str_replace('/', '_', $moduleName[1]);
            $data[$configFile] = $moduleName;
        }
        return $data;
    }
}
