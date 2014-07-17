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
     * Propagate version across dependent components
     */
    const UPDATE_DEPENDENT_NONE = '';
    const UPDATE_DEPENDENT_EXACT = 'exact';
    const UPDATE_DEPENDENT_WILDCARD = 'wildcard';
    /**@#-*/

    /**
     * Map of component names to the original json objects
     *
     * @var Package[]
     */
    private $packages = [];

    /**
     * @var string
     */
    private $version;

    /**
     * Which way to update dependent components
     *
     * @var string
     */
    private $updateDependent;

    /**
     * Constructor
     *
     * @param string $version
     * @param string $updateDependent
     */
    public function __construct($version, $updateDependent = self::UPDATE_DEPENDENT_NONE)
    {
        Version::validate($version);
        $this->version = $version;
        $this->updateDependent = $updateDependent;
        $this->getDependentVersion();
    }

    /**
     * Add package definition to the collection
     *
     * @param Package $package
     * @return void
     * @throws \LogicException
     */
    public function add(Package $package)
    {
        $name = $package->get('name');
        if (false === $name) {
            throw new \LogicException("No package name found in the file: {$package->getFile()}");
        }
        if (isset($this->packages[$name])) {
            throw new \LogicException("The package '{$name}' already exists in collection");
        }
        $this->packages[$name] = $package;
    }

    /**
     * Get the collection of packages
     *
     * @return Package[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Set a version to the package and optionally propagate the version in any other packages that depend on it
     *
     * @param string $packageName
     * @return void
     */
    public function setVersion($packageName)
    {
        $package = $this->getPackage($packageName);
        $package->set('version', $this->version);
        $dependentVersion = $this->getDependentVersion($this->version);
        if ($dependentVersion) {
            $this->massUpdateByKey($packageName, $dependentVersion);
        }
    }

    /**
     * Get a package object
     *
     * @param string $name
     * @return Package
     * @throws \LogicException
     */
    public function getPackage($name)
    {
        if (!isset($this->packages[$name])) {
            throw new \LogicException("Package not found: {$name}");
        }
        return $this->packages[$name];
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
        foreach ($this->packages as $package) {
            foreach ($keys as $key) {
                if ($package->get("{$key}->{$subjectName}")) {
                    $package->set("{$key}->{$subjectName}", $targetValue);
                }
            }
        }
    }

    /**
     * Validate/filter a version and determine what version to specify to dependent components
     *
     * @param string $versionAgainst
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getDependentVersion($versionAgainst = '')
    {
        $value = $this->updateDependent;
        switch ($value) {
            case self::UPDATE_DEPENDENT_EXACT:
                return $versionAgainst;
            case self::UPDATE_DEPENDENT_WILDCARD:
                if ($versionAgainst) {
                    if (!preg_match('/^\d+\.\d+\.\d+$/', $versionAgainst)) {
                        throw new \InvalidArgumentException(
                            'Wildcard may be set only fo stable versions (format: x.y.z)'
                        );
                    }
                    return preg_replace('/\.\d+$/', '.*', $versionAgainst);
                }
                return '';
            case self::UPDATE_DEPENDENT_NONE:
                return '';
            default:
                throw new \InvalidArgumentException("Unexpected value for 'dependent' argument: '{$value}'");
        }
    }
}
