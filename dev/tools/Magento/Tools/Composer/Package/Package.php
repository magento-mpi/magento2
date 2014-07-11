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
     * The cloned data of composer.json
     *
     * @var \StdClass
     */
    private $clone;

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
     * Get JSON contents of composer.json
     *
     * @return \StdClass
     */
    public function getJson()
    {
        return $this->getClone();
    }

    /**
     * Replace the real JSON object with a clone to be able to track changes
     *
     * @return \StdClass
     */
    private function getClone()
    {
        if (null === $this->clone) {
            $this->clone = unserialize(serialize($this->json)); // a "clone" won't create object clones recursively
        }
        return $this->clone;
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
        $json = (null !== $this->clone) ? $this->clone : $this->json;
        return $this->traverseProperty($json, explode('->', $propertyPath));
    }

    /**
     * Traverse an \StdClass object recursively in search of the needed property
     *
     * @param \StdClass $json
     * @param array $chain
     * @param int $index
     * @return mixed
     */
    private function traverseProperty(\StdClass $json, array $chain, $index = 0)
    {
        $property = $chain[$index];
        if (!property_exists($json, $property)) {
            return false;
        }
        if (isset($chain[$index + 1])) {
            return $this->traverseProperty($json->{$property}, $chain, $index + 1);
        } else {
            return $json->{$property};
        }
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
     * Whether contents of the package was modified
     *
     * It is assumed as modified, as soon as someone has obtained the clone of object using getJson()
     *
     * @return bool
     */
    public function isModified()
    {
        return null === $this->clone;
    }

    /**
     * Set something into "require" section
     *
     * @param string $name
     * @param string $version
     */
    public function setRequire($name, $version)
    {
        $json = $this->getClone();
        $json->require->{$name} = $version;
    }

    /**
     * Set mapping info in "extra" section
     *
     * @param string $name
     * @param string $workingDir
     */
    public function setExtra($name, $workingDir)
    {
        $json = $this->getClone();
        $json->extra->{$name} = $this->getMappingList($workingDir);
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
