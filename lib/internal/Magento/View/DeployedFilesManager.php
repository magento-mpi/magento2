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
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var Asset\PathGenerator
     */
    protected $path;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Asset\PathGenerator $path
     */
    public function __construct(\Magento\App\Filesystem $filesystem, Asset\PathGenerator $path)
    {
        $this->filesystem = $filesystem;
        $this->path = $path;
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
        $subPath = $this->path->getPath($params['area'], $themePath, '', $params['module']) . '/' . $filePath;
        $subPath = str_replace('//', '/', $subPath); // workaround while locale support is not implemented
        $deployedFilePath = $this->filesystem->getPath(\Magento\App\Filesystem::STATIC_VIEW_DIR) . '/' . $subPath;

        return $deployedFilePath;
    }
}
