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
        // @TODO 'i18n' directory file resolving rules are not defined
        $this->_localeDirectory = $filesystem->getDirectoryRead(\Magento\Filesystem::LOCALE);
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        if (!$this->_localeDirectory->isExist()) {
            $fileList = array();
        } else {
            // Create pattern similar to */config.xml
            $path = '#.*?\/' . preg_quote($filename) . '#';
            $fileList = $this->_localeDirectory->search($path);
        }
        return $fileList;
    }
}
