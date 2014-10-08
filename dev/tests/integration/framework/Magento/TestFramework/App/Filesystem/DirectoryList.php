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
}
