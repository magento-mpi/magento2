<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem;

class Config
{
    /**
     * Filesystem Directory configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Root directory
     *
     * @var string
     */
    protected $root;

    /**
     * Store directory configuration
     *
     * @param string $rootDirectory
     * @param array $directories
     * @param array $directoryPaths
     */
    public function __construct($rootDirectory, array $directories, array $directoryPaths)
    {
        $this->root = $rootDirectory;
        $this->config = $this->updatePaths($directories, $directoryPaths);
    }

    /**
     * Update directories paths
     *
     * @param array $directories
     * @param $directoryPaths
     * @return array
     */
    protected function updatePaths(array $directories, $directoryPaths)
    {
        foreach ($directoryPaths as $code => $path) {
            $directories[$code]['path'] = $path;
        }
        return $directories;
    }

    /**
     * Directory configuration
     *
     * @param string $code
     * @return array
     * @throws FilesystemException
     */
    public function get($code)
    {
        if(isset($this->config[$code])) {
            return $this->config[$code];
        }
        throw new FilesystemException("Cannot get configuration for directory $code!");
    }

    /**
     * Get root directory path
     *
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }
}