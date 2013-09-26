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
 * Theme file service abstract class
 */
namespace Magento\Core\Model\Theme\Customization;

abstract class AbstractFile
    implements \Magento\Core\Model\Theme\Customization\FileInterface,
               \Magento\Core\Model\Theme\Customization\FileAssetInterface
{
    /**
     * @var \Magento\Core\Model\Theme\Customization\Path
     */
    protected $_customizationPath;

    /**
     * @var \Magento\Core\Model\Theme\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\Core\Model\Theme\Customization\Path $customizationPath
     * @param \Magento\Core\Model\Theme\FileFactory $fileFactory
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Model\Theme\Customization\Path $customizationPath,
        \Magento\Core\Model\Theme\FileFactory $fileFactory,
        \Magento\Filesystem $filesystem
    ) {
        $this->_customizationPath = $customizationPath;
        $this->_fileFactory = $fileFactory;
        $this->_filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $file = $this->_fileFactory->create();
        $file->setCustomizationService($this);
        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullPath(\Magento\Core\Model\Theme\FileInterface $file)
    {
        $customizationPath = $this->_customizationPath->getCustomizationPath($file->getTheme());
        return $customizationPath . DIRECTORY_SEPARATOR . $file->getData('file_path');
    }

    /**
     * {@inheritdoc}
     */
    public function prepareFile(\Magento\Core\Model\Theme\FileInterface $file)
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
     * {@inheritdoc}
     */
    public function save(\Magento\Core\Model\Theme\FileInterface $file)
    {
        $this->_saveFileContent($this->getFullPath($file), $file->getContent());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Core\Model\Theme\FileInterface $file)
    {
        $this->_deleteFileContent($this->getFullPath($file));
        return $this;
    }

    /**
     * Prepares filename of file
     *
     * @param \Magento\Core\Model\Theme\FileInterface $file
     */
    protected function _prepareFileName(\Magento\Core\Model\Theme\FileInterface $file)
    {
        $customFiles = $file->getTheme()->getCustomization()->getFilesByType($this->getType());

        $fileName = $file->getFileName();
        $fileInfo = pathinfo($fileName);
        $fileIndex = 0;
        /** @var $customFile \Magento\Core\Model\Theme\FileInterface */
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
     * @param \Magento\Core\Model\Theme\FileInterface $file
     */
    protected function _prepareFilePath(\Magento\Core\Model\Theme\FileInterface $file)
    {
        $file->setData('file_path', $this->getContentType() . '/' . $file->getFileName());
    }

    /**
     * Prepares sort order of custom file
     *
     * @param \Magento\Core\Model\Theme\FileInterface $file
     */
    protected function _prepareSortOrder(\Magento\Core\Model\Theme\FileInterface $file)
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
        $this->_filesystem->delete($filePath);
        if (!empty($content)) {
            $this->_filesystem->setIsAllowCreateDirectories(true)->write($filePath, $content);
        }
    }

    /**
     * Deletes file of customization in filesystem
     *
     * @param string $filePath
     */
    protected function _deleteFileContent($filePath)
    {
        if ($this->_filesystem->has($filePath)) {
            $this->_filesystem->delete($filePath);
        }
    }
}
