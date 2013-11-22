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
    protected $_localeDirectory;

    /**
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(\Magento\Filesystem $filesystem)
    {
        $this->_localeDirectory = $filesystem->getDirectoryRead(\Magento\Filesystem::LOCALE);
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        // Create pattern similar to */config.xml
        $fileList = $this->_localeDirectory->search('*/' . $filename);
        return $fileList;
    }
}
