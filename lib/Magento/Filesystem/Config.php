<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\Directory;

use Magento\Filesystem\FilesystemException;;

class Config
{
    /**
     * Path to filesystem directory configuration
     *
     * @var string
     */
    const XML_FILESYSTEM_DIRECTORY_PATH = 'filesystem/directory';

    /**
     * Filesystem Directory configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Store directory configuration
     *
     * @param \Magento\Core\Model\ConfigInterface $config
     */
    public function __construct(\Magento\Core\Model\ConfigInterface $config)
    {
        $this->config = $config->getValue(self::XML_FILESYSTEM_DIRECTORY_PATH);
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
}