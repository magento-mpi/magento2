<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Filesystem\DirectoryList;

use Magento\App\Filesystem;

/**
 * Class Configuration
 * @package Magento\App\Filesystem\DirectoryList
 */
class Configuration implements \Magento\Filesystem\ConfigurationInterface
{
    /**
     * Path to filesystem directory configuration
     *
     * @var string
     */
    const XML_FILESYSTEM_DIRECTORY_PATH = 'system/filesystem/directory';

    /**
     * Declaration wrapper configuration
     */
    const XML_FILESYSTEM_WRAPPER_PATH = 'system/filesystem/protocol';

    /**
     * Filesystem protocols configuration
     *
     * @var array
     */
    protected $protocols;

    /**
     * Store directory configuration
     *
     * @param \Magento\Core\Model\ConfigInterface $config
     */
    public function __construct(\Magento\Core\Model\ConfigInterface $config)
    {
        $this->directories = $config->getValue(self::XML_FILESYSTEM_DIRECTORY_PATH) ?: array();
        $this->protocols = $config->getValue(self::XML_FILESYSTEM_WRAPPER_PATH) ?: array();
    }

    /**
     * @return array
     */
    public function getDirectories()
    {
        return $this->directories;
    }
}
