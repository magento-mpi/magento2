<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

use Magento\Filesystem\Driver\DriverInterface;

class Read implements ReadInterface
{
    /**
     * Directory path
     *
     * @var string
     */
    protected $path;

    /**
     * Filesystem driver
     *
     * @var \Magento\Filesystem\Driver\DriverInterface
     */
    protected $driver;

    /**
     * @param DriverInterface $driver
     * @param array $config
     */
    public function __construct(
        DriverInterface $driver,
        array $config
    ) {
        $this->driver = $driver;
        $this->setProperties($config);
    }

    /**
     * Set properties from config
     *
     * @param array $config
     * @return void
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function setProperties(array $config)
    {
        if (!empty($config['path'])) {
            $this->path = rtrim(str_replace('\\', '/', $config['path']), '/') . '/';
        }
    }

    /**
     * Retrieves absolute path
     * E.g.: /var/www/application/file.txt
     *
     * @param string $path
     * @return string
     */
    public function getAbsolutePath($path = null)
    {
        return $this->driver->getAbsolutePath($this->path, $path);
    }

    /**
     * Check a file or directory exists
     *
     * @param string $path [optional]
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isExist($path = null)
    {
        return $this->driver->isExists($this->driver->getAbsolutePath($this->path, $path));
    }

    /**
     * Check permissions for reading file or directory
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isReadable($path = null)
    {
        return $this->driver->isReadable($this->driver->getAbsolutePath($this->path, $path));
    }

    /**
     * Check whether given path is directory
     *
     * @param string $path
     * @return bool
     */
    public function isDirectory($path = null)
    {
        return $this->driver->isDirectory($this->driver->getAbsolutePath($this->path, $path));
    }
}
