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
 * Model that finds file paths by their fileId
 */
class Magento_Core_Model_View_FileSystem
{
    /**
     * Model, used to resolve the file paths
     *
     * @var Magento_Core_Model_Design_FileResolution_StrategyPool
     */
    protected $_resolutionPool = null;

    /**
     * @var Magento_Core_Model_View_Service
     */
    protected $_viewService;

    /**
     * View files system model
     *
     * @param Magento_Core_Model_Design_FileResolution_StrategyPool $resolutionPool
     * @param Magento_Core_Model_View_Service $viewService
     */
    public function __construct(
        Magento_Core_Model_Design_FileResolution_StrategyPool $resolutionPool,
        Magento_Core_Model_View_Service $viewService
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
        $filePath = $this->_viewService->extractScope($fileId, $params);
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
        return $this->_resolutionPool->getLocaleStrategy($skipProxy)->getLocaleFile($params['area'],
            $params['themeModel'], $params['locale'], $file);
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
        $filePath = $this->_viewService->extractScope($fileId, $params);
        $this->_viewService->updateDesignParams($params);
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        return $this->_resolutionPool->getViewStrategy($skipProxy)->getViewFile($params['area'],
            $params['themeModel'], $params['locale'], $filePath, $params['module']);
    }

    /**
     * Notify that view file resolved path was changed (i.e. it was published to a public directory)
     *
     * @param string $targetPath
     * @param string $fileId
     * @param array $params
     * @return $this
     */
    public function notifyViewFileLocationChanged($targetPath, $fileId, $params)
    {
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        $strategy = $this->_resolutionPool->getViewStrategy($skipProxy);
        if ($strategy instanceof Magento_Core_Model_Design_FileResolution_Strategy_View_NotifiableInterface) {
            /** @var $strategy Magento_Core_Model_Design_FileResolution_Strategy_View_NotifiableInterface  */
            $filePath = $this->_viewService->extractScope($fileId, $params);
            $this->_viewService->updateDesignParams($params);
            $strategy->setViewFilePathToMap(
                $params['area'], $params['themeModel'], $params['locale'], $params['module'], $filePath, $targetPath
            );
        }

        return $this;
    }
}
