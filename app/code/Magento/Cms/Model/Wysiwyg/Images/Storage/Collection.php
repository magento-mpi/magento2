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
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * Constructor
     *
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(\Magento\Filesystem $filesystem)
    {
        $this->_filesystem = $filesystem;
        parent::__construct();
    }

    protected function _generateRow($filename)
    {
        $filename = preg_replace('~[/\\\]+~', DIRECTORY_SEPARATOR, $filename);

        return array(
            'filename' => $filename,
            'basename' => basename($filename),
            'mtime'    => $this->_filesystem->getMTime($filename)
        );
    }
}
