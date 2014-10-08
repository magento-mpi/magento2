<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Framework\App\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;

/**
 * Class Configuration
 */
class Configuration
{
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
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $config)
    {
        $this->protocols = $config->getValue(self::XML_FILESYSTEM_WRAPPER_PATH) ?: array();
    }

    /**
     * Add directories from configuration to Filesystem
     *
     * @param DirectoryList $directoryList
     * @return void
     */
    public function configure(DirectoryList $directoryList)
    {
        foreach ($this->protocols as $code => $protocolConfiguration) {
            $directoryList->addProtocol($code, $protocolConfiguration);
        }
    }
}
