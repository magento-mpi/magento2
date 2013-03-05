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
 * Class for managing resolution of files
 */
class Mage_Core_Model_File_Resolution
{
    /**
     * Path to config node that allows automatically updating map files in runtime
     */
    const XML_PATH_ALLOW_MAP_UPDATE = 'global/dev/design_fallback/allow_map_update';

    /**
     * Sub-directory where to store maps of view files fallback (if used)
     */
    const FALLBACK_MAP_DIR = 'maps/fallback';

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var bool
     */
    protected $_isDeveloperMode;

    /**
     * Array of fallback model, controlling rules of fallback and inheritance for appropriate
     * area, package, theme, locale
     *
     * @var array
     */
    protected $_fallback = array();

    /**
     * @param Mage_Core_Model_App_State $appState
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(Mage_Core_Model_App_State $appState, Magento_Filesystem $filesystem)
    {
        $this->_isDeveloperMode = $appState->isDeveloperMode();
        $this->_filesystem = $filesystem;
    }

    /**
     * Return most appropriate model to perform fallback
     *
     * @param array $params
     * @return Mage_Core_Model_Design_FallbackInterface
     */
    protected function _getFallback($params)
    {
        $skipProxy = (isset($params['skipProxy']) && $params['skipProxy']) ?: $this->_isDeveloperMode;

        $cacheKey = join('|', array(
            $params['area'],
            $params['themeModel']->getCacheKey(),
            $params['locale'],
            $skipProxy
        ));
        if (!isset($this->_fallback[$cacheKey])) {
            $fallback = Mage::getObjectManager()->create('Mage_Core_Model_Design_Fallback', array('params' => $params));
            if ($skipProxy) {
                $this->_fallback[$cacheKey] = $fallback;
            } else {
                /** @var $dirs Mage_Core_Model_Dir */
                $dirs = Mage::getObjectManager()->get('Mage_Core_Model_Dir');
                $proxy = new Mage_Core_Model_Design_Fallback_CachingProxy(
                    $fallback,
                    $this->_filesystem,
                    $dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . DIRECTORY_SEPARATOR . self::FALLBACK_MAP_DIR,
                    $dirs->getDir(Mage_Core_Model_Dir::ROOT),
                    (bool)(string)Mage::app()->getConfig()->getNode(self::XML_PATH_ALLOW_MAP_UPDATE)
                );
                $this->_fallback[$cacheKey] = $proxy;
            }
        }
        return $this->_fallback[$cacheKey];
    }

    /**
     * Get existing file name, using fallback mechanism
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($file, $params)
    {
        return  $this->_getFallback($params)->getFile($file, $params['module']);
    }

    /**
     * Get locale file name, using fallback mechanism
     *
     * @param string $file
     * @return string
     */
    public function getLocaleFile($file, $params)
    {
        return $this->_getFallback($params)->getLocaleFile($file);
    }

    /**
     * Get view file name, using fallback mechanism
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($file, $params)
    {
        return $this->_getFallback($params)->getViewFile($file, $params['module']);
    }

    /**
     * Update file path in the map for a case when we use caching mechanism
     *
     * @param string $targetPath
     * @param string $themeFile
     * @param array $params
     * @return Mage_Core_Model_File_Resolution
     */
    public function notifyViewFileLocationChanged($targetPath, $themeFile, $params)
    {
        $fallback = $this->_getFallback($params);
        if ($fallback instanceof Mage_Core_Model_Design_Fallback_CachingProxy) {
            /** @var $fallback Mage_Core_Model_Design_Fallback_CachingProxy */
            $fallback->setFilePathToMap($targetPath, $themeFile, $params['module']);
        }
        return $this;
    }
}
