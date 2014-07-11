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
 * A model that represents composer package
 */
class Package
{
    /**
     * Contents of composer.json
     *
     * @var \StdClass
     */
    private $json;

    /**
     * Path to the composer.json file
     *
     * @var string
     */
    private $file;

    /**
     * Constructor
     *
     * @param \StdClass $json
     * @param string $file
     */
    public function __construct(\StdClass $json, $file)
    {
        $this->json = $json;
        $this->file = $file;
    }

    /**
     * Get JSON contents
     *
     * @param bool $formatted
     * @param string|null $format
     * @return string|\StdClass
     */
    public function getJson($formatted = true, $format = null)
    {
        if ($formatted) {
            if (null === $format) {
                $format = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
            }
            return json_encode($this->json, $format) . "\n";
        }
        return $this->json;
    }

    /**
     * Get file path
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * A getter for properties of the package
     *
     * For example:
     *     $package->get('name');
     *     $package->get('version');
     *     $package->get('require->php');
     *
     * Returns whatever there is in the node or false if was unable to find this node
     *
     * @param string $propertyPath
     * @return mixed
     */
    public function get($propertyPath)
    {
        return $this->traverseGet($this->json, explode('->', $propertyPath));
    }

    /**
     * Traverse an \StdClass object recursively in search of the needed property
     *
     * @param \StdClass $json
     * @param array $chain
     * @param int $index
     * @return mixed
     */
    private function traverseGet(\StdClass $json, array $chain, $index = 0)
    {
        $property = $chain[$index];
        if (!property_exists($json, $property)) {
            return false;
        }
        if (isset($chain[$index + 1])) {
            return $this->traverseGet($json->{$property}, $chain, $index + 1);
        } else {
            return $json->{$property};
        }
    }

    /**
     * A setter for properties
     *
     * For example:
     *     $package->set('name', 'foo/bar');
     *     $package->set('require->foo/bar', '1.0.0');
     *     $package->set('extra->foo->bar', 'baz');
     *     $package->set('extra->foo', ['bar', 'baz']);
     *
     * @param string $propertyPath
     * @param mixed $value
     * @return void
     */
    public function set($propertyPath, $value)
    {
        $this->traverseSet($this->json, $value, explode('->', $propertyPath));
    }

    /**
     * Traverse an \StdClass object recursively and set the property by specified path (chain)
     *
     * @param \StdClass $target
     * @param mixed $value
     * @param array $chain
     * @param int $index
     * @return void
     */
    private function traverseSet(\StdClass $target, $value, array $chain, $index = 0)
    {
        $property = $chain[$index];
        if (isset($chain[$index + 1])) {
            if (!property_exists($target, $property)) {
                $target->{$property} = new \StdClass;
            }
            $this->traverseSet($target->{$property}, $value, $chain, $index + 1);
        } else {
            $target->{$property} = $value;
        }
    }

    public function getMappingList($workingDir)
    {
        $mappingList = array();
        //excluding paths those come as composer packages
        $excludes = file(str_replace('\\', '/',
            realpath(__DIR__ . '/../etc/magento_components_list.txt')), FILE_IGNORE_NEW_LINES);
        for($i=0; $i<count($excludes); $i++){
            $excludes[$i] = str_replace('\\', '/', $workingDir) . '/' . $excludes[$i];
        }
        //excluding paths those are customizable
        $customizableLocationList = file(str_replace('\\', '/',
            realpath(__DIR__ . '/../etc/magento_customizable_paths.txt')), FILE_IGNORE_NEW_LINES);
        for($i=0; $i<count($customizableLocationList); $i++){
            $customizableLocationList[$i] = str_replace('\\', '/',
                    $workingDir) . '/' . $customizableLocationList[$i];
        }
        $directory = new \RecursiveDirectoryIterator($workingDir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $directory = new ExcludeFilter($directory, $excludes);
        $files = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            $file = str_replace('\\', '/', realpath($file));
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                continue;
            }
            if (!$this->checkExistence($customizableLocationList, $excludes, $file)){
                array_push($mappingList, str_replace(str_replace('\\', '/', $workingDir) . '/', '', $file));
            }
        }

        asort($mappingList);
        $modifiedMappingList = array();
        $index = 0;
        for($i=0; $i<count($mappingList); $i++) {
            if ($i===0){
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

        $mappings = array();
        foreach($modifiedMappingList as $path){
            $mappings[] = array($path, $path);
        }

        return $mappings;
    }

    /**
     * Check existence of a path in exempt list
     *
     * @param array $customizableLocationList
     * @param array $excludes
     * @param string $file
     * @return void
     */
    protected function checkExistence($customizableLocationList, $excludes, $file)
    {
        foreach ($excludes as $path) {
            if (strpos($path, $file) !== false) return true;
        }

        foreach ($customizableLocationList as $path) {
            if ((strpos($path, $file) !== false)
                ||((strpos(str_replace('*', '', $path), $file)) !== false)) return true;
        }

        return false;
    }
}
