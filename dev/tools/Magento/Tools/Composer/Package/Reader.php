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
     * List of patterns by which Magento components reside
     */
    const MAGENTO_COMPONENT_PATTERNS = 'magento_components_list.txt';

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
    private $customizablePaths= [];

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
     * @param array string$workingDir
     * @return array
     */
    public function getMappingList($workingDir)
    {
        $mappingList = array();
        $excludes = array();
        $customizableLocationList = array();

        $this->getExemptAndCustomizablePaths($workingDir, $excludes, $customizableLocationList);

        $this->getCompleteMappingInfo($workingDir, $mappingList, $excludes, $customizableLocationList);

        $modifiedMappingList = array();
        $this->getConciseMappingInfo($modifiedMappingList, $mappingList);

        $mappings = array();
        foreach ($modifiedMappingList as $path) {
            $mappings[] = array($path, $path);
        }

        return $mappings;
    }

    /**
     * Gets exempt and customizable paths
     *
     * @param array string$workingDir
     * @param array $excludes
     * @param array $customizableLocationList
     * @return void
     */
    private function getExemptAndCustomizablePaths($workingDir, &$excludes, &$customizableLocationList)
    {
        $excludes = $this->patterns;
        for ($i = 0; $i<count($excludes); $i++) {
            $excludes[$i] = str_replace('\\', '/', $workingDir) . '/' . $excludes[$i];
        }
        $customizableLocationList = $this->customizablePaths;
        for ($i = 0; $i<count($customizableLocationList); $i++) {
            $customizableLocationList[$i] = str_replace('\\', '/',
                    $workingDir) . '/' . $customizableLocationList[$i];
        }
    }

    /**
     * Gets complete mapping info
     *
     * @param array string$workingDir
     * @param array $mappingList
     * @param array $excludes
     * @param array $customizableLocationList
     * @return void
     */
    private function getCompleteMappingInfo($workingDir, &$mappingList, $excludes, $customizableLocationList)
    {
        $directory = new \RecursiveDirectoryIterator($workingDir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $directory = new ExcludeFilter($directory, $excludes);
        $files = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            $file = str_replace('\\', '/', realpath($file));
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                continue;
            }
            if (!$this->checkExistence($customizableLocationList, $excludes, $file)) {
                array_push($mappingList, str_replace(str_replace('\\', '/', $workingDir) . '/', '', $file));
            }
        }

        asort($mappingList);
    }

    /**
     * Gets final mapping info
     *
     * @param array $modifiedMappingList
     * @param array $mappingList
     * @return void
     */
    private function getConciseMappingInfo(&$modifiedMappingList, $mappingList)
    {
        $index = 0;
        for ($i=0; $i<count($mappingList); $i++) {
            if ($i === 0) {
                array_push($modifiedMappingList, $mappingList[$i]);
                continue;
            }
            if (strpos($mappingList[$i], $mappingList[$index]) !== false) {
                if (mb_substr_count($mappingList[$i], '/') === mb_substr_count($mappingList[$index], '/')) {
                    array_push($modifiedMappingList, $mappingList[$i]);
                    $index = $i;
                }
            } else {
                array_push($modifiedMappingList, $mappingList[$i]);
                $index = $i;
            }
        }
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
