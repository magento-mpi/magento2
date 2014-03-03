<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme;

/**
 * Theme customizations manager
 */
class Customization implements CustomizationInterface
{
    /**
     * File provider
     *
     * @var \Magento\View\Design\Theme\FileProviderInterface
     */
    protected $fileProvider;

    /**
     * Theme customization path
     *
     * @var \Magento\View\Design\Theme\Customization\Path
     */
    protected $customizationPath;

    /**
     * Theme
     *
     * @var \Magento\View\Design\ThemeInterface
     */
    protected $theme;

    /**
     * Theme files
     *
     * @var \Magento\View\Design\Theme\FileInterface[]
     */
    protected $themeFiles;

    /**
     * Theme files by type
     *
     * @var \Magento\View\Design\Theme\FileInterface[]
     */
    protected $themeFilesByType = array();

    /**
     * Constructor
     *
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
     * Returns customization absolute path
     *
     * @return null|string
     */
    public function getCustomizationPath()
    {
        return $this->customizationPath->getCustomizationPath($this->theme);
    }

    /**
     * Get directory where themes files are stored
     *
     * @return null|string
     */
    public function getThemeFilesPath()
    {
        return $this->theme->isPhysical()
            ? $this->customizationPath->getThemeFilesPath($this->theme)
            : $this->customizationPath->getCustomizationPath($this->theme);
    }

    /**
     * Get path to custom view configuration file
     *
     * @return null|string
     */
    public function getCustomViewConfigPath()
    {
        return $this->customizationPath->getCustomViewConfigPath($this->theme);
    }

    /**
     * Reorder
     *
     * @param string $type
     * @param array $sequence
     * @return $this|CustomizationInterface
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
