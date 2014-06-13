<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Test\Integrity;

use Magento\TestFramework\Utility\Files;
use Magento\Framework\Shell;
use Magento\Framework\Exception;

/**
 * A test that enforces validity of composer.json files and any other conventions in Magento components
 */
class ComposerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Shell
     */
    private static $shell;

    /**
     * @var bool
     */
    private static $isComposerAvailable;

    /**
     * @var string
     */
    private static $root;

    public static function setUpBeforeClass()
    {
        self::$shell = self::createShell();
        self::$isComposerAvailable = self::isComposerAvailable();
        self::$root = Files::init()->getPathToSource();
    }

    /**
     * @param string $dir
     * @param string $packageType
     * @dataProvider validateComposerJsonDataProvider
     */
    public function testValidComposerJson($dir, $packageType)
    {
        $this->assertComposerAvailable();
        $file = $dir . '/composer.json';
        $this->assertFileExists($file);
        self::$shell->execute('composer validate --working-dir=%s', [$dir]);
        $json = json_decode(file_get_contents($file));
        $this->assertMagentoConventions($dir, $packageType, $json);
    }

    /**
     * @return array
     */
    public function validateComposerJsonDataProvider()
    {
        $root = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $result = [];
        foreach (glob("{$root}/app/code/Magento/*", GLOB_ONLYDIR) as $dir) {
            $result[] = [$dir, 'magento2-module'];
        }
        foreach (glob("{$root}/app/i18n/Magento/*", GLOB_ONLYDIR) as $dir) {
            $result[] = [$dir, 'magento2-language'];
        }
        foreach (glob("{$root}/app/design/adminhtml/Magento/*", GLOB_ONLYDIR) as $dir) {
            $result[] = [$dir, 'magento2-theme-adminhtml'];
        }
        foreach (glob("{$root}/app/design/frontend/Magento/*", GLOB_ONLYDIR) as $dir) {
            $result[] = [$dir, 'magento2-theme-frontend'];
        }
        foreach (glob("{$root}/lib/internal/Magento/*", GLOB_ONLYDIR) as $dir) {
            $result[] = [$dir, 'magento2-framework'];
        }
        return $result;
    }

    /**
     * Enforce Magento-specific conventions to a composer.json file
     *
     * @param string $dir
     * @param string $packageType
     * @param \StdClass $json
     * @throws \InvalidArgumentException
     */
    private function assertMagentoConventions($dir, $packageType, \StdClass $json)
    {
        $this->assertObjectHasAttribute('name', $json);
        $this->assertObjectHasAttribute('type', $json);
        $this->assertObjectHasAttribute('version', $json);
        $this->assertEquals($packageType, $json->type);
        switch ($packageType) {
            case 'magento2-module':
                $this->assertRegExp('/^magento\/module(\-[a-z][a-z\d]+)+$/', $json->name);
                $this->assertObjectHasAttribute('require', $json);
                $this->assertDependsOnFramework($json->require);
                $this->assertModuleDependenciesInSync($dir, $json->require);
                break;
            case 'magento2-language':
                $this->assertRegExp('/^magento\/language\-[a-z]{2}_[a-z]{2}$/', $json->name);
                $this->assertObjectHasAttribute('require', $json);
                $this->assertDependsOnFramework($json->require);
                break;
            case 'magento2-theme-adminhtml':
                $this->assertRegExp('/^magento\/theme-adminhtml(\-[a-z0-9_]+)+$/', $json->name);
                $this->assertObjectHasAttribute('require', $json);
                $this->assertDependsOnFramework($json->require);
                $this->assertThemeVersionInSync($dir, $json->version);
                break;
            case 'magento2-theme-frontend':
                $this->assertRegExp('/^magento\/theme-frontend(\-[a-z0-9_]+)+$/', $json->name);
                $this->assertObjectHasAttribute('require', $json);
                $this->assertDependsOnFramework($json->require);
                break;
            case 'magento2-framework':
                $this->assertRegExp('/^magento\/framework$/', $json->name);
                break;
            default:
                throw new \InvalidArgumentException("Unknown package type {$packageType}");
        }
    }

    /**
     * Make sure a component depends on magento/framework component
     *
     * @param \StdClass $json
     */
    private function assertDependsOnFramework(\StdClass $json)
    {
        $this->assertObjectHasAttribute('magento/framework', $json);
    }

    /**
     * Assert that references to module dependencies in module.xml and composer.json are not out of sync
     *
     * @param string $dir
     * @param \StdClass $json
     */
    private function assertModuleDependenciesInSync($dir, \StdClass $json)
    {
        $xml = simplexml_load_file("$dir/etc/module.xml");
        $packages = [];
        /** @var \SimpleXMLElement $node */
        foreach ($xml->module->depends->children() as $node) {
            if ('module' === $node->getName()) {
                $packages[] = $this->convertModuleToPackageName((string)$node['name']);
            }
        }
        foreach ($packages as $package) {
            $this->assertObjectHasAttribute($package, $json);
        }
    }

    /**
     * Assert that references to theme version in theme.xml and composer.json are not out of sync
     *
     * @param string $dir
     * @param string $version
     */
    private function assertThemeVersionInSync($dir, $version)
    {
        $xml = simplexml_load_file("$dir/theme.xml");
        $this->assertEquals($xml->theme->version, $version);
    }

    /**
     * Convert a fully qualified module name to a composer package name according to conventions
     *
     * @param string $moduleName
     * @return string
     */
    private function convertModuleToPackageName($moduleName)
    {
        list($vendor, $name) = explode('_', $moduleName, 2);
        $package = 'module';
        foreach(preg_split('/([A-Z][a-z\d]+)/', $name, -1, PREG_SPLIT_DELIM_CAPTURE) as $chunk) {
            $package .= $chunk ? "-{$chunk}" : '';
        }
        return strtolower("{$vendor}/{$package}");
    }

    /**
     * Create shell wrapper
     *
     * @return \Magento\Framework\Shell
     */
    private static function createShell()
    {
        return new Shell(new Shell\CommandRenderer, null);
    }

    /**
     * Check if composer command is available in the environment
     *
     * @return bool
     */
    private static function isComposerAvailable()
    {
        try {
            self::$shell->execute('composer --version');
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Skip the test if composer is unavailable
     */
    private function assertComposerAvailable()
    {
        if (!self::$isComposerAvailable) {
            $this->markTestSkipped();
        }
    }
}
