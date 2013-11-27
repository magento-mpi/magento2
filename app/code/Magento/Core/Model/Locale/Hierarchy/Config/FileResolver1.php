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
     * @var \Magento\App\Dir
     */
    protected $_applicationDirs;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(\Magento\Filesystem $filesystem)
    {
        $this->directoryRead = $filesystem->getDirectoryRead(\Magento\Filesystem::APP);
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        return $this->directoryRead->search('#.*?/' . $filename . '$#');
    }
}
