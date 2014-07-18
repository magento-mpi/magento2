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
     * List of paths that can be customized
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
     * Read the list of customizable paths
     *
     * @return string[]
     */
    public function getCustomizablePaths()
    {
        return $this->customizablePaths;
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
        //adding this manually as we have yet not created the composer.json
        $modifiedMappingList[] = "composer.json";

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

        list($excludesCombinationsComponents, $excludesComponents) = $this->getExcludeCombinationsComponents();
        list($excludesCombinationsCustoms, $excludesCustoms) = $this->getExcludeCombinationsCustomPaths();

        $excludesCombinations = array_merge($excludesCombinationsComponents, $excludesCombinationsCustoms);
        $excludes = array_merge($excludesComponents, $excludesCustoms);

        $directory = new \RecursiveDirectoryIterator($this->rootDir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $directory = new ExcludeFilter($directory, $excludes);
        $files = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            $file = str_replace('\\', '/', realpath($file));
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                continue;
            }
            if (!in_array($file, $excludesCombinations)) {
                $result[] = str_replace(str_replace('\\', '/', $this->rootDir) . '/', '', $file);
            }
        }

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

        if (empty($mappingList)) {
            return [];
        }
        $lastAdded = $mappingList[0];
        $result[] = $lastAdded;
        foreach ($mappingList as $item) {
            if (strpos($item . '/', $lastAdded . '/', 0) === false) {
                $result[] = $item;
                $lastAdded = $item;
            }
        }
        return $result;
    }

    /**
     * Gets paths that should be excluded for Composer components with combinations
     *
     * @return list
     */
    private function getExcludeCombinationsComponents()
    {
        $excludesCombinations = [];

        $excludes = $this->patterns;
        $counter = count($excludes);
        for ($i = 0; $i < $counter; $i++) {
            $splitArray = explode("/", $excludes[$i]);
            $count = count($splitArray);
            $path = '';
            for ($j = 0; $j < ($count - 1); $j++) {
                $path .= '/' . $splitArray[$j];
            }
            $locations = scandir(str_replace('\\', '/', $this->rootDir) . '/' . $path);
            $found = false;
            foreach ($locations as $location) {
                if (!in_array($location, array('.', '..', $splitArray[$count -1]))) {
                    $found = true;
                }
            }
            $splitArray = explode("/", $excludes[$i]);
            if ($found === false) {
                $count = count($splitArray) - 2;
            } else {
                $count = count($splitArray);
            }
            $path = '';
            for ($j = 0; $j < $count; $j++) {
                $path .= '/' . $splitArray[$j];
                $excludesCombinations[] =  str_replace('\\', '/', $this->rootDir) . $path;
            }
            $excludes[$i] = str_replace('\\', '/', $this->rootDir) . '/' . $excludes[$i];
        }

        return array($excludesCombinations, $excludes);
    }

    /**
     * Gets paths that should be excluded as customizable locations with combinations
     *
     * @return list
     */
    private function getExcludeCombinationsCustomPaths()
    {
        $excludesCombinations = [];

        $rootPath = str_replace('\\', '/', $this->rootDir);
        $pathList = $this->customizablePaths;
        $counter = count($pathList);
        for ($i = 0; $i < $counter; $i++) {
            $fullPath = str_replace('\\', '/', $this->rootDir) . '/' . str_replace('*', '', $pathList[$i]);
            $locations = scandir($fullPath);
            foreach ($locations as $location) {
                if (!in_array($location, array('.', '..'))) {
                    if (is_dir($fullPath . $location)) {
                        $excludes[] = $fullPath . $location . '/';
                    }
                    $splitArray = explode("/", str_replace('*', '', $pathList[$i]) . $location);
                    $path = '';
                    for ($j = 0; $j < (count($splitArray) - 1); $j++) {
                        $path .= '/' . $splitArray[$j];
                        $excludesCombinations[] =  str_replace('\\', '/', $this->rootDir) . $path;
                    }
                }
            }
        }

        return array($excludesCombinations, $excludes);
    }
}
