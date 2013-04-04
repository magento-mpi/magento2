<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme files abstract class
 */
abstract class Mage_Core_Model_Theme_Customization_Files_FilesAbstract extends Varien_Object
    implements Mage_Core_Model_Theme_Customization_CustomizationInterface
{
    /**
     * @var Mage_Core_Model_Theme_File
     */
    protected $_themeFiles;

    /**
     * Data for save
     *
     * @var mixed
     */
    protected $_dataForSave;

    /**
     * @param Mage_Core_Model_Theme_File $themeFiles
     */
    public function __construct(Mage_Core_Model_Theme_File $themeFiles)
    {
        $this->_themeFiles = $themeFiles;
    }

    /**
     * Setter for data for save
     *
     * @param mixed $data
     * @return Mage_Core_Model_Theme_Customization_Files_FilesAbstract
     */
    public function setDataForSave($data)
    {
        $this->_dataForSave = $data;
        return $this;
    }

    /**
     * Save data
     *
     * @param Mage_Core_Model_Theme_Customization_CustomizedInterface $theme
     * @return Mage_Core_Model_Theme_Customization_Files_FilesAbstract
     */
    public function saveData(Mage_Core_Model_Theme_Customization_CustomizedInterface $theme)
    {
        if (null !== $this->_dataForSave) {
            $this->_save($theme);
        }
        return $this;
    }

    /**
     * Save data
     *
     * @param Mage_Core_Model_Theme_Customization_CustomizedInterface $theme
     * @return Mage_Core_Model_Resource_Theme_File_Collection
     */
    public function getCollectionByTheme(Mage_Core_Model_Theme_Customization_CustomizedInterface $theme)
    {
        /** @var $filesCollection Mage_Core_Model_Resource_Theme_File_Collection */
        $filesCollection = $this->_themeFiles->getCollection()->addFilter('theme_id', $theme->getId())
            ->addFilter('file_type', $this->_getFileType());

        return $filesCollection;
    }

    /**
     * Return file type
     *
     * @return string
     */
    abstract protected function _getFileType();

    /**
     * Save data
     *
     * @param Mage_Core_Model_Theme $theme
     */
    abstract protected function _save($theme);
}
