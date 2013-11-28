<?php
/**
 * Application primary config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\FileResolver;

/***
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */

class Primary implements \Magento\Config\FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var \Magento\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;


    /**
     * @param \Magento\Filesystem $filesystem
     * @param FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\App\Config\FileResolver\FileIteratorFactory $iteratorFactory
    ) {
        $this->directoryRead = $filesystem->getDirectoryRead(\Magento\Filesystem::APP);
        $this->iteratorFactory = $iteratorFactory;
        $this->filesystem = $filesystem;
    }
    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        return $this->iteratorFactory->create(
            $this->filesystem, $this->directoryRead->search('#/' . preg_quote($filename) . '$#')
        );
    }
}
