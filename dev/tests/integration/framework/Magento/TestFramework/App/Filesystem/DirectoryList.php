<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\App\Filesystem;

class DirectoryList extends \Magento\App\Filesystem\DirectoryList
{
    /**
     * Check whether configured directory
     *
     * @param string $code
     * @return bool
     */
    public function isConfigured($code)
    {
        return false;
    }

    /**
     * Add directory configuration
     *
     * @param string $code
     * @param array $directoryConfig
     */
    public function addDirectory($code, array $directoryConfig)
    {
        if (!isset($directoryConfig['path'])) {
            $directoryConfig['path'] = null;
        }
        if (!$this->isAbsolute($directoryConfig['path'])) {
            $directoryConfig['path'] = $this->makeAbsolute($directoryConfig['path']);
        }

        $this->directories[$code] = $directoryConfig;
    }
}
