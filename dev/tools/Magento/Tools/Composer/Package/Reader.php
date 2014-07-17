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
        $excludesCombinations = [];
        $counter = count($excludes);
        for ($i = 0; $i < $counter; $i++) {
            $splitArray = explode("/", $excludes[$i]);
            $temp = '';
            for ($j = 0; $j < count(($splitArray)); $j++) {
                $temp .= '/' . $splitArray[$j];
                $excludesCombinations[] =  str_replace('\\', '/', $this->rootDir) . $temp;

            }
            $excludes[$i] = str_replace('\\', '/', $this->rootDir) . '/' . $excludes[$i];
        }

        $pathList = $this->customizablePaths;
        $counter = count($pathList);
        for ($i = 0; $i < $counter; $i++) {
            $locations = glob(str_replace('\\', '/', $this->rootDir) . '/' . $pathList[$i]);
            $allFiles = true;
            foreach ($locations as $location) {
                if ((is_dir($location)) && (!in_array($location, array('.', '..')))) {
                    $excludes[] = $location . '/';
                    $allFiles = false;
                    $location = str_replace(str_replace('\\', '/', $this->rootDir) . '/', '', $location);
                    $splitArray = explode("/", $location);
                    $temp = '';
                    for ($j = 0; $j < (count(($splitArray)) - 1); $j++) {
                        $temp .= '/' . $splitArray[$j];
                        $excludesCombinations[] =  str_replace('\\', '/', $this->rootDir) . $temp;
                    }
                }
            }
            if ($allFiles === true) {
                $excludesCombinations[] = str_replace('\\', '/', $this->rootDir)
                    . '/' . str_replace('/*', '', $pathList[$i]);
            }
            $pathList[$i] = str_replace('\\', '/', $this->rootDir) . '/' . $pathList[$i];
        }

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
        $index = 0;
        $counter = count($mappingList);
        for ($i = 0; $i < $counter; $i++) {
            if ($i === 0) {
                $result[] = $mappingList[$i];
                continue;
            }
            if (!(strpos($mappingList[$i] . '/', $mappingList[$index] . '/', 0) !== false)) {
                $result[] = $mappingList[$i];
                $index = $i;
            }
        }

        return $result;
    }
}
