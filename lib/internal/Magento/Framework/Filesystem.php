<?php
/**
 * Magento filesystem facade
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

use Magento\Framework\Filesystem\File\ReadInterface;

class Filesystem
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $readFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteFactory
     */
    protected $writeFactory;

    /**
     * @var \Magento\Framework\Filesystem\File\ReadFactory
     */
    protected $fileReadFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface[]
     */
    protected $readInstances = array();

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface[]
     */
    protected $writeInstances = array();

    /**
     * @var \Magento\Framework\Filesystem\File\ReadInterface[]
     */
    protected $remoteResourceInstances = array();

    /**
     * @param Filesystem\DirectoryList $directoryList
     * @param Filesystem\Directory\ReadFactory $readFactory
     * @param Filesystem\Directory\WriteFactory $writeFactory
     * @param Filesystem\File\ReadFactory $fileReadFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactory = null
    ) {
        $this->directoryList = $directoryList;
        $this->readFactory = $readFactory;
        $this->writeFactory = $writeFactory;
        $this->fileReadFactory = $fileReadFactory;
    }

    /**
     * Create an instance of directory with write permissions
     *
     * @param string $code
     * @return \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    public function getDirectoryRead($code)
    {
        if (!array_key_exists($code, $this->readInstances)) {
            $this->readInstances[$code] = $this->readFactory->create($this->getDirPath($code));
        }
        return $this->readInstances[$code];
    }

    /**
     * Create an instance of directory with read permissions
     *
     * @param string $code
     * @return \Magento\Framework\Filesystem\Directory\WriteInterface
     * @throws \Magento\Framework\Filesystem\FilesystemException
     */
    public function getDirectoryWrite($code)
    {
        if (!array_key_exists($code, $this->writeInstances)) {
            $this->writeInstances[$code] = $this->writeFactory->create($this->getDirPath($code));
        }
        return $this->writeInstances[$code];
    }

    /**
     * Gets configuration of a directory
     *
     * @param string $code
     * @return string
     */
    protected function getDirPath($code)
    {
        return $this->directoryList->getPath($code);
    }

    /**
     * @param string $path
     * @param string|null $protocol
     * @return ReadInterface
     */
    public function getRemoteResource($path, $protocol = null)
    {
        if (!$this->fileReadFactory) {
            // case when a temporary Filesystem object is used for loading primary configuration
            return null;
        }

        if (empty($protocol)) {
            $protocol = strtolower(parse_url($path, PHP_URL_SCHEME));
            if ($protocol) {
                // Strip down protocol from path
                $path = preg_replace('#.+://#', '', $path);
            }
        }

        if (!array_key_exists($protocol, $this->remoteResourceInstances)) {
            $this->remoteResourceInstances[$protocol] = $this->fileReadFactory->create($path, $protocol);
        }
        return $this->remoteResourceInstances[$protocol];
    }

    /**
     * Retrieve uri for given code
     *
     * @param string $code
     * @return string
     */
    public function getUri($code)
    {
        return $this->directoryList->getUrlPath($code);
    }
}
