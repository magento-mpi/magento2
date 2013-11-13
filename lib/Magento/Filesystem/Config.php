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
     * @var string
     */
    protected $root;

    /**
     * Store directory configuration
     *
     * @param string $rootDirectory
     * @param array $directories
     */
    public function __construct($rootDirectory, array $directories)
    {
        $this->config = $directories;
        $this->root = $rootDirectory;
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