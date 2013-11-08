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
namespace Magento\View\Design\Theme;

class Customization implements CustomizationInterface
{
    /**
     * @var \Magento\View\Design\Theme\FileProviderInterface
     */
    protected $fileProvider;

    /**
     * @var \Magento\View\Design\Theme\Customization\Path
     */
    protected $customizationPath;

    /**
     * @var \Magento\View\Design\ThemeInterface
     */
    protected $theme;

    /**
     * @var \Magento\Core\Model\Resource\Theme\File\Collection
     */
    protected $themeFiles;

    /**
     * @var \Magento\Core\Model\Resource\Theme\File\Collection[]
     */
    protected $themeFilesByType = array();

    /**
     * @param \Magento\View\Design\Theme\FileProviderInterface $fileProvider
     * @param \Magento\View\Design\Theme\Customization\Path $customizationPath
     * @param \Magento\View\Design\ThemeInterface $theme
     */
    public function __construct(
        \Magento\View\Design\Theme\FileProviderInterface $fileProvider,
        \Magento\View\Design\Theme\Customization\Path $customizationPath,
        \Magento\View\Design\ThemeInterface $theme = null
    ) {
        $this->fileProvider = $fileProvider;
        $this->customizationPath = $customizationPath;
        $this->theme = $theme;
    }

    /**
     * Retrieve list of files which belong to a theme
     *
     * @return \Magento\View\Design\Theme\FileInterface[]
     */
    public function getFiles()
    {
        if (!$this->themeFiles) {
            $this->themeFiles = $this->fileProvider->getItems($this->theme);
        }
        return $this->themeFiles;
    }

    /**
     * Retrieve list of files which belong to a theme only by type
     *
     * @param string $type
     * @return \Magento\View\Design\Theme\FileInterface[]
     */
    public function getFilesByType($type)
    {
        if (!isset($this->themeFilesByType[$type])) {
            $this->themeFilesByType[$type] = $this->fileProvider->getItems(
                $this->theme, array('file_type' => $type)
            );
        }
        return $this->themeFilesByType[$type];
    }

    /**
     * Get short file information
     *
     * @param \Magento\View\Design\Theme\FileInterface[] $files
     * @return array
     */
    public function generateFileInfo(array $files)
    {
        $filesInfo = array();
        /** @var $file \Magento\View\Design\Theme\FileInterface */
        foreach ($files as $file) {
            if ($file instanceof \Magento\View\Design\Theme\FileInterface) {
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
        return $this->customizationPath->getCustomizationPath($this->theme);
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeFilesPath()
    {
        return $this->theme->isPhysical()
            ? $this->customizationPath->getThemeFilesPath($this->theme)
            : $this->customizationPath->getCustomizationPath($this->theme);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomViewConfigPath()
    {
        return $this->customizationPath->getCustomViewConfigPath($this->theme);
    }

    /**
     * {@inheritdoc}
     */
    public function reorder($type, array $sequence)
    {
        $sortOrderSequence = array_flip(array_values($sequence));
        /** @var $file \Magento\View\Design\Theme\FileInterface */
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
        /** @var $file \Magento\View\Design\Theme\FileInterface */
        foreach ($this->getFiles() as $file) {
            if (in_array($file->getId(), $fileIds)) {
                $file->delete();
            }
        }
        return $this;
    }
}
