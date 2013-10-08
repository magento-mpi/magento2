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
     * @param \Magento\App\Dir $applicationDirs
     */
    public function __construct(\Magento\App\Dir $applicationDirs)
    {
        $this->_applicationDirs = $applicationDirs;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        $appLocaleDir = $this->_applicationDirs->getDir(\Magento\App\Dir::LOCALE);
        // Create pattern similar to app/locale/*/config.xml
        $filePattern = $appLocaleDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $filename;
        $fileList = glob($filePattern, GLOB_BRACE);
        return $fileList;
    }
}
