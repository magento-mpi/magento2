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
     * @var \Magento\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->directoryRead = $filesystem->getDirectoryRead(\Magento\Filesystem::APP);
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        $result = array();
        if ($this->directoryRead->isExist('locale')) {
            $result = $this->iteratorFactory->create(
                $this->directoryRead,
                $this->directoryRead->search('/locale/*/' . $filename)
            );
        }
        return $result;
    }
}
