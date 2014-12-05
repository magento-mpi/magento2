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

class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    const README_FILENAME = 'README.md';
    const BLACKLIST_FILES_PATTERN = '_files/readme/blacklist/*.txt';
    const PATTERNS_FILE = '_files/readme/patterns.txt';

    /** @var array Blacklisted files and directories */
    private $blacklist = [];

    /** @var array */
    private $patterns = [];

    /**
     * @var string Path to project root
     */
    private $root;

    protected function setUp()
    {
        $this->root = Files::init()->getPathToSource();
        $this->blacklist = $this->getBlacklist();
        $this->patterns = $this->getPatterns();
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
    private function getModuleDirectories()
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
    private function getBlacklist()
    {
        if (empty($this->blacklist)) {
            foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . self::BLACKLIST_FILES_PATTERN) as $file) {
                $blacklist = file($file);
                foreach ($blacklist as $path) {
                    $this->blacklist[] = $this->root . trim(($path[0] === '/' ? $path : '/' . $path));
                }
            }
        }
        return $this->blacklist;
    }

    /**
     * @return array
     */
    private function getPatterns()
    {
        if (empty($this->patterns)) {
            $filename = __DIR__ . DIRECTORY_SEPARATOR . self::PATTERNS_FILE;
            $patterns = file($filename);
            foreach ($patterns as $pattern) {
                $this->blacklist[] = trim($pattern);
            }
        }
        return $this->blacklist;
    }
}
