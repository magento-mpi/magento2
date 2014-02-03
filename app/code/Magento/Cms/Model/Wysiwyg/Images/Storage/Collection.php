<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wysiwyg Images storage collection
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Model\Wysiwyg\Images\Storage;

class Collection extends \Magento\Data\Collection\Filesystem
{
    /**
     * @var \Magento\App\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\App\Filesystem $filesystem
     */
    public function __construct(\Magento\Core\Model\EntityFactory $entityFactory, \Magento\App\Filesystem $filesystem)
    {
        $this->_filesystem = $filesystem;
        parent::__construct($entityFactory);
    }

    protected function _generateRow($filename)
    {
        $filename = preg_replace('~[/\\\]+~', '/', $filename);
        $path = $this->_filesystem->getDirectoryWrite(\Magento\App\Filesystem::MEDIA_DIR);
        return array(
            'filename' => $filename,
            'basename' => basename($filename),
            'mtime'    => $path->stat($path->getRelativePath($filename))['mtime']
        );
    }
}
