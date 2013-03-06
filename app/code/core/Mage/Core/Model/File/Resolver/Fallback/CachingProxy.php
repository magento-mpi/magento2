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
 * A proxy for the Fallback resolver. This proxy processes fallback resolution calls by either using map of cached \
 * paths, or passing resolution to the Fallback resolver.
 */
class Mage_Core_Model_File_Resolver_Fallback_CachingProxy implements Mage_Core_Model_File_Resolver_FileInterface,
    Mage_Core_Model_File_Resolver_LocaleInterface, Mage_Core_Model_File_Resolver_ViewInterface
{
    /**
     * Factory to create the fallback model
     *
     * @var Mage_Core_Model_File_Resolver_Fallback_CachingProxy_Map
     */
    protected $_fallbackFactory;

    /**
     * Proxied fallback model
     *
     * @var Mage_Core_Model_File_Resolver_Fallback
     */
    protected $_fallback;

    /**
     * @param Mage_Core_Model_File_Resolver_Fallback_CachingProxy_Map $map
     * @param Mage_Core_Model_File_Resolver_Fallback_Factory $fallbackFactory
     */
    public function __construct(
        Mage_Core_Model_File_Resolver_Fallback_CachingProxy_Map $map,
        Mage_Core_Model_File_Resolver_Fallback_Factory $fallbackFactory
    ) {
        $this->_fallbackFactory = $fallbackFactory;
        $this->_map = $map;
    }

    /**
     * Proxy to Mage_Core_Model_File_Resolver_Fallback::getFile()
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($area, Mage_Core_Model_Theme $themeModel, $file, $module = null)
    {
        $result = $this->_map->get('file', $area, $themeModel, null, $module, $file);
        if (!$result) {
            $result = $this->_getFallback()->getFile($area, $themeModel, $file, $module);
            $this->_map->set('file', $area, $themeModel, null, $module, $file, $result);
        }
        return $result;
    }

    /**
     * Proxy to Mage_Core_Model_File_Resolver_Fallback::getLocaleFile()
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $locale
     * @param string $file
     * @return string
     */
    public function getLocaleFile($area, Mage_Core_Model_Theme $themeModel, $locale, $file)
    {
        $result = $this->_map->get('locale', $area, $themeModel, $locale, null, $file);
        if (!$result) {
            $result = $this->_getFallback()->getLocaleFile($area, $themeModel, $locale, $file);
            $this->_map->set('locale', $area, $themeModel, $locale, null, $file, $result);
        }
        return $result;
    }

    /**
     * Proxy to Mage_Core_Model_File_Resolver_Fallback::getViewFile()
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($area, Mage_Core_Model_Theme $themeModel, $locale, $file, $module = null)
    {
        $result = $this->_map->get('view', $area, $themeModel, $locale, $module, $file);
        if (!$result) {
            $result = $this->_getFallback()->getViewFile($area, $themeModel, $locale, $file, $module);
            $this->_map->set('view', $area, $themeModel, $locale, $module, $file, $result);
        }
        return $result;
    }

    /**
     * Creates fallback model to forward requests to
     *
     * @return Mage_Core_Model_File_Resolver_Fallback
     */
    protected function _getFallback()
    {
        return $this->_fallbackFactory->createFromArray();
    }

    /**
     * Set file path to map.
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $locale
     * @param $module
     * @param string $file
     * @param string $filePath
     * @return Mage_Core_Model_File_Resolver_Fallback_CachingProxy
     */
    public function setViewFilePathToMap($area, $themeModel, $locale, $module, $file, $filePath)
    {
        $this->_map->set('view', $area, $themeModel, $locale, $module, $file, $filePath);
        return $this;
    }
}
