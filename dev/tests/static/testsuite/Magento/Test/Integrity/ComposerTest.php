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
use Magento\Tools\Composer\Helper\ReplaceFilter;

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

    /**
     * @var \stdClass
     */
    private static $rootJson;

    /**
     * @var array
     */
    private static $dependencies;

    /**
     * @var string
     */
    private static $composerPath = 'composer';

    public static function setUpBeforeClass()
    {
        if (defined('TESTS_COMPOSER_PATH')) {
            self::$composerPath = TESTS_COMPOSER_PATH;
        }
        self::$shell = self::createShell();
        self::$isComposerAvailable = self::isComposerAvailable();
        self::$root = Files::init()->getPathToSource();
        self::$rootJson = json_decode(file_get_contents(self::$root . '/composer.json'));
        self::$dependencies = [];
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
        self::$shell->execute(self::$composerPath . ' validate --working-dir=%s', [$dir]);
        $contents = file_get_contents($file);
        $json = json_decode($contents);
        $this->assertCodingStyle($contents);
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
        foreach (glob("{$root}/app/i18n/magento/*", GLOB_ONLYDIR) as $dir) {
            $result[] = [$dir, 'magento2-language'];
        }
        foreach (glob("{$root}/app/design/adminhtml/Magento/*", GLOB_ONLYDIR) as $dir) {
            $result[] = [$dir, 'magento2-theme'];
        }
        foreach (glob("{$root}/app/design/frontend/Magento/*", GLOB_ONLYDIR) as $dir) {
            $result[] = [$dir, 'magento2-theme'];
        }
        foreach (glob("{$root}/lib/internal/Magento/*", GLOB_ONLYDIR) as $dir) {
            $result[] = [$dir, 'magento2-library'];
        }
        $result[] = [$root, 'project'];

        return $result;
    }

    /**
     * Some of coding style conventions
     *
     * @param string $contents
     */
    private function assertCodingStyle($contents)
    {
        $this->assertNotRegExp('/" :\s*["{]/', $contents, 'Coding style: no space before colon.');
        $this->assertNotRegExp('/":["{]/', $contents, 'Coding style: a space is necessary after colon.');
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
        $this->assertVersionInSync($json->name, $json->version);
        $this->assertObjectHasAttribute('require', $json);
        $this->assertEquals($packageType, $json->type);
        if ($packageType !== 'project') {
            self::$dependencies[] = $json->name;
            $this->assertHasMap($json);
            $this->assertMapConsistent($dir, $json);
        }
        switch ($packageType) {
            case 'magento2-module':
                $xml = simplexml_load_file("$dir/etc/module.xml");
                $this->assertConsistentModuleName($xml, $json->name);
                $this->assertDependsOnPhp($json->require);
                $this->assertDependsOnFramework($json->require);
                $this->assertModuleDependenciesInSync($xml, $json->require);
                break;
            case 'magento2-language':
                $this->assertRegExp('/^magento\/language\-[a-z]{2}_[a-z]{2}$/', $json->name);
                $this->assertDependsOnFramework($json->require);
                break;
            case 'magento2-theme':
                $this->assertRegExp('/^magento\/theme-(?:adminhtml|frontend)(\-[a-z0-9_]+)+$/', $json->name);
                $this->assertDependsOnPhp($json->require);
                $this->assertDependsOnFramework($json->require);
                $this->assertThemeVersionInSync($dir, $json->version);
                break;
            case 'magento2-library':
                $this->assertDependsOnPhp($json->require);
                $this->assertRegExp('/^magento\/framework$/', $json->name);
                break;
            case 'project':
                sort(self::$dependencies);
                $dependenciesListed = [];
                foreach (array_keys((array)self::$rootJson->replace) as $key) {
                    if (ReplaceFilter::isMagentoComponent($key)) {
                        $dependenciesListed[] = $key;
                    }
                }
                sort($dependenciesListed);
                $this->assertEquals(
                    self::$dependencies,
                    $dependenciesListed,
                    'The root composer.json does not match with currently available components.'
                );
                break;
            default:
                throw new \InvalidArgumentException("Unknown package type {$packageType}");
        }
    }

    /**
     * Assert that there is map in specified composer json
     *
     * @param \StdClass $json
     */
    private function assertHasMap(\StdClass $json)
    {
        $error = 'There must be an "extra->map" node in composer.json of each Magento component.';
        $this->assertObjectHasAttribute('extra', $json, $error);
        $this->assertObjectHasAttribute('map', $json->extra, $error);
        $this->assertInternalType('array', $json->extra->map, $error);
    }

    /**
     * Assert that component directory name and mapping information are consistent
     *
     * @param string $dir
     * @param \StdClass $json
     */
    private function assertMapConsistent($dir, $json)
    {
        preg_match('/^.+\/(.+)\/(.+)$/', $dir, $matches);
        list(, $vendor, $name) = $matches;
        $map = $json->extra->map;
        $this->assertArrayHasKey(0, $map);
        $this->assertArrayHasKey(1, $map[0]);
        $this->assertRegExp(
            "/{$vendor}\\/{$name}$/",
            $map[0][1],
            'Mapping info is inconsistent with the directory structure'
        );
    }

    /**
     * Enforce package naming conventions for modules
     *
     * @param \SimpleXMLElement $xml
     * @param string $packageName
     */
    private function assertConsistentModuleName(\SimpleXMLElement $xml, $packageName)
    {
        $moduleName = (string)$xml->module->attributes()->name;
        $this->assertEquals(
            $packageName,
            $this->convertModuleToPackageName($moduleName),
            "For the module '{$moduleName}', the expected package name is '{$packageName}'"
        );
    }

    /**
     * Make sure a component depends on php version
     *
     * @param \StdClass $json
     */
    private function assertDependsOnPhp(\StdClass $json)
    {
        $this->assertObjectHasAttribute('php', $json, 'This component is expected to depend on certain PHP version(s)');
    }

    /**
     * Make sure a component depends on magento/framework component
     *
     * @param \StdClass $json
     */
    private function assertDependsOnFramework(\StdClass $json)
    {
        $this->assertObjectHasAttribute(
            'magento/framework',
            $json,
            'This component is expected to depend on magento/framework'
        );
    }

    /**
     * Assert that references to module dependencies in module.xml and composer.json are not out of sync
     *
     * @param \SimpleXMLElement $xml
     * @param \StdClass $json
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function assertModuleDependenciesInSync(\SimpleXMLElement $xml, \StdClass $json)
    {
        $packages = [];
        /** @var \SimpleXMLElement $node */
        foreach ($xml->module->depends->children() as $node) {
            if ('module' === $node->getName()) {
                $moduleName = (string)$node['name'];
                $packages[$moduleName] = $this->convertModuleToPackageName($moduleName);
            }
        }
        foreach ($packages as $package) {
            $this->assertObjectHasAttribute(
                $package,
                $json,
                "Dependency on the component {$package} is found at the etc/module.xml, but missing in composer.json"
            );
        }
        foreach (array_keys((array) $json) as $key) {
            if (0 === strpos($key, 'magento/module-', 0)) {
                $this->assertContains(
                    $key,
                    $packages,
                    "Dependency on the component {$key} is found at the composer.json, but missing in etc/module.xml"
                );
            }
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
        $this->assertEquals($xml->version, $version);
    }

    /**
     * Assert that versions in root composer.json and Magento component's composer.json are not out of sync
     *
     * @param string $name
     * @param string $version
     */
    private function assertVersionInSync($name, $version)
    {
        $this->assertEquals(
            self::$rootJson->version,
            $version,
            "Version {$version} in component {$name} is inconsistent with version "
            . self::$rootJson->version . ' in root composer.json'
        );
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
        foreach (preg_split('/([A-Z][a-z\d]+)/', $name, -1, PREG_SPLIT_DELIM_CAPTURE) as $chunk) {
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
            self::$shell->execute(self::$composerPath . ' --version');
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

    public function testComponentPathsInRoot()
    {
        if (!isset(self::$rootJson->extra) || !isset(self::$rootJson->extra->component_paths)) {
            $this->markTestSkipped("The root composer.json file doesn't mention any extra component paths information");
        }
        $this->assertObjectHasAttribute(
            'replace',
            self::$rootJson,
            "If there are any component paths specified, then they must be reflected in 'replace' section"
        );
        $flat = [];
        foreach (self::$rootJson->extra->component_paths as $key => $element) {
            if (is_string($element)) {
                $flat[] = [$key, $element];
            } elseif (is_array($element)) {
                foreach ($element as $path) {
                    $flat[] = [$key, $path];
                }
            } else {
                throw new \Exception("Unexpected element 'in extra->component_paths' section");
            }
        }
        while (list(, list($component, $path)) = each($flat)) {
            $this->assertFileExists(
                self::$root . '/' . $path,
                "Missing or invalid component path: {$component} -> {$path}"
            );
            $this->assertObjectHasAttribute(
                $component,
                self::$rootJson->replace,
                "The {$component} is specified in 'extra->component_paths', but missing in 'replace' section"
            );
        }
    }
}
