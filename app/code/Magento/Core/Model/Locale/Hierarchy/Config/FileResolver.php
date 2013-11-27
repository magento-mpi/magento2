<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Locale\Hierarchy\Config;

class FileResolver implements \Magento\Config\FileResolverInterface
{
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
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Locale\Hierarchy\Config\FileIteratorFactory $iteratorFactory
    ){
        $this->directoryRead    = $filesystem->getDirectoryRead(\Magento\Filesystem::APP);
        $this->iteratorFactory  = $iteratorFactory;
        $this->filesystem       = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        return $this->iteratorFactory->create(array(
            'paths' => $this->directoryRead->search('#.*?/' . $filename . '$#'),
            'filesystem' => $this->filesystem
        ));
    }
}
