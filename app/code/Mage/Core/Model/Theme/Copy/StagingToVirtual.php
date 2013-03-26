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
 *
 * @method Mage_Core_Model_Theme_Copy_StagingToVirtual _copyLayoutUpdates($theme, $virtualTheme)
 */
class Mage_Core_Model_Theme_Copy_StagingToVirtual extends Mage_Core_Model_Theme_Copy_Abstract
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_Core_Model_Layout_Link $layoutLink
     * @param Mage_Core_Model_Layout_Update $layoutUpdate
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Design_Package $design
     * @param Mage_Core_Model_App $app
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_Core_Model_Layout_Link $layoutLink,
        Mage_Core_Model_Layout_Update $layoutUpdate,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Design_Package $design,
        Mage_Core_Model_App $app,
        array $data = array()
    ) {
        $this->_filesystem = $filesystem;
        $this->_design = $design;
        $this->_app = $app;
        parent::__construct($themeFactory, $layoutLink, $layoutUpdate, $data);
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
        $this->_copyLayoutUpdates($theme, $virtualTheme)->_copyAllThemeFiles($theme, $virtualTheme);
        $this->_design->dropPublicationCache(array(
            'area' => Mage_Core_Model_Design_Package::DEFAULT_AREA, 'themeModel' => $virtualTheme
        ));
        $this->_app->cleanCache(array('layout', Mage_Core_Model_Layout_Merge::LAYOUT_GENERAL_CACHE_TAG));
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
