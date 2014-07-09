<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Composer\Package;

/**
 * A collection of objects representing composer packages
 */
class Collection
{
    /**@#+
     * Propagate version across dependent components exactly as specified or using a wildcard
     */
    const DEPENDENCIES_EXACT = 'exact';
    const DEPENDENCIES_WILDCARD = 'wildcard';
    /**@#-*/

    /**
     * Composer package reader
     *
     * @var Reader
     */
    private $reader;

    /**
     * Map of component names to files
     *
     * @var string[]
     */
    private $files = [];

    /**
     * Map of component names to the original json objects
     *
     * @var \StdClass[]
     */
    private $packages = [];

    /**
     * Map of component names to the cloned json objects that may have been modified
     *
     * @var \StdClass[]
     */
    private $clones;

    /**
     * List of component names that have been modified
     *
     * @var string[]
     */
    private $modified = [];

    /**
     * Constructor
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Validate a value of the option how to update dependent components
     *
     * @param string $value
     * @param string $versionAgainst
     * @return void
     * @throws \InvalidArgumentException
     */
    public static function validateUpdateDependent($value, $versionAgainst)
    {
        switch ($value) {
            case self::DEPENDENCIES_EXACT:
                break;
            case self::DEPENDENCIES_WILDCARD:
                if (!preg_match('/^\d+\.\d+\.\d+$/', $versionAgainst)) {
                    throw new \InvalidArgumentException('Wildcard may be set only fo stable versions (format: x.y.z)');
                }
                break;
            case false:
                break;
            default:
                throw new \InvalidArgumentException("Unexpected value for 'dependent' argument: '{$value}'");
        }
    }

    /**
     * Read multiple package definitions
     *
     * @param string $pattern
     * @return void
     */
    public function readPackages($pattern)
    {
        foreach ($this->reader->readPattern($pattern) as $file => $json) {
            $this->add($file, $json);
        }
    }

    /**
     * Read one package definition
     *
     * @param string $subDir
     * @return void
     */
    public function readPackage($subDir)
    {
        list($file, $json) = $this->reader->readOne($subDir);
        if ($json) {
            $this->add($file, $json);
        }
    }

    /**
     * Add package definition to the collection
     *
     * @param string $file
     * @param \StdClass $json
     * @return void
     * @throws \LogicException
     */
    private function add($file, $json)
    {
        $this->clones = null;
        $this->modified = [];
        if (!isset($json->name)) {
            throw new \LogicException("No package name found in the file: {$file}");
        }
        if (isset($this->packages[$json->name])) {
            throw new \LogicException("The package '{$json->name}' was already read");
        }
        $this->packages[$json->name] = $json;
        $this->files[$json->name] = $file;
    }

    /**
     * Read all package names
     *
     * @return string[]
     */
    public function getPackageNames()
    {
        return array_keys($this->packages);
    }

    /**
     * Set a version to the package and optionally propagate the version in any other packages that depend on it
     *
     * @param string $package
     * @param string $version
     * @param bool|string $updateDependent
     * @return void
     */
    public function setVersion($package, $version, $updateDependent = false)
    {
        $json = $this->getPackage($package);
        $json->version = $version;
        $this->modified[$package] = $package;
        if ($updateDependent) {
            $this->updateDependent($json, $updateDependent);
        }
    }

    /**
     * Get a package object (which is safe for modifications)
     *
     * @param string $key
     * @return \StdClass
     * @throws \LogicException
     */
    public function getPackage($key)
    {
        $this->cloneAll();
        if (!isset($this->clones[$key])) {
            throw new \LogicException("Package not found: {$key}");
        }
        return $this->clones[$key];
    }

    /**
     * Get list of packages that were modified
     *
     * Returns an associative array of file => json object
     *
     * @return \StdClass[]
     */
    public function getModified()
    {
        $result = [];
        foreach ($this->modified as $name) {
            $result[$this->files[$name]] = $this->clones[$name];
        }
        return $result;
    }

    /**
     * Clone the original package definitions
     *
     * @return void
     */
    private function cloneAll()
    {
        if (null === $this->clones) {
            foreach ($this->packages as $key => $package) {
                $this->clones[$key] = clone $package;
            }
        }
    }

    /**
     * Update version information in packages that depend on this package
     *
     * @param \StdClass $subject
     * @param string $updateDependent
     * @return void
     */
    private function updateDependent($subject, $updateDependent)
    {
        self::validateUpdateDependent($updateDependent, $subject->version);
        if ($updateDependent == self::DEPENDENCIES_EXACT) {
            $newValue = $subject->version;
        } else {
            $newValue = preg_replace('/\.\d+$/', '.*', $subject->version);
        }
        $this->massUpdateByKey($subject->name, $newValue);
    }

    /**
     * Perform a mass-update of versions in "require" and "replace" sections in all packages
     *
     * @param string $subjectName
     * @param string $targetValue
     * @return void
     */
    private function massUpdateByKey($subjectName, $targetValue)
    {
        $keys = ['require', 'replace'];
        foreach ($this->packages as $json) {
            foreach ($keys as $key) {
                if (isset($json->{$key})) {
                    if (property_exists($json->{$key}, $subjectName)) {
                        $this->modified[$json->name] = $json->name;
                        $dependent = $this->clones[$json->name];
                        $dependent->{$key}->{$subjectName} = $targetValue;
                    }
                }
            }
        }
    }
}
