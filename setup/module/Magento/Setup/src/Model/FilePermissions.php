<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Config\Config;
use Magento\Config\ConfigFactory;
use Magento\Filesystem\Filesystem;

class FilePermissions
{
    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * List of required directories
     *
     * @var array
     */
    protected $required = [];

    /**
     * List of currently existed directories
     *
     * @var array
     */
    protected $current = [];

    /**
     * @param Filesystem $filesystem
     * @param ConfigFactory $configFactory
     */
    public function __construct(
        Filesystem $filesystem,
        ConfigFactory $configFactory
    ) {
        $this->filesystem = $filesystem;

        $this->configFactory = $configFactory;
        $this->config = $this->configFactory->create();
    }

    /**
     * Retrieve list of required directories
     *
     * @return array
     */
    public function getRequired()
    {
        if (!$this->required) {
            foreach ($this->config->getMagentoFilePermissions() as $code => $config) {
                if (isset($config['path'])) {
                    $this->required[$code] = $config['path'];
                }
            }
        }
        return array_values($this->required);
    }

    /**
     * Retrieve list of currently existed directories
     *
     * @return array
     */
    public function getCurrent()
    {
        if (!$this->current) {
            foreach ($this->required as $code => $path) {
                if (!$this->validate($code)) {
                    continue;
                }
                $this->current[$code] = $path;
            }
        }
        return array_values($this->current);
    }

    /**
     * Validate directory permissions by given directory code
     *
     * @param string $code
     * @return bool
     */
    protected function validate($code)
    {
        $directory = $this->filesystem->getDirectoryWrite($code);
        if (!$directory->isExist()) {
            return false;
        }
        if (!$directory->isDirectory()) {
            return false;
        }
        if (!$directory->isReadable()) {
            return false;
        }
        if (!$directory->isWritable()) {
            return false;
        }
        return true;
    }

    /**
     * Checks if has file permission or not
     *
     * @return array
     */
    public function checkPermission()
    {
        $required = $this->getRequired();
        $current = $this->getCurrent();
        return array_diff($required, $current);
    }
}
