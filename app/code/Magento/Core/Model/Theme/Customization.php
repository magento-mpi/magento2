<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme customizations manager
 */
class Magento_Core_Model_Theme_Customization implements Magento_Core_Model_Theme_CustomizationInterface
{
    /**
     * @var Magento_Core_Model_Resource_Theme_File_CollectionFactory
     */
    protected $_fileFactory;

    /**
     * @var Magento_Core_Model_Theme_Customization_Path
     */
    protected $_customizationPath;

    /**
     * @var Magento_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Magento_Core_Model_Resource_Theme_File_Collection
     */
    protected $_themeFiles;

    /**
     * @var Magento_Core_Model_Resource_Theme_File_Collection[]
     */
    protected $_themeFilesByType = array();

    /**
     * @param Magento_Core_Model_Resource_Theme_File_CollectionFactory $fileFactory
     * @param Magento_Core_Model_Theme_Customization_Path $customizationPath
     * @param Magento_Core_Model_Theme $theme
     */
    public function __construct(
        Magento_Core_Model_Resource_Theme_File_CollectionFactory $fileFactory,
        Magento_Core_Model_Theme_Customization_Path $customizationPath,
        Magento_Core_Model_Theme $theme = null
    ) {
        $this->_fileFactory = $fileFactory;
        $this->_customizationPath = $customizationPath;
        $this->_theme = $theme;
    }

    /**
     * Retrieve list of files which belong to a theme
     *
     * @return Magento_Core_Model_Theme_FileInterface[]
     */
    public function getFiles()
    {
        if (!$this->_themeFiles) {
            $this->_themeFiles = $this->_fileFactory->create();
            $this->_themeFiles->addThemeFilter($this->_theme);
            $this->_themeFiles->setDefaultOrder();
        }
        return $this->_themeFiles->getItems();
    }

    /**
     * Retrieve list of files which belong to a theme only by type
     *
     * @param string $type
     * @return Magento_Core_Model_Theme_FileInterface[]
     */
    public function getFilesByType($type)
    {
        if (!isset($this->_themeFilesByType[$type])) {
            $themeFiles = $this->_fileFactory->create();
            $themeFiles->addThemeFilter($this->_theme);
            $themeFiles->addFieldToFilter('file_type', $type);
            $themeFiles->setDefaultOrder();
            $this->_themeFilesByType[$type] = $themeFiles;
        }
        return $this->_themeFilesByType[$type]->getItems();
    }

    /**
     * Get short file information
     *
     * @param Magento_Core_Model_Theme_FileInterface[] $files
     * @return array
     */
    public function generateFileInfo(array $files)
    {
        $filesInfo = array();
        /** @var $file Magento_Core_Model_Theme_FileInterface */
        foreach ($files as $file) {
            if ($file instanceof Magento_Core_Model_Theme_FileInterface) {
                $filesInfo[] = $file->getFileInfo();
            }
        }
        return $filesInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizationPath()
    {
        return $this->_customizationPath->getCustomizationPath($this->_theme);
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeFilesPath()
    {
        return $this->_theme->isPhysical()
            ? $this->_customizationPath->getThemeFilesPath($this->_theme)
            : $this->_customizationPath->getCustomizationPath($this->_theme);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomViewConfigPath()
    {
        return $this->_customizationPath->getCustomViewConfigPath($this->_theme);
    }

    /**
     * Reorder files positions
     *
     * @param string $type
     * @param array $sequence
     * @return $this
     */
    public function reorder($type, array $sequence)
    {
        $sortOrderSequence = array_flip(array_values($sequence));
        /** @var $file Magento_Core_Model_Theme_FileInterface */
        foreach ($this->getFilesByType($type) as $file) {
            if (isset($sortOrderSequence[$file->getId()])) {
                $prevSortOrder = $file->getData('sort_order');
                $currentSortOrder = $sortOrderSequence[$file->getId()];
                if ($prevSortOrder !== $currentSortOrder) {
                    $file->setData('sort_order', $currentSortOrder);
                    $file->save();
                }
            }
        }
        return $this;
    }

    /**
     * Remove custom files by ids
     *
     * @param array $fileIds
     * @return $this
     */
    public function delete(array $fileIds)
    {
        /** @var $file Magento_Core_Model_Theme_FileInterface */
        foreach ($this->getFiles() as $file) {
            if (in_array($file->getId(), $fileIds)) {
                $file->delete();
            }
        }
        return $this;
    }
}
