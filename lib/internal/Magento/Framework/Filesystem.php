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

use Magento\Framework\Filesystem\FilesystemException;
use Magento\Framework\Filesystem\File\ReadInterface;

class Filesystem
{
    /**#@+
     * Content wrappers
     */
    const WRAPPER_CONTENT_ZLIB = 'compress.zlib';

    const WRAPPER_CONTENT_PHAR = 'phar';

    const WRAPPER_CONTENT_RAR = 'rar';

    const WRAPPER_CONTENT_OGG = 'ogg';

    /**#@-*/

    /**#@+
     * Remote resource Access Protocols
     */
    const HTTP = 'http';

    const HTTPS = 'https';

    /**#@-*/

    /**
     * System base temporary directory
     */
    const SYS_TMP = 'sys_tmp';

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
     * @var \Magento\Framework\Filesystem\WrapperFactory
     */
    protected $wrapperFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface[]
     */
    protected $writeInstances = array();

    /**
     * @var \Magento\Framework\Filesystem\File\ReadInterface[]
     */
    protected $remoteResourceInstances = array();

    /**
     * Path to system temporary directory
     *
     * @var string
     */
    private $sysTmpPath = null;

    /**
     * @param Filesystem\DirectoryList $directoryList
     * @param Filesystem\Directory\ReadFactory $readFactory
     * @param Filesystem\Directory\WriteFactory $writeFactory
     * @param Filesystem\File\ReadFactory $fileReadFactory
     * @param Filesystem\File\WriteFactory $fileWriteFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactory = null,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory = null
    ) {
        $this->directoryList = $directoryList;
        $this->readFactory = $readFactory;
        $this->writeFactory = $writeFactory;
        $this->fileReadFactory = $fileReadFactory;
        $this->fileWriteFactory = $fileWriteFactory;

        $this->driverFactory = new \Magento\Framework\Filesystem\DriverFactory($this->directoryList);
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
            $config = $this->getDirConfig($code);
            $this->readInstances[$code] = $this->readFactory->create($config, $this->driverFactory);
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
            $config = $this->getDirConfig($code);
            if (isset($config['read_only']) && $config['read_only']) {
                throw new FilesystemException(sprintf('The "%s" directory doesn\'t allow write operations', $code));
            }

            $this->writeInstances[$code] = $this->writeFactory->create($config, $this->driverFactory);
        }
        return $this->writeInstances[$code];
    }

    /**
     * Gets configuration of a directory
     *
     * @param string $code
     * @return string[]
     */
    protected function getDirConfig($code)
    {
        if (self::SYS_TMP == $code) {
            return [
                'path' => $this->getSysTmpPath(),
                'read_only' => false,
                'allow_create_dirs' => true,
                'permissions' => 0777
            ];
        }
        return $this->directoryList->getConfig($code);
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
        $config = $this->directoryList->getConfig($code);
        return isset($config['uri']) ? $config['uri'] : '';
    }

    /**
     * Gets system temporary directory path
     *
     * @return string
     */
    protected function getSysTmpPath()
    {
        if (null === $this->sysTmpPath) {
            $this->sysTmpPath = sys_get_temp_dir();
        }
        return $this->sysTmpPath;
    }
}
