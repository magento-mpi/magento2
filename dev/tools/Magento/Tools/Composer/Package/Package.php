<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Composer\Package;

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
}
