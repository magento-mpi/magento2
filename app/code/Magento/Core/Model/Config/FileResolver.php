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
    protected $moduleReader;

    /**
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
        $this->moduleReader = $moduleReader;
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        switch ($scope) {
            case 'primary':
                $appConfigDir = $this->filesystem->getDirectoryRead(\Magento\Filesystem::CONFIG);
                // Create pattern similar to app/etc/{*config.xml,*/*config.xml}
                $fileListRelative = $appConfigDir->search('~' . '\S+' . preg_quote($filename) . '~');
                $fileList = array();
                foreach ($fileListRelative as $file) {
                    $fileList[] = $appConfigDir->getAbsolutePath($file);
                }
                break;
            case 'global':
                $fileList = $this->moduleReader->getConfigurationFiles($filename);
                break;
            default:
                $fileList = $this->moduleReader->getConfigurationFiles($scope . '/' . $filename);
                break;
        }
        return $fileList;
    }
}
