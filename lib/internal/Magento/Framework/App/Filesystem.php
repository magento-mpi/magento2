<?php
/**
 * Magento application filesystem facade
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

use Magento\Framework\App\Filesystem\DirectoryList;

class Filesystem extends \Magento\Framework\Filesystem
{
    /**
     * Retrieve absolute path for for given code
     *
     * @param string $code
     * @return string
     */
    public function getPath($code = DirectoryList::ROOT_DIR)
    {
        $config = $this->directoryList->getConfig($code);
        $path = isset($config['path']) ? $config['path'] : '';
        return str_replace('\\', '/', $path);
    }
}
