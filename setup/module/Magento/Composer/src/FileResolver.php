<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Composer;

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
     * Collect files and wrap them into an Iterator object
     *
     * @param string $filename
     * @return array
     */
    public function get($filename)
    {
        $paths = [];
        $files = $this->getFiles($this->config->getMagentoModulePath() . '*/*/' . $filename);
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
            return substr($path, strlen($basePath));
        } else {
            return $path;
        }
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
