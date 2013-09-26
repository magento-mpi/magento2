<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Model\Config;

class FileResolver implements \Magento\Config\FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_moduleReader;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_applicationDirs;

    /**
     * @param \Magento\Core\Model\Config\Modules\Reader $moduleReader
     * @param \Magento\Core\Model\Dir $applicationDirs
     */
    public function __construct(
        \Magento\Core\Model\Config\Modules\Reader $moduleReader,
        \Magento\Core\Model\Dir $applicationDirs
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_applicationDirs = $applicationDirs;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        $fileList = array();
        switch ($scope) {
            case 'global':
                $fileList = $this->_moduleReader->getConfigurationFiles($filename);
                break;
            case 'design':
                $fileList = glob($this->_applicationDirs->getDir(\Magento\Core\Model\Dir::THEMES)
                . "/*/*/etc/$filename", GLOB_NOSORT | GLOB_BRACE);
                break;
            default:
                break;
        }
        return $fileList;
    }
}
