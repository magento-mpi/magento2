<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View;

/**
 * Builds path for files deployed into public directory in advance
 */
class DeployedFilesManager implements \Magento\Framework\View\PublicFilesManagerInterface
{
    /**
     * View service
     *
     * @var \Magento\Framework\View\Service
     */
    protected $_viewService;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Service $viewService
     */
    public function __construct(\Magento\Framework\View\Service $viewService)
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
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public static function buildDeployedFilePath($area, $themePath, $file, $module = null)
    {
        return $area . '/' . $themePath . '/' . ($module ? $module . '/' : '') . $file;
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
        /** @var $themeModel \Magento\Framework\View\Design\ThemeInterface */
        $themeModel = $params['themeModel'];
        $themePath = $themeModel->getThemePath();
        while (empty($themePath) && $themeModel) {
            $themePath = $themeModel->getThemePath();
            $themeModel = $themeModel->getParentTheme();
        }
        $subPath = self::buildDeployedFilePath($params['area'], $themePath, $filePath, $params['module']);
        $deployedFilePath = $this->_viewService->getPublicDir() . '/' . $subPath;

        return $deployedFilePath;
    }
}
