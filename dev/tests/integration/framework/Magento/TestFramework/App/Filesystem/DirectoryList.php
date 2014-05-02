<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\App\Filesystem;

class DirectoryList extends \Magento\Framework\App\Filesystem\DirectoryList
{
    /**
     * Check whether configured directory
     *
     * @param string $code
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
     * @param bool $overrideConfig
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addDirectory($code, array $directoryConfig, $overrideConfig = false)
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
