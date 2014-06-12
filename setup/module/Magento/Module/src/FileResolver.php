<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Module;

use Zend\ServiceManager\ServiceLocatorInterface;
use Magento\Config\FileResolverInterface;
use Magento\Config\FileIteratorFactory;

class FileResolver implements FileResolverInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var \Magento\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param \Magento\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        FileIteratorFactory $iteratorFactory
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->iteratorFactory = $iteratorFactory;
        $this->configuration = $this->serviceLocator->get('config')['parameters'];
    }

    /**
     * @param string $filename
     * @return array
     */
    public function get($filename)
    {
        $paths = [];

        $pattern = $this->configuration['magento']['base_path']
            . $this->configuration['magento']['filesystem']['module']
            . '*/*/etc/' . $filename;
        $files = glob($pattern);

        foreach ($files as $file) {
            $paths[] = $this->getRelativePath($file);
        }

        $path = $this->configuration['magento']['base_path']
            . $this->configuration['magento']['filesystem']['config']
            . '*/' . $filename;

        $files = glob($path);

        foreach ($files as $file) {
            $paths[] = $this->getRelativePath($file);
        }

        return $this->iteratorFactory->create($this->configuration['magento']['base_path'], $paths);
    }

    /**
     * Retrieves relative path
     *
     * @param string $path
     * @return string
     */
    protected function getRelativePath($path = null)
    {
        $basePath = $this->configuration['magento']['base_path'];
        if (strpos($path, $basePath) === 0
            || $basePath == $path . '/') {
            $result = substr($path, strlen($basePath));
        } else {
            $result = $path;
        }
        return $result;
    }
}
