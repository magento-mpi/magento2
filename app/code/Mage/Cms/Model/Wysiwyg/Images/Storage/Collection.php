<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wysiwyg Images storage collection
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Wysiwyg_Images_Storage_Collection extends Magento_Data_Collection_Filesystem
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Constructor
     *
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(Magento_Filesystem $filesystem)
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
