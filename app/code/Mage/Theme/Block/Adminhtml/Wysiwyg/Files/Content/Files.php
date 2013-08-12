<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files files block
 *
 * @method Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Files setStorage(Mage_Theme_Model_Wysiwyg_Storage $storage)
 * @method Mage_Theme_Model_Wysiwyg_Storage getStorage
 */
class Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Files extends Magento_Backend_Block_Template
{
    /**
     * Files list
     *
     * @var null|array
     */
    protected $_files;

    /**
     * Get files
     *
     * @return array
     */
    public function getFiles()
    {
        if (null === $this->_files && $this->getStorage()) {
            $this->_files = $this->getStorage()->getFilesCollection();
        }

        return $this->_files;
    }

    /**
     * Get files count
     *
     * @return int
     */
    public function getFilesCount()
    {
        return count($this->getFiles());
    }
}
