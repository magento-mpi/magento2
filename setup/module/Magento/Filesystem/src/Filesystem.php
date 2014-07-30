<?php
/**
 * Magento filesystem facade
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem;

use Magento\Filesystem\Directory\ReadFactory;
use Magento\Filesystem\Directory\ReadInterface;
use Magento\Filesystem\Directory\WriteFactory;
use Magento\Filesystem\Directory\WriteInterface;

class Filesystem
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var ReadFactory
     */
    protected $readFactory;

    /**
     * @var WriteFactory
     */
    protected $writeFactory;

    /**
     * @var ReadInterface[]
     */
    protected $readInstances = array();

    /**
     * @var WriteInterface[]
     */
    protected $writeInstances = array();

    /**
     * @param DirectoryList $directoryList
     * @param ReadFactory $readFactory
     * @param WriteFactory $writeFactory
     */
    public function __construct(
        DirectoryList $directoryList,
        ReadFactory $readFactory,
        WriteFactory $writeFactory
    ) {
        $this->directoryList = $directoryList;
        $this->readFactory = $readFactory;
        $this->writeFactory = $writeFactory;
    }

    /**
     * Create an instance of directory with write permissions
     *
     * @param string $code
     * @return ReadInterface
     */
    public function getDirectoryRead($code)
    {
        if (!array_key_exists($code, $this->readInstances)) {
            $config = $this->directoryList->getConfig($code);
            $this->readInstances[$code] = $this->readFactory->create($config);
        }
        return $this->readInstances[$code];
    }

    /**
     * Create an instance of directory with read permissions
     *
     * @param string $code
     * @return WriteInterface
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function getDirectoryWrite($code)
    {
        if (!array_key_exists($code, $this->writeInstances)) {
            $config = $this->directoryList->getConfig($code);
            $this->writeInstances[$code] = $this->writeFactory->create($config);
        }
        return $this->writeInstances[$code];
    }
}
