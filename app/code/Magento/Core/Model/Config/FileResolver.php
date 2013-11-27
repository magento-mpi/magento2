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
     * Filesystem instance
     *
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\Module\Dir\Reader $moduleReader
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Module\Dir\Reader $moduleReader,
        \Magento\Filesystem $filesystem
    ) {
        $this->_moduleReader = $moduleReader;
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        switch ($scope) {
            case 'primary':
                $appConfigDir = $this->filesystem->getPath(\Magento\Filesystem::CONFIG);
                // Create pattern similar to app/etc/{*config.xml,*/*config.xml}
                $filePattern = $appConfigDir . '/'
                    . '{*' . $filename . ',*' . '/' . '*' . $filename . '}';
                $fileList = glob($filePattern, GLOB_BRACE);
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
