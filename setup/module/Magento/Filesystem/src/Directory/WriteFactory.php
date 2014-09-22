<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

use Magento\Filesystem\Driver\File;

class WriteFactory
{
    /**
     * @param array $config
     * @return \Magento\Filesystem\Directory\Write
     */
    public function create(array $config)
    {
        return new Write(new File(), $config);
    }
}
