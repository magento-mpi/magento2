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

class FileResolver implements \Magento\Config\FileResolverInterface
{
    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * @var \Magento\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Email\Model\Template\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Email\Model\Template\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->directoryRead = $filesystem->getDirectoryRead(\Magento\Filesystem::MODULES);
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * @inheritdoc
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
