<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

use Magento\Filesystem\FilesystemException;

class Write extends Read implements WriteInterface
{
    /**
     * Check if given path is writable
     *
     * @param null $path
     * @return bool
     * @throws FilesystemException
     */
    public function isWritable($path = null)
    {
        return $this->driver->isWritable($this->driver->getAbsolutePath($this->path, $path));
    }

    /**
     * Change permissions of given path
     *
     * @param string $path
     * @param int $permissions
     * @return bool
     * @throws FilesystemException
     */
    public function changePermissions($path, $permissions)
    {
        $absolutePath = $this->driver->getAbsolutePath($this->path, $path);
        return $this->driver->changePermissions($absolutePath, $permissions);
    }

    /**
     * Write contents to file in given mode
     *
     * @param string $path
     * @param string $content
     * @param string|null $mode
     * @return int The number of bytes that were written.
     * @throws FilesystemException
     */
    public function writeFile($path, $content, $mode = 'w+')
    {
        $result = @file_put_contents($this->getAbsolutePath($path), $content, $mode);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The specified "%s" file could not be written', $this->getAbsolutePath($path))
            );
        }
        return $result;
    }
}
