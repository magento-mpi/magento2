<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class FileResolver implements \Magento\Config\FileResolverInterface
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
    protected $_configDirectory;

    /**
     * @param \Magento\Module\Dir\Reader $moduleReader
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Module\Dir\Reader $moduleReader,
        \Magento\Filesystem $filesystem
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_configDirectory = $filesystem->getDirectoryRead(\Magento\Filesystem::CONFIG);
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        switch ($scope) {
            case 'primary':
                // Create pattern similar to (*config.xml|*/*config.xml)
                $filePattern = '(*' . $filename . '|*/*' . $filename . ')';
                $fileList = $this->_configDirectory->search($filePattern);
                break;
            case 'global':
                $fileList = $this->_moduleReader->getConfigurationFiles($filename);
                break;
            default:
                $fileList = $this->_moduleReader->getConfigurationFiles($scope . '/' . $filename);
                break;
        }
        return $fileList;
    }
}
