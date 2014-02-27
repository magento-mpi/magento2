<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Locale\Hierarchy\Config;

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
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->directoryRead = $filesystem->getDirectoryRead(\Magento\App\Filesystem::APP_DIR);
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
