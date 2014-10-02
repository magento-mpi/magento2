<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataProvider\Config;

use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\FileIteratorFactory;
use Magento\Framework\App\Filesystem;

/**
 * Class FileResolver
 */
class FileResolver implements \Magento\Framework\Config\FileResolverInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param Filesystem $filesystem
     * @param FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        Filesystem $filesystem,
        FileIteratorFactory $iteratorFactory
    ) {
        $this->directoryRead = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::MODULES_DIR);
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($filename, $scope)
    {
        $iterator = $this->iteratorFactory->create(
            $this->directoryRead,
            $this->directoryRead->search('/*/*/etc/datasource/*')
        );
        return $iterator;
    }
}
