<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class PhpVerifications
{
    /**
     * List of required extensions
     *
     * @var array
     */
    protected $required;

    /**
     * List of currently installed extensions
     *
     * @var array
     */
    protected $current = [];

    /**
     * Interface to read composer.lock file
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $rootDir;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->rootDir = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    /**
     * Retrieves required php version
     *
     * @return string
     * @throws \Exception If composer.lock file is not found or if attributes are missing.
     */
    public function getPhpVersion()
    {
        if (!$this->rootDir->isExist('composer.lock')) {
            throw new \Exception('Cannot read `composer.lock` file');
        }
        $composerInfo = json_decode($this->rootDir->readFile('composer.lock'), true);
        if (!empty($composerInfo['platform']['php'])) {
            return $composerInfo['platform']['php'];
        } else {
            throw new \Exception('Missing php version in `composer.lock`');
        }
    }

    /**
     * Retrieve list of required extensions
     *
     * Collect required extensions from composer.lock file
     *
     * @return array
     * @throws \Exception If composer.lock file is not found or if attributes are missing.
     */
    public function getRequired()
    {
        if (null === $this->required) {
            if (!$this->rootDir->isExist('composer.lock')) {
                throw new \Exception('Cannot read `composer.lock` file');
            }
            $composerInfo = json_decode($this->rootDir->readFile('composer.lock'), true);
            $declaredDependencies = [];

            if (!empty($composerInfo['platform-dev'])) {
                $declaredDependencies = array_merge($declaredDependencies, array_keys($composerInfo['platform-dev']));
            } else {
                throw new \Exception('Missing platform-dev in `composer.lock`');
            }
            if (!empty($composerInfo['packages'])) {
                foreach ($composerInfo['packages'] as $package) {
                    if (!empty($package['require'])) {
                        $declaredDependencies = array_merge($declaredDependencies, array_keys($package['require']));
                    }
                }
            } else {
                throw new \Exception('Missing packages in `composer.lock`');
            }
            if ($declaredDependencies) {
                $declaredDependencies = array_unique($declaredDependencies);
                $phpDependencies = [];
                foreach ($declaredDependencies as $dependency) {
                    if (stripos($dependency, 'ext-') === 0) {
                        $phpDependencies[] = substr($dependency, 4);
                    }
                }
                $this->required = array_unique($phpDependencies);
            }
        }
        return $this->required;
    }

    /**
     * Retrieve list of currently installed extensions
     *
     * @return array
     */
    public function getCurrent()
    {
        if (!$this->current) {
            $this->current = array_map('strtolower', get_loaded_extensions());
        }
        return $this->current;
    }
}
