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
 * A proxy for Fallback model. This proxy processes fallback resolution calls by either using map of cached paths, or
 * passing resolution to the Fallback model.
 */
class Mage_Core_Model_Design_Fallback_CachingProxy implements Mage_Core_Model_Design_FallbackInterface
{
    /**
     * @var string
     */
    protected $_area;

    /**
     * @var string
     */
    protected $_package;

    /**
     * @var string
     */
    protected $_theme;

    /**
     * @var string|null
     */
    protected $_skin;

    /**
     * @var string|null
     */
    protected $_locale;

    /**
     * Whether object can save map changes upon destruction
     *
     * @var bool
     */
    protected $_canSaveMap;

    /**
     * Whether there were changes in map
     *
     * @var bool
     */
    protected $_isMapChanged = false;

    /**
     * Map full filename
     *
     * @var string
     */
    protected $_mapFile;

    /**
     * Cached fallback map
     *
     * @var array
     */
    protected $_map;

    /**
     * Proxied fallback model
     *
     * @var Mage_Core_Model_Design_Fallback
     */
    protected $_fallback;

    /**
     * Directory to keep map file
     *
     * @var string
     */
    protected $_mapDir;

    /**
     * Path to Magento base directory
     *
     * @var string
     */
    protected $_basePath;

    /**
     * Constructor.
     * Following entries in $params are required: 'area', 'package', 'theme', 'skin', 'locale', 'canSaveMap',
     * 'mapDir', 'baseDir'.
     *
     * @param array $params
     */
    public function __construct($params)
    {
        $this->_area = $params['area'];
        $this->_package = $params['package'];
        $this->_theme = $params['theme'];
        $this->_skin = $params['skin'];
        $this->_locale = $params['locale'];
        $this->_canSaveMap = $params['canSaveMap'];
        $this->_mapDir = $params['mapDir'];
        $this->_basePath = $params['baseDir'] ? $params['baseDir'] . DIRECTORY_SEPARATOR : '';

        $this->_mapFile =
            "{$this->_mapDir}/{$this->_area}_{$this->_package}_{$this->_theme}_{$this->_skin}_{$this->_locale}.ser";
        $this->_map = file_exists($this->_mapFile) ? unserialize(file_get_contents($this->_mapFile)) : array();
    }

    public function __destruct()
    {
        if ($this->_isMapChanged && $this->_canSaveMap) {
            if (!is_dir($this->_mapDir)) {
                mkdir($this->_mapDir, 0777, true);
            }
            file_put_contents($this->_mapFile, serialize($this->_map), LOCK_EX);
        }
    }

    /**
     * Return instance of fallback model. Create it, if it has not been created yet.
     *
     * @return Mage_Core_Model_Design_Fallback
     */
    protected function _getFallback()
    {
        if (!$this->_fallback) {
            $this->_fallback = Mage::getModel('Mage_Core_Model_Design_Fallback', array(
                'area' => $this->_area,
                'package' => $this->_package,
                'theme' => $this->_theme,
                'skin' => $this->_skin,
                'locale' => $this->_locale,
            ));
        }
        return $this->_fallback;
    }

    /**
     * Return relative file name from map
     *
     * @param string $prefix
     * @param string $file
     * @param string|null $module
     * @return string|null
     */
    protected function _getFromMap($prefix, $file, $module = null)
    {
        $mapKey = "$prefix|$file|$module";
        if (isset($this->_map[$mapKey])) {
            $value =  $this->_map[$mapKey];
            if ((string) $value !== '') {
                return $this->_basePath . $value;
            } else {
                return $value;
            }
        } else {
            return null;
        }
    }

    /**
     * Sets file to map
     *
     * @param string $filePath
     * @param string $prefix
     * @param string $file
     * @param string|null $module
     * @return Mage_Core_Model_Design_Fallback_CachingProxy
     */
    protected function _setToMap($filePath, $prefix, $file, $module = null)
    {
        $mapKey = "$prefix|$file|$module";
        $this->_map[$mapKey] = substr($filePath, strlen($this->_basePath));
        $this->_isMapChanged = true;
        return $this;
    }

    /**
     * Get existing file name, using map and fallback mechanism
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($file, $module = null)
    {
        $result = $this->_getFromMap('theme', $file, $module);
        if (!$result) {
            $result = $this->_getFallback()->getFile($file, $module);
            $this->_setToMap($result, 'theme', $file, $module);
        }
        return $result;
    }

    /**
     * Get locale file name, using map and fallback mechanism
     *
     * @param string $file
     * @return string
     */
    public function getLocaleFile($file)
    {
        $result = $this->_getFromMap('locale', $file);
        if (!$result) {
            $result = $this->_getFallback()->getLocaleFile($file);
            $this->_setToMap($result, 'locale', $file);
        }
        return $result;
    }

    /**
     * Get skin file name, using map and fallback mechanism
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getSkinFile($file, $module = null)
    {
        $result = $this->_getFromMap('skin', $file, $module);
        if (!$result) {
            $result = $this->_getFallback()->getSkinFile($file, $module);
            $this->_setToMap($result, 'skin', $file, $module);
        }
        return $result;
    }

    /**
     * Object notified, that skin file was published, thus it can return published file name on next calls
     *
     * @param string $publicFilePath
     * @param string $file
     * @param string|null $module
     * @return Mage_Core_Model_Design_FallbackInterface
     */
    public function notifySkinFilePublished($publicFilePath, $file, $module = null)
    {
        return $this->_setToMap($publicFilePath, 'skin', $file, $module);
    }
}
