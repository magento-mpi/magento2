<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Module;

use Zend\Stdlib\Glob;
use Magento\Config\FileResolverInterface;
use Magento\Config\FileIteratorFactory;
use Magento\Config\ConfigFactory;

class FileResolver implements FileResolverInterface
{
    /**
     * @var \Magento\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @var \Magento\Config\ConfigFactory
     */
    protected $configFactory;

    /**
     * @var \Magento\Config\Config
     */
    protected $config;

    /**
     * @param \Magento\Config\FileIteratorFactory $iteratorFactory
     * @param \Magento\Config\ConfigFactory $configFactory
     * @internal param \Magento\Config\Config $config
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
        $files = $this->getFiles($this->config->magento->filesystem->module . '*/*/etc/' . $filename);
        foreach ($files as $file) {
            $paths[] = $this->getRelativePath($file);
        }

        // Collect files by /app/etc/*/{filename} pattern
        $files = $this->getFiles($this->config->magento->filesystem->config . '*/' . $filename);
        foreach ($files as $file) {
            $paths[] = $this->getRelativePath($file);
        }

        return $this->iteratorFactory->create($this->config->magento->basePath, $paths);
    }

    /**
     * Retrieves relative path
     *
     * @param string $path
     * @return string
     */
    protected function getRelativePath($path = null)
    {
        $basePath = $this->config->magento->basePath;
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
        return Glob::glob($this->config->magento->basePath . $path);
    }
}
