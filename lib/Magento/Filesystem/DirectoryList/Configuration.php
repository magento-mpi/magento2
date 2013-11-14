<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\DirectoryList;

use Magento\Filesystem\DirectoryList;

class Configuration
{
    /**
     * Path to filesystem directory configuration
     *
     * @var string
     */
    const XML_FILESYSTEM_DIRECTORY_PATH = 'system/filesystem/directory';

    /**
     * Filesystem Directory configuration
     *
     * @var array
     */
    protected $directories;

    /**
     * Store directory configuration
     *
     * @param \Magento\Core\Model\ConfigInterface $config
     */
    public function __construct(\Magento\Core\Model\ConfigInterface $config)
    {
        $this->directories = $config->getValue(self::XML_FILESYSTEM_DIRECTORY_PATH);
    }

    /**
     * Add directories from configuration to Filesystem
     *
     * @param DirectoryList $directoryList
     */
    public function configure(DirectoryList $directoryList)
    {
        foreach ($this->directories as $code => $directoryConfiguration) {
            $directoryList->addDirectory($code, $directoryConfiguration);
        }
    }
}