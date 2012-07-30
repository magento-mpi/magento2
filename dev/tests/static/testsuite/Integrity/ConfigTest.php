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
     * @var array
     */
    private static $_brokenModules = array('Social_Facebook');

    /**
     * @return array
     */
    public function testDeclaredLocales()
    {
        $configFiles = Utility_Files::init()->getConfigFiles('config.xml', array(), false);
        $verifiedFiles = array();
        foreach ($configFiles as $configFile) {
            preg_match('/\/([^\/]+?\/[^\/]+?)\/etc\/config\.xml$/', $configFile, $moduleName);
            $moduleName = str_replace('/', '_', $moduleName[1]);
            if (in_array($moduleName, self::$_brokenModules)) {
                continue;
            }
            $config = simplexml_load_file($configFile);
            $nodes = $config->xpath("/config/*/translate/modules/{$moduleName}/files/*") ?: array();
            foreach ($nodes as $node) {
                $localeFile = dirname($configFile) . '/../locale/en_US/' . (string)$node;
                $this->assertFileExists($localeFile);
                $verifiedFiles[] = realpath($localeFile);
            }
        }
        return array_unique($verifiedFiles);
    }

    /**
     * @depends testDeclaredLocales
     */
    public function testExistingFilesDeclared($verifiedFiles)
    {
        $root = Utility_Files::init()->getPathToSource();
        foreach (glob($root . "/app/code/*/*/*", GLOB_ONLYDIR) as $modulePath) {
            $localeFiles = glob($modulePath . "/locale/en_US/*.csv");
            foreach ($localeFiles as $file) {
                $file = realpath($file);
                $key = array_search($file, $verifiedFiles);
                if (false !== $key) {
                    unset($verifiedFiles[$key]);
                }
            }
        }
        $this->assertEmpty($verifiedFiles,
            'Translation files exist, but not declared in configuration:' . "\n" . var_export($verifiedFiles, 1)
        );
    }
}
