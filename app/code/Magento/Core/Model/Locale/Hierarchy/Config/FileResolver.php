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

use \Magento\Filesystem;

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
        // @TODO 'i18n' directory file resolving rules are not defined
        $this->_localeDirectory = $filesystem->getDirectoryRead(\Magento\Filesystem::LOCALE);
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        $fileList = array();
        if ($this->_localeDirectory->isExist()) {
            // Create pattern similar to */config.xml
            $path = '#.*?\/' . preg_quote($filename) . '#';
            $fileList = $this->_localeDirectory->search($path);
        }
        $fileListAbsolute = array();
        foreach ($fileList as $file) {
            $fileListAbsolute[] = $this->_localeDirectory->getAbsolutePath($file);
        }
        return $fileListAbsolute;
    }
}
