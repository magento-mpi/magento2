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
 * Builds path for files deployed into public directory in advance
 */
class Mage_Core_Model_View_DeployedFilesManager
{
    /**
     * @var Mage_Core_Model_View_Service
     */
    protected $_viewService;

    /**
     * Deployed view files manager
     *
     * @param Mage_Core_Model_View_Service $viewService
     */
    public function __construct(Mage_Core_Model_View_Service $viewService)
    {
        $this->_viewService = $viewService;
    }

    /**
     * Get deployed file path
     *
     * @param string $filePath
     * @param array $params
     * @return string
     */
    public function getDeployedFilePath($filePath, $params)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $params['themeModel'];
        $themePath = $themeModel->getThemePath();
        while (empty($themePath) && $themeModel) {
            $themePath = $themeModel->getThemePath();
            $themeModel = $themeModel->getParentTheme();
        }
        $subPath = self::buildDeployedFilePath(
            $params['area'], $themePath, $params['locale'], $filePath, $params['module']
        );
        $deployedFilePath = $this->_viewService->getPublicDir() . DIRECTORY_SEPARATOR . $subPath;

        return $deployedFilePath;
    }

    /**
     * Build a relative path to a static view file, if published with duplication.
     *
     * Just concatenates all context arguments.
     * Note: despite $locale is specified, it is currently ignored.
     *
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public static function buildDeployedFilePath($area, $themePath, $locale, $file, $module = null)
    {
        return $area . DIRECTORY_SEPARATOR . $themePath . DIRECTORY_SEPARATOR
            . ($module ? $module . DIRECTORY_SEPARATOR : '') . $file;
    }
}
