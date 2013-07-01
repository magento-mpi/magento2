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
 * View responsibilities:
 * - process input params
 * - get correct file search strategy
 * - use strategy to get file
 *
 * Publisher responsibilities:
 * - copy file to public directory
 * - replace URLs in CSS file and move related files
 *
 * Service responsibilities:
 * - extract scope
 * - process params array
 * - build url by public path
 *
 * (Design?) params object:
 * - contains params for correct fetching of given view file
 * - options:
 *      _secure - used for URLs only
 *      skipProxy
 *      area
 *      themeModel
 *      themeId
 *      package + theme
 *      locale
 *      module
 *  - every type of file requires different params:
 *      - URL: _secure, area, locale, module, themeModel
 *      - common file: skipProxy, area, themeModel, module
 *      - locale file: skipProxy, area, themeModel, locale
 *      - view file: skipProxy, area, themeModel, locale, module
 *
 *
 *
 *
 *
 * @TODO
 *  - We can extract object that builds paths and urls
 *  - remove area
 *
 * Used path variables:
 *  - $fileId = 'Mage_Core::css/style.css' or more simple case 'css/style.css'
 *  - $filePath = 'css/style.css'
 *  - $sourcePath = '/usr/local/.../css/style.css'
 */
class Mage_Core_Model_View_FileSystem implements Mage_Core_Model_View_FileSystemInterface
{
    /**
     * Model, used to resolve the file paths
     *
     * @var Mage_Core_Model_Design_FileResolution_StrategyPool
     */
    protected $_resolutionPool = null;

    /**
     * @var Mage_Core_Model_View_Service
     */
    protected $_viewService;

    /**
     * View files system model
     *
     * @param Mage_Core_Model_Design_FileResolution_StrategyPool $resolutionPool
     * @param Mage_Core_Model_View_Service $viewService
     */
    public function __construct(
        Mage_Core_Model_Design_FileResolution_StrategyPool $resolutionPool,
        Mage_Core_Model_View_Service $viewService
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
     * @param $targetPath
     * @param $fileId
     * @param $params
     * @return Mage_Core_Model_View_Design
     */
    public function notifyViewFileLocationChanged($targetPath, $fileId, $params)
    {
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        $strategy = $this->_resolutionPool->getViewStrategy($skipProxy);
        if ($strategy instanceof Mage_Core_Model_Design_FileResolution_Strategy_View_NotifiableInterface) {
            /** @var $strategy Mage_Core_Model_Design_FileResolution_Strategy_View_NotifiableInterface  */
            $filePath = $this->_viewService->extractScope($fileId, $params);
            $this->_viewService->updateDesignParams($params);
            $strategy->setViewFilePathToMap(
                $params['area'], $params['themeModel'], $params['locale'], $params['module'], $filePath, $targetPath
            );
        }

        return $this;
    }
}
