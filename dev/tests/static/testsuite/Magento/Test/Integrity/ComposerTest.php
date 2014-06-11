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
        $this->assertMagentoConventions($json, $packageType);
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
        return $result;
    }

    /**
     * Enforce Magento-specific conventions to a composer.json file
     *
     * @param \StdClass $json
     * @param string $packageType
     * @throws \InvalidArgumentException
     */
    private function assertMagentoConventions(\StdClass $json, $packageType)
    {
        $this->assertObjectHasAttribute('name', $json);
        $this->assertObjectHasAttribute('type', $json);
        $this->assertObjectHasAttribute('version', $json);
        $this->assertObjectHasAttribute('require', $json);
        $this->assertEquals($packageType, $json->type);
        switch ($packageType) {
            case 'magento2-module':
                $this->assertRegExp('/^magento\/module(\-[a-z][a-z\d]+)+$/', $json->name);
                $this->assertDependsOnFramework($json->require);
                break;
            case 'magento2-language':
                $this->assertRegExp('/^magento\/language\-[a-z]{2}_[a-z]{2}$/', $json->name);
                $this->assertDependsOnFramework($json->require);
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
