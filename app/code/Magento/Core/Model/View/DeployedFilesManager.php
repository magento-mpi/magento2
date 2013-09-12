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
 * Builds path for files deployed into public directory in advance
 */
class Magento_Core_Model_View_DeployedFilesManager implements Magento_Core_Model_View_PublicFilesManagerInterface
{
    /**
     * @var Magento_Core_Model_View_Service
     */
    protected $_viewService;

    /**
     * Deployed view files manager
     *
     * @param Magento_Core_Model_View_Service $viewService
     */
    public function __construct(Magento_Core_Model_View_Service $viewService)
    {
        $this->_viewService = $viewService;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicFilePath($filePath, $params)
    {
        return $this->_getDeployedFilePath($filePath, $params);
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

    /**
     * Get deployed file path
     *
     * @param string $filePath
     * @param array $params
     * @return string
     */
    protected function _getDeployedFilePath($filePath, $params)
    {
        /** @var $themeModel Magento_Core_Model_Theme */
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
}
