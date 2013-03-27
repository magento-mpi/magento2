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
 *  Model to create 'virtual' copy of 'staging' theme
 */
class Mage_Core_Model_Theme_Copy_StagingToVirtual implements Mage_Core_Model_Theme_Copy_Interface
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(Magento_Filesystem $filesystem)
    {
        $this->_filesystem = $filesystem;
    }

    /**
     * Copy 'staging' theme related data to current 'virtual' theme
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme
     * @throws Mage_Core_Exception
     */
    public function copy(Mage_Core_Model_Theme $theme)
    {
        if ($theme->getType() != Mage_Core_Model_Theme::TYPE_STAGING) {
            throw new Mage_Core_Exception('Invalid theme type');
        }
        $virtualTheme = $theme->getParentTheme();
        $this->_copyAllThemeFiles($theme, $virtualTheme);
        return $virtualTheme;
    }

    /**
     * Copies all modified theme files
     *
     * @param $stagingTheme
     * @param $virtualTheme
     * @return Mage_Core_Model_Theme_Copy_StagingToVirtual
     */
    protected function _copyAllThemeFiles(Mage_Core_Model_Theme $stagingTheme, Mage_Core_Model_Theme $virtualTheme)
    {
        $sourcePath = $stagingTheme->getCustomizationPath();
        $targetPath = $virtualTheme->getCustomizationPath();
        if ($stagingTheme && $targetPath && $this->_filesystem->isDirectory($sourcePath)) {
            $this->_copyRecursively($sourcePath, $sourcePath, $targetPath);
        }
        return $this;
    }

    /**
     * Copies all modified theme files recursively
     *
     * @param string $baseDir
     * @param string $sourceDir
     * @param string $targetDir
     * @return Mage_Core_Model_Theme_Copy_StagingToVirtual
     */
    protected function _copyRecursively($baseDir, $sourceDir, $targetDir)
    {
        $this->_filesystem->setIsAllowCreateDirectories(true);
        foreach ($this->_filesystem->searchKeys($sourceDir, '*') as $path) {
            if ($this->_filesystem->isDirectory($path)) {
                $this->_copyRecursively($baseDir, $path, $targetDir);
            } else {
                $filePath = trim(substr($path, strlen($baseDir)), DIRECTORY_SEPARATOR);
                $this->_filesystem->copy($path, $targetDir . DIRECTORY_SEPARATOR . $filePath, $baseDir, $targetDir);
            }
        }
        return $this;
    }
}
