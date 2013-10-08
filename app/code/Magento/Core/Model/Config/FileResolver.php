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
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_moduleReader;

    /**
     * @var \Magento\App\Dir
     */
    protected $_applicationDirs;

    /**
     * @param \Magento\Core\Model\Config\Modules\Reader $moduleReader
     * @param \Magento\App\Dir $applicationDirs
     */
    public function __construct(
        \Magento\Core\Model\Config\Modules\Reader $moduleReader,
        \Magento\App\Dir $applicationDirs
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_applicationDirs = $applicationDirs;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        switch ($scope) {
            case 'primary':
                $appConfigDir = $this->_applicationDirs->getDir(\Magento\App\Dir::CONFIG);
                // Create pattern similar to app/etc/{*config.xml,*/*config.xml}
                $filePattern = $appConfigDir . DIRECTORY_SEPARATOR
                    . '{*' . $filename . ',*' . DIRECTORY_SEPARATOR . '*' . $filename . '}';
                $fileList = glob($filePattern, GLOB_BRACE);
                break;
            case 'global':
                $fileList = $this->_moduleReader->getConfigurationFiles($filename);
                break;
            default:
                $fileList = $this->_moduleReader->getConfigurationFiles($scope . DIRECTORY_SEPARATOR . $filename);
                break;
        }
        return $fileList;
    }
}
