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
    protected $_map = array();

    /**
     * Proxied fallback model
     *
     * @var Mage_Core_Model_Design_Fallback
     */
    protected $_fallback;

    /**
     * Path to Magento base directory
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Read the class map according to provided fallback model and parameters
     *
     * @param Mage_Core_Model_Design_Fallback $fallback
     * @param string $mapDir directory where to look the map files in
     * @param string $baseDir base directory path to prepend to file paths
     * @param bool $canSaveMap whether to update map file in destructor
     * @throws InvalidArgumentException
     */
    public function __construct(Mage_Core_Model_Design_Fallback $fallback, $mapDir, $baseDir, $canSaveMap = true)
    {
        if (!is_dir($baseDir)) {
            throw new InvalidArgumentException("Wrong base directory specified: '{$baseDir}'");
        }
        $this->_fallback = $fallback;
        $this->_baseDir = $baseDir;
        $this->_canSaveMap = $canSaveMap;
        $this->_mapFile = $mapDir . DIRECTORY_SEPARATOR
            . "{$fallback->getArea()}_{$fallback->getTheme()}_{$fallback->getLocale()}.ser";
        if (file_exists($this->_mapFile)) {
            $this->_map = unserialize(file_get_contents($this->_mapFile));
        }
    }

    /**
     * Write the serialized class map to the file
     */
    public function __destruct()
    {
        if ($this->_isMapChanged && $this->_canSaveMap) {
            $dir = dirname($this->_mapFile);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($this->_mapFile, serialize($this->_map), LOCK_EX);
        }
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
                return $this->_baseDir . DIRECTORY_SEPARATOR . $value;
            } else {
                return $value;
            }
        } else {
            return null;
        }
    }

    /**
     * Sets file to map. The file path must be within baseDir path.
     *
     * @param string $prefix
     * @param string $file
     * @param string|null $module
     * @param string $filePath
     * @throws Magento_Exception
     */
    protected function _setToMap($prefix, $file, $module, $filePath)
    {
        $pattern = $this->_baseDir . DIRECTORY_SEPARATOR;
        if (0 !== strpos($filePath, $pattern, 0)) {
            throw new Magento_Exception(
                "Attempt to store fallback path '{$filePath}', which is not within '{$pattern}'"
            );
        }
        $mapKey = "$prefix|$file|$module";
        $this->_map[$mapKey] = substr($filePath, strlen($pattern));
        $this->_isMapChanged = true;
    }

    /**
     * Proxy to Mage_Core_Model_Design_Fallback::getFile()
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($file, $module = null)
    {
        $result = $this->_getFromMap('theme', $file, $module);
        if (!$result) {
            $result = $this->_fallback->getFile($file, $module);
            $this->_setToMap('theme', $file, $module, $result);
        }
        return $result;
    }

    /**
     * Proxy to Mage_Core_Model_Design_Fallback::getLocaleFile()
     *
     * @param string $file
     * @return string
     */
    public function getLocaleFile($file)
    {
        $result = $this->_getFromMap('locale', $file);
        if (!$result) {
            $result = $this->_fallback->getLocaleFile($file);
            $this->_setToMap('locale', $file, null, $result);
        }
        return $result;
    }

    /**
     * Proxy to Mage_Core_Model_Design_Fallback::getViewFile()
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($file, $module = null)
    {
        $result = $this->_getFromMap('view', $file, $module);
        if (!$result) {
            $result = $this->_fallback->getViewFile($file, $module);
            $this->_setToMap('view', $file, $module, $result);
        }
        return $result;
    }

    /**
     * Object notified, that view file was published, thus it can return published file name on next calls
     *
     * @param string $publicFilePath
     * @param string $file
     * @param string|null $module
     * @return Mage_Core_Model_Design_Fallback_CachingProxy
     */
    public function notifyViewFilePublished($publicFilePath, $file, $module = null)
    {
        $this->_setToMap('view', $file, $module, $publicFilePath);
        return $this;
    }
}
