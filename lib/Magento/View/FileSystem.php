<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * Model that finds file paths by their fileId
 */
class FileSystem
{
    /**
     * Model, used to resolve the file paths
     *
     * @var \Magento\View\Design\FileResolution\StrategyPool
     */
    protected $_resolutionPool;

    /**
     * @var Service
     */
    protected $_viewService;

    /**
     * @param \Magento\View\Design\FileResolution\StrategyPool $resolutionPool
     * @param Service $viewService
     */
    public function __construct(
        \Magento\View\Design\FileResolution\StrategyPool $resolutionPool,
        Service $viewService
    ) {
        $this->_resolutionPool = $resolutionPool;
        $this->_viewService = $viewService;
    }

    /**
     * Get existing file name with fallback to default
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getFilename($fileId, array $params = array())
    {
        $filePath = $this->_viewService->extractScope($this->normalizePath($fileId), $params);
        $this->_viewService->updateDesignParams($params);
        return $this->_resolutionPool->getFileStrategy(!empty($params['skipProxy']))
            ->getFile($params['area'], $params['themeModel'], $filePath, $params['module']);
    }

    /**
     * Get a locale file
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getLocaleFileName($file, array $params = array())
    {
        $this->_viewService->updateDesignParams($params);
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        return $this->_resolutionPool->getLocaleStrategy($skipProxy)->getLocaleFile(
            $params['area'],
            $params['themeModel'],
            $params['locale'],
            $file
        );
    }

    /**
     * Find a view file using fallback mechanism
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFile($fileId, array $params = array())
    {
        $filePath = $this->_viewService->extractScope($this->normalizePath($fileId), $params);
        $this->_viewService->updateDesignParams($params);
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        return $this->_resolutionPool->getViewStrategy($skipProxy)->getViewFile(
            $params['area'],
            $params['themeModel'],
            $params['locale'],
            $filePath,
            $params['module']
        );
    }

    /**
     * Notify that view file resolved path was changed (i.e. it was published to a public directory)
     *
     * @param Publisher\FileInterface $publisherFile
     * @return $this
     */
    public function notifyViewFileLocationChanged(Publisher\FileInterface $publisherFile)
    {
        $params = $publisherFile->getViewParams();
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        $strategy = $this->_resolutionPool->getViewStrategy($skipProxy);
        if ($strategy instanceof Design\FileResolution\Strategy\View\NotifiableInterface) {
            /** @var $strategy Design\FileResolution\Strategy\View\NotifiableInterface  */
            $strategy->setViewFilePathToMap(
                $params['area'],
                $params['themeModel'],
                $params['locale'],
                $params['module'],
                $publisherFile->getFilePath(),
                $publisherFile->buildPublicViewFilename()
            );
        }

        return $this;
    }

    /**
     * Remove unmeaning path chunks from path
     *
     * @param string $path
     * @return string
     */
    public function normalizePath($path)
    {
        $parts = explode('/', $path);
        $result = array();

        foreach ($parts as $part) {
            if ('..' === $part) {
                if (!count($result) || ($result[count($result) - 1] == '..')) {
                    $result[] = $part;
                } else {
                    array_pop($result);
                }
            } elseif ('.' !== $part) {
                $result[] = $part;
            }
        }
        return implode('/', $result);
    }
}
