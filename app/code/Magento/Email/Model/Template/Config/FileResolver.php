<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Model\Template\Config;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\FileIteratorFactory;

class FileResolver implements \Magento\Framework\Config\FileResolverInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * @var \Magento\Framework\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Framework\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\Framework\App\Filesystem $filesystem,
        FileIteratorFactory $iteratorFactory
    ) {
        $this->directoryRead = $filesystem->getDirectoryRead(DirectoryList::MODULES_DIR);
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($filename, $scope)
    {
        $iterator = $this->iteratorFactory->create(
            $this->directoryRead,
            $this->directoryRead->search('/*/*/etc/' . $filename)
        );
        return $iterator;
    }
}
