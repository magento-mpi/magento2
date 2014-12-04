<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests to ensure that all license blocks are represented by placeholders
 */
namespace Magento\Test\Legacy;

use Magento\Framework\Test\Utility\Files;

class ModuleDescriptionTest extends \PHPUnit_Framework_TestCase
{
    const README_FILENAME = 'README.md';
    const BLACKLIST_PATTERN = '_files/blacklist/module_description/*.txt';
    /** @var array Blacklisted files and directories */
    private $blacklist = [];

    /** @var array */
    private $patterns = [
        'app/code/Magento/*',
        'lib/internal/Magento/*/*',
        'lib/internal/Magento/*',
    ];

    /**
     * @var string Path to project root
     */
    private $root;

    protected function setUp()
    {
        $this->root = Files::init()->getPathToSource();
        $this->blacklist = $this->getBlacklist();
    }

    public function testModuleDescriptionFiles()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
        /**
         * @param string $dir
         * @param string $packageType
         */
            function ($dir) {
                $file = $dir . DIRECTORY_SEPARATOR . self::README_FILENAME;
                $this->assertFileExists(
                    $file,
                    sprintf('File %s not found in %s', self::README_FILENAME, $dir)
                );
            },
            $this->getModuleDirectories()
        );
    }

    /**
     * @return array
     */
    public function getModuleDirectories()
    {
        $root = $this->root;
        $result = [];
        foreach ($this->patterns as $pattern) {
            foreach (glob("{$root}/{$pattern}", GLOB_ONLYDIR) as $dir) {
                $isBlacklisted = false;
                foreach ($this->blacklist as $blacklistedItem) {
                    if ($blacklistedItem === $dir) {
                        $isBlacklisted = true;
                        break;
                    }
                }
                if (!$isBlacklisted) {
                    $result[][$dir] = $dir;
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getBlacklist()
    {
        if (empty($this->blacklist)) {
            foreach (glob(__DIR__ . DIRECTORY_SEPARATOR. self::BLACKLIST_PATTERN) as $file) {
                $blacklist = file($file);
                foreach ($blacklist as $path) {
                    $this->blacklist[] = $this->root . trim(($path[0] === '/' ? $path : '/' . $path));
                }
            }
        }
        return $this->blacklist;
    }
}
