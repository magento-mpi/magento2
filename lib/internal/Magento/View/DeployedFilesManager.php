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
class DeployedFilesManager implements \Magento\View\FilesManagerInterface
{
    /**
     * View service
     *
     * @var \Magento\View\Service
     */
    protected $_viewService;

    /**
     * @var Path
     */
    protected $_path;

    /**
     * Constructor
     *
     * @param \Magento\View\Service $viewService
     * @param Path $path
     */
    public function __construct(Service $viewService, Path $path)
    {
        $this->_viewService = $viewService;
        $this->_path = $path;
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
        $subPath = $this->_path->getFullyQualifiedPath($params['area'], $themePath, '', $params['module'])
            . '/' . $filePath;
        $subPath = str_replace('//', '/', $subPath); // workaround while locale support is not implemented
        $deployedFilePath = $this->_viewService->getPublicDir() . '/' . $subPath;

        return $deployedFilePath;
    }
}
