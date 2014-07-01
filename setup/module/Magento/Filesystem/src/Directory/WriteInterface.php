<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

interface WriteInterface extends ReadInterface
{
    /**
     * Check if given path is writable
     *
     * @param string $path [optional]
     * @return bool
     */
    public function isWritable($path = null);
}
