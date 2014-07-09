<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Composer\Package;

/**
 * A reader of composer.json files
 */
class Reader
{
    /**
     * Constructor
     *
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * Read composer.json files by specified glob patterns
     *
     * @param string $pattern
     * @return \StdClass[]
     */
    public function readPattern($pattern)
    {
        $result = [];
        foreach (glob("{$this->rootDir}/$pattern/composer.json", GLOB_BRACE) as $file) {
            $json = json_decode(file_get_contents($file));
            $result[$file] = $json;
        }
        return $result;
    }

    /**
     * Read one composer.json file by specified subdirectory
     *
     * Returns array of 2 elements: file name and result of reading the composer.json file
     *
     * @param string $subDir
     * @return array
     */
    public function readOne($subDir)
    {
        $file = $this->rootDir . ($subDir ? '/' . $subDir : '') . '/composer.json';
        if (is_file($file)) {
            $result = json_decode(file_get_contents($file));
        } else {
            $result = false;
        }
        return [$file, $result];
    }
}
