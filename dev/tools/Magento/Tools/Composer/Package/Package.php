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

    /**
     * Unset a property by specified path
     *
     * @param string $path
     * @return void
     */
    public function unsetProperty($path)
    {
        $chain = explode('->', $path);
        $this->traverseUnset($this->json, $chain, count($chain) - 1);
    }

    /**
     * Traverse an \StdClass object recursively and unset the property by specified path (chain)
     *
     * @param \StdClass $json
     * @param array $chain
     * @param int $endIndex
     * @param int $index
     * @return void
     */
    private function traverseUnset(\StdClass $json, array $chain, $endIndex, $index = 0)
    {
        $key = $chain[$index];
        if ($index < $endIndex) {
            if (isset($json->{$key}) && isset($chain[$index + 1])) {
                $this->traverseUnset($json->{$key}, $chain, $endIndex, $index + 1);
            }
        } else {
            unset($json->{$key});
        }
    }
}
