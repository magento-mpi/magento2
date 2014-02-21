<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * Builds path for files deployed into public directory in advance
 */
class DeployedFilesManager implements \Magento\View\PublicFilesManagerInterface
{
    /**
     * View service
     *
     * @var \Magento\View\Service
     */
    protected $_viewService;

    /**
     * Constructor
     *
     * @param \Magento\View\Service $viewService
     */
    public function __construct(Service $viewService)
    {
        $this->_viewService = $viewService;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicViewFile($filePath, array $params)
    {
        return $this->_getDeployedFilePath($filePath, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFile($filePath, array $params)
    {
        return $this->getPublicViewFile($filePath, $params);
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
        /** @var $themeModel \Magento\View\Design\ThemeInterface */
        $themeModel = $params['themeModel'];
        $themePath = $themeModel->getThemePath();
        while (empty($themePath) && $themeModel) {
            $themePath = $themeModel->getThemePath();
            $themeModel = $themeModel->getParentTheme();
        }
        $subPath = Url::getFullyQualifiedPath($filePath, $params['area'], $themePath, '', $params['module']);
        $subPath = str_replace('//', '/', $subPath); // workaround while locale support is not implemented
        $deployedFilePath = $this->_viewService->getPublicDir() . '/' . $subPath;

        return $deployedFilePath;
    }
}
