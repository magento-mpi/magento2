<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Composer\Package;

use \Magento\Tools\Composer\Helper\ExcludeFilter;

/**
 * A reader of composer.json files
 */
class Reader
{
    /**
     * Root directory
     *
     * @var string
     */
    private $rootDir;

    /**
     * List of patterns
     *
     * @var string[]
     */
    private $patterns = [];

    /**
     * List of patterns
     *
     * @var string[]
     */
    private $customizablePaths = [];

    /**
     * Constructor
     *
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
        $this->patterns = file(
            __DIR__ . '/../etc/magento_components_list.txt',
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );
        $this->customizablePaths = file(
            __DIR__ . '/../etc/magento_customizable_paths.txt',
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );
    }

    /**
     * Read all Magento-specific components and create package objects for them
     *
     * @return Package[]
     * @throws \LogicException
     */
    public function readMagentoPackages()
    {
        $result = [];
        foreach ($this->patterns as $pattern) {
            foreach (glob("{$this->rootDir}/{$pattern}/*", GLOB_ONLYDIR) as $dir) {
                $package = $this->readFile($dir . '/composer.json');
                if (false === $package) {
                    throw new \LogicException("Missing composer.json file in the directory: {$dir}");
                }
                $result[] = $package;
            }
        }
        return $result;
    }

    /**
     * Attempt to read a composer.json file in the specified directory (relatively to the root)
     *
     * @param string $dir
     * @return bool|Package
     */
    public function readFromDir($dir)
    {
        $file = $this->rootDir . ($dir ? '/' . $dir : '') . '/composer.json';
        return $this->readFile($file);
    }

    /**
     * Read a composer.json file and create a Package object
     *
     * @param string $file
     * @return bool|Package
     */
    private function readFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        $json = json_decode(file_get_contents($file));
        return new Package($json, $file);
    }

    /**
     * Read the list of patterns
     *
     * @return string[]
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    /**
     * Gets mapping list for root composer.json file to be used by marshalling tool
     *
     * @return array
     */
    public function getRootMappingPatterns()
    {
        $mappingList = $this->getCompleteMappingInfo();

        $modifiedMappingList = $this->getConciseMappingInfo($mappingList);

        $mappings = array();
        foreach ($modifiedMappingList as $path) {
            $mappings[] = array($path, $path);
        }

        return $mappings;
    }

    /**
     * Gets complete mapping info
     *
     * @return array
     */
    private function getCompleteMappingInfo()
    {
        $result = [];
        $excludes = $this->patterns;
        $counter = count($excludes);
        for ($i = 0; $i < $counter; $i++) {
            $excludes[$i] = str_replace('\\', '/', $this->rootDir) . '/' . $excludes[$i];
        }
        $customizableLocationList = $this->customizablePaths;
        $counter = count($customizableLocationList);
        for ($i = 0; $i < $counter; $i++) {
            $customizableLocationList[$i] = str_replace('\\', '/',
                    $this->rootDir) . '/' . $customizableLocationList[$i];
        }
        $directory = new \RecursiveDirectoryIterator($this->rootDir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $directory = new ExcludeFilter($directory, $excludes);
        $files = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            $file = str_replace('\\', '/', realpath($file));
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                continue;
            }
            if (!$this->checkExistence($customizableLocationList, $excludes, $file)) {
                $result[] = str_replace(str_replace('\\', '/', $this->rootDir) . '/', '', $file);
            }
        }

        sort($result);
        return $result;
    }

    /**
     * Gets final mapping info
     *
     * @param array $mappingList
     * @return array
     */
    private function getConciseMappingInfo($mappingList)
    {
        $result = [];
        $index = 0;
        for ($i=0; $i < count($mappingList); $i++) {
            if ($i === 0) {
                $result[] = $mappingList[$i];
                continue;
            }
            if (strpos($mappingList[$i], $mappingList[$index]) !== false) {
                if (mb_substr_count($mappingList[$i], '/') === mb_substr_count($mappingList[$index], '/')) {
                    $result[] = $mappingList[$i];
                    $index = $i;
                }
            } else {
                $result[] = $mappingList[$i];
                $index = $i;
            }
        }

        return $result;
    }

    /**
     * Check existence of a path in exempt list
     *
     * @param array $customizableLocationList
     * @param array $excludes
     * @param string $file
     * @return boolean
     */
    private function checkExistence($customizableLocationList, $excludes, $file)
    {
        foreach ($customizableLocationList as $path) {
            if ((strpos($path, $file) !== false)
                || ((strpos(str_replace('*', '', $path), $file)) !== false)) {
                return true;
            }
        }

        foreach ($excludes as $path) {
            if (strpos($path, $file) !== false) {
                return true;
            }
        }

        return false;
    }
}
