<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

use Magento\Filesystem\Driver\File;

class ReadFactory
{
    /**
     * Create a readable directory
     *
     * @param array $config
     * @return ReadInterface
     */
    public function create(array $config)
    {
        return new Read(new File(), $config);
    }
}
