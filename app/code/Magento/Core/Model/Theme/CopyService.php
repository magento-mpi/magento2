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
 * Service of copying customizations from one theme to another
 */
class Magento_Core_Model_Theme_CopyService
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Core_Model_Theme_FileFactory
     */
    protected $_fileFactory;

    /**
     * @var Magento_Core_Model_Layout_Link
     */
    protected $_link;

    /**
     * @var Magento_Core_Model_Layout_UpdateFactory
     */
    protected $_updateFactory;

    /**
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Magento_Core_Model_Theme_Customization_Path
     */
    protected $_customizationPath;

    /**
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Theme_FileFactory $fileFactory
     * @param Magento_Core_Model_Layout_Link $link
     * @param Magento_Core_Model_Layout_UpdateFactory $updateFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Theme_Customization_Path $customization
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Theme_FileFactory $fileFactory,
        Magento_Core_Model_Layout_Link $link,
        Magento_Core_Model_Layout_UpdateFactory $updateFactory,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Theme_Customization_Path $customization
    ) {
        $this->_filesystem = $filesystem;
        $this->_fileFactory = $fileFactory;
        $this->_link = $link;
        $this->_updateFactory = $updateFactory;
        $this->_eventManager = $eventManager;
        $this->_customizationPath = $customization;
    }

    /**
     * Copy customizations from one theme to another
     *
     * @param Magento_Core_Model_Theme $source
     * @param Magento_Core_Model_Theme $target
     */
    public function copy(Magento_Core_Model_Theme $source, Magento_Core_Model_Theme $target)
    {
        $this->_copyDatabaseCustomization($source, $target);
        $this->_copyLayoutCustomization($source, $target);
        $this->_copyFilesystemCustomization($source, $target);
        $this->_eventManager->dispatch('theme_copy_after', array('sourceTheme' => $source, 'targetTheme' => $target));
    }

    /**
     * Copy customizations stored in a database from one theme to another, overriding existing data
     *
     * @param Magento_Core_Model_Theme $source
     * @param Magento_Core_Model_Theme $target
     */
    protected function _copyDatabaseCustomization(Magento_Core_Model_Theme $source, Magento_Core_Model_Theme $target)
    {
        /** @var $themeFile Magento_Core_Model_Theme_File */
        foreach ($target->getCustomization()->getFiles() as $themeFile) {
            $themeFile->delete();
        }
        /** @var $newFile Magento_Core_Model_Theme_File */
        foreach ($source->getCustomization()->getFiles() as $themeFile) {
            /** @var $newThemeFile Magento_Core_Model_Theme_File */
            $newThemeFile = $this->_fileFactory->create();
            $newThemeFile->setData(array(
                'theme_id'      => $target->getId(),
                'file_path'     => $themeFile->getFilePath(),
                'file_type'     => $themeFile->getFileType(),
                'content'       => $themeFile->getContent(),
                'sort_order'    => $themeFile->getData('sort_order'),
            ));
            $newThemeFile->save();
        }
    }

    /**
     * Add layout links to general layout updates for themes
     *
     * @param Magento_Core_Model_Theme $source
     * @param Magento_Core_Model_Theme $target
     */
    protected function _copyLayoutCustomization(Magento_Core_Model_Theme $source, Magento_Core_Model_Theme $target)
    {
        $update = $this->_updateFactory->create();
        /** @var $targetUpdates Magento_Core_Model_Resource_Layout_Update_Collection */
        $targetUpdates = $update->getCollection();
        $targetUpdates->addThemeFilter($target->getId());
        $targetUpdates->delete();

        /** @var $sourceCollection Magento_Core_Model_Resource_Layout_Link_Collection */
        $sourceCollection = $this->_link->getCollection();
        $sourceCollection->addThemeFilter($source->getId());
        /** @var $layoutLink Magento_Core_Model_Layout_Link */
        foreach ($sourceCollection as $layoutLink) {
            /** @var $update Magento_Core_Model_Layout_Update */
            $update = $this->_updateFactory->create();
            $update->load($layoutLink->getLayoutUpdateId());
            if ($update->getId()) {
                $update->setId(null);
                $update->save();
                $layoutLink->setThemeId($target->getId());
                $layoutLink->setLayoutUpdateId($update->getId());
                $layoutLink->setId(null);
                $layoutLink->save();
            }
        }
    }

    /**
     * Copy customizations stored in a file system from one theme to another, overriding existing data
     *
     * @param Magento_Core_Model_Theme $source
     * @param Magento_Core_Model_Theme $target
     */
    protected function _copyFilesystemCustomization(Magento_Core_Model_Theme $source, Magento_Core_Model_Theme $target)
    {
        $sourcePath = $this->_customizationPath->getCustomizationPath($source);
        $targetPath = $this->_customizationPath->getCustomizationPath($target);

        if (!$sourcePath || !$targetPath) {
            return;
        }

        $this->_deleteFilesRecursively($targetPath);

        if ($this->_filesystem->isDirectory($sourcePath)) {
            $this->_copyFilesRecursively($sourcePath, $sourcePath, $targetPath);
        }
    }

    /**
     * Copies all files in a directory recursively
     *
     * @param string $baseDir
     * @param string $sourceDir
     * @param string $targetDir
     */
    protected function _copyFilesRecursively($baseDir, $sourceDir, $targetDir)
    {
        $this->_filesystem->setIsAllowCreateDirectories(true);
        foreach ($this->_filesystem->searchKeys($sourceDir, '*') as $path) {
            if ($this->_filesystem->isDirectory($path)) {
                $this->_copyFilesRecursively($baseDir, $path, $targetDir);
            } else {
                $filePath = substr($path, strlen($baseDir) + 1);
                $this->_filesystem->copy($path, $targetDir . '/' . $filePath, $baseDir, $targetDir);
            }
        }
    }

    /**
     * Delete all files in a directory recursively
     *
     * @param string $targetDir
     */
    protected function _deleteFilesRecursively($targetDir)
    {
        foreach ($this->_filesystem->searchKeys($targetDir, '*') as $path) {
            $this->_filesystem->delete($path);
        }
    }
}
