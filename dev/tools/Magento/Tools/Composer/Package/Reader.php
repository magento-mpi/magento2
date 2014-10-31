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
        $this->rootDir = str_replace('\\', '/', $rootDir);
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

        $filteredMappingList = $this->getConciseMappingInfo($mappingList);

        $mappings = array();
        foreach ($filteredMappingList as $path) {
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

        $excludes = $this->getExcludePaths();
        $directory = new \RecursiveDirectoryIterator($this->rootDir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $directory = new ExcludeFilter($directory, $excludes);
        $paths = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);

        $excludesCombinations = $this->getExcludeCombinations();

        foreach ($paths as $path) {
            $path = str_replace('\\', '/', realpath($path));

            if (!in_array($path, $excludesCombinations)) {
                $result[] = str_replace($this->rootDir . '/', '', $path);
            }
        }

        return $result;
    }

    /**
     * Gets final filtered mapping info
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
            if (!(strncmp($item . '/', $lastAdded . '/', strlen($lastAdded . '/')) === 0)) {
                $result[] = $item;
                $lastAdded = $item;
            }
        }
        return $result;
    }

    /**
     * Gets paths that should be excluded during iterative searches for locations
     *
     * @return array
     */
    public function getExcludePaths()
    {
        $excludes = [];
        foreach ($this->patterns as $pattern) {
            $excludes[] = $this->rootDir . '/' . $pattern;
        }
        $excludes[] = $this->rootDir . '/.idea';
        $excludes[] = $this->rootDir . '/.git';
        $excludes[] = $this->rootDir . '/app/etc/vendor_path.php';
        $excludes[] = $this->rootDir . '/composer.json';
        $excludes[] = $this->rootDir . '/README.md';
        $excludes[] = $this->rootDir . '/.gitignore';

        return $excludes;
    }

    /**
     * Gets combinations of excluded paths
     *
     * @return array
     */
    private function getExcludeCombinations()
    {
        $excludesCombinations = [];

        //Dealing components list
        foreach ($this->patterns as $component) {
            $excludesCombinations = array_merge(
                $excludesCombinations,
                $this->getPathCombinations($component)
            );
        }
        //Dealing customizable locations list
        foreach ($this->customizablePaths as $customPath) {
            $excludesCombinations = array_merge(
                $excludesCombinations,
                $this->getPathCombinations($customPath)
            );
        }

        return array_unique($excludesCombinations);
    }

    /**
     * Gets combinations for a path
     *
     * @param string $path
     * @return array
     */
    private function getPathCombinations($path)
    {
        $excludesCombinations = [];

        $splitArray = explode('/', $path);
        $pathCombination = '';
        foreach ($splitArray as $split) {
            $pathCombination .= '/' . $split;
            $excludesCombinations[] =  $this->rootDir . $pathCombination;
        }

        return $excludesCombinations;
    }
}
