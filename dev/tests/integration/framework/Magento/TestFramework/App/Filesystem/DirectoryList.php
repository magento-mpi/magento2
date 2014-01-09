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
     * Add directory configuration
     *
     * @param string $code
     * @param array $directoryConfig
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function addDirectory($code, array $directoryConfig)
    {
        try{
            parent::addDirectory($code, $directoryConfig);
        } catch(\Exception $e) {
            // Ignore exception during executing tests
        }
    }
}
