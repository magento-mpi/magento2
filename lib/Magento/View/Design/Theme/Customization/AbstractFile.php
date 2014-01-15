<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Customization;

/**
 * Theme file service abstract class
 */
abstract class AbstractFile
    implements \Magento\View\Design\Theme\Customization\FileInterface,
               \Magento\View\Design\Theme\Customization\FileAssetInterface
{
    /**
     * @var \Magento\View\Design\Theme\Customization\Path
     */
    protected $_customizationPath;

    /**
     * @var \Magento\View\Design\Theme\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\View\Design\Theme\Customization\Path $customizationPath
     * @param \Magento\View\Design\Theme\FileFactory $fileFactory
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\View\Design\Theme\Customization\Path $customizationPath,
        \Magento\View\Design\Theme\FileFactory $fileFactory,
        \Magento\Filesystem $filesystem
    ) {
        $this->_customizationPath = $customizationPath;
        $this->_fileFactory = $fileFactory;
        $this->_filesystem = $filesystem;
    }

    /**
     * Create class instance with specified parameters
     *
     * @return \Magento\View\Design\Theme\FileInterface
     */
    public function create()
    {
        $file = $this->_fileFactory->create();
        $file->setCustomizationService($this);
        return $file;
    }

    /**
     * Returns customization full path
     *
     * @param \Magento\View\Design\Theme\FileInterface $file
     * @return string
     */
    public function getFullPath(\Magento\View\Design\Theme\FileInterface $file)
    {
        $customizationPath = $this->_customizationPath->getCustomizationPath($file->getTheme());
        return $customizationPath . '/' . $file->getData('file_path');
    }

    /**
     * @param \Magento\View\Design\Theme\FileInterface $file
     * @return $this
     */
    public function prepareFile(\Magento\View\Design\Theme\FileInterface $file)
    {
        $file->setData('file_type', $this->getType());
        if (!$file->getId()) {
            $this->_prepareFileName($file);
            $this->_prepareFilePath($file);
            $this->_prepareSortOrder($file);
        }
        return $this;
    }

    /**
     * Creates or updates file of customization in filesystem
     *
     * @param \Magento\View\Design\Theme\FileInterface $file
     * @return $this
     */
    public function save(\Magento\View\Design\Theme\FileInterface $file)
    {
        $this->_saveFileContent($this->getFullPath($file), $file->getContent());
        return $this;
    }

    /**
     * Deletes file of customization in filesystem
     *
     * @param \Magento\View\Design\Theme\FileInterface $file
     * @return $this
     */
    public function delete(\Magento\View\Design\Theme\FileInterface $file)
    {
        $this->_deleteFileContent($this->getFullPath($file));
        return $this;
    }

    /**
     * Prepares filename of file
     *
     * @param \Magento\View\Design\Theme\FileInterface $file
     */
    protected function _prepareFileName(\Magento\View\Design\Theme\FileInterface $file)
    {
        $customFiles = $file->getTheme()->getCustomization()->getFilesByType($this->getType());

        $fileName = $file->getFileName();
        $fileInfo = pathinfo($fileName);
        $fileIndex = 0;
        /** @var $customFile \Magento\View\Design\Theme\FileInterface */
        foreach ($customFiles as $customFile) {
            if ($fileName === $customFile->getFileName()) {
                $fileName = sprintf('%s_%d.%s', $fileInfo['filename'], ++$fileIndex, $fileInfo['extension']);
            }
        }
        $file->setFileName($fileName);
    }

    /**
     * Prepares relative path of file
     *
     * @param \Magento\View\Design\Theme\FileInterface $file
     */
    protected function _prepareFilePath(\Magento\View\Design\Theme\FileInterface $file)
    {
        $file->setData('file_path', $this->getContentType() . '/' . $file->getFileName());
    }

    /**
     * Prepares sort order of custom file
     *
     * @param \Magento\View\Design\Theme\FileInterface $file
     */
    protected function _prepareSortOrder(\Magento\View\Design\Theme\FileInterface $file)
    {
        $customFiles = $file->getTheme()->getCustomization()->getFilesByType($this->getType());
        $sortOrderIndex = (int)$file->getData('sort_order');
        foreach ($customFiles as $customFile) {
            $prevSortOrderIndex = $customFile->getData('sort_order');
            if ($prevSortOrderIndex > $sortOrderIndex) {
                $sortOrderIndex = $prevSortOrderIndex;
            }
        }
        $file->setData('sort_order', ++$sortOrderIndex);
    }

    /**
     * Creates or updates file of customization in filesystem
     *
     * @param string $filePath
     * @param string $content
     */
    protected function _saveFileContent($filePath, $content)
    {
        $this->getDirectoryWrite()->delete($filePath);
        if (!empty($content)) {
            $this->getDirectoryWrite()->writeFile($this->getDirectoryWrite()->getRelativePath($filePath), $content);
        }
    }

    /**
     * Deletes file of customization in filesystem
     *
     * @param string $filePath
     */
    protected function _deleteFileContent($filePath)
    {
        $filePath = $this->getDirectoryWrite()->getRelativePath($filePath);
        if ($this->getDirectoryWrite()->touch($filePath)) {
            $this->getDirectoryWrite()->delete($filePath);
        }
    }

    /**
     * Returns filesystem directory instance for write operations
     *
     * @return \Magento\Filesystem\Directory\WriteInterface
     */
    protected function getDirectoryWrite()
    {
        return $this->_filesystem->getDirectoryWrite(\Magento\Filesystem::ROOT);
    }
}
