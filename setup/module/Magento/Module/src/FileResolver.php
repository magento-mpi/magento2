<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Module;

use Magento\Config\Config;
use Zend\Stdlib\Glob;
use Magento\Config\FileResolverInterface;
use Magento\Config\FileIteratorFactory;
use Magento\Config\ConfigFactory;

class FileResolver implements FileResolverInterface
{
    /**
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param FileIteratorFactory $iteratorFactory
     * @param ConfigFactory $configFactory
     * @internal param Config $config
     */
    public function __construct(
        FileIteratorFactory $iteratorFactory,
        ConfigFactory $configFactory
    ) {
        $this->iteratorFactory = $iteratorFactory;
        $this->configFactory = $configFactory;
        $this->config = $this->configFactory->create();
    }

    /**
     * @param string $filename
     * @return array
     */
    public function get($filename)
    {
        $paths = [];

        // Collect files by /app/code/*/*/etc/{filename} pattern
        $files = $this->getFiles($this->config->getMagentoModulePath() . '*/*/etc/' . $filename);
        foreach ($files as $file) {
            $paths[] = $this->getRelativePath($file);
        }

        // Collect files by /app/etc/*/{filename} pattern
        $files = $this->getFiles($this->config->getMagentoConfigPath() . '*/' . $filename);
        foreach ($files as $file) {
            $paths[] = $this->getRelativePath($file);
        }

        return $this->iteratorFactory->create($this->config->getMagentoBasePath(), $paths);
    }

    /**
     * Retrieves relative path
     *
     * @param string $path
     * @return string
     */
    protected function getRelativePath($path = null)
    {
        $basePath = $this->config->getMagentoBasePath();
        if (strpos($path, $basePath) === 0
            || $basePath == $path . '/') {
            $result = substr($path, strlen($basePath));
        } else {
            $result = $path;
        }
        return $result;
    }

    /**
     * @param string $path
     * @return array|false
     */
    protected function getFiles($path)
    {
        return Glob::glob($this->config->getMagentoBasePath() . $path);
    }
}
