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
 * Map for storing files fallback names
 */
class Mage_Core_Model_Design_Fallback_Map
{

    /**
     * Segments of fallback map
     *
     * @var array
     */
    protected $_segments = array();

    /**
     * List of changed segments, will be saved upon save() operation
     *
     * @var array
     */
    protected $_changedSegments = array();

    /**
     * Directory path, where serialized segments are saved
     *
     * @var string
     */
    protected $_segmentsPath;

    /**
     * Constructor
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->_segmentsPath = $dir . DIRECTORY_SEPARATOR;
    }

    /**
     * Retrieve file path by view parameters
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string|null $skin
     * @param string|null $locale
     * @param string|null $module
     * @return string|null
     */
    public function getFilePath($file, $area, $package, $theme, $skin, $locale, $module)
    {
        $segment = $this->_getSegment($area, $package, $theme, $skin, $locale);
        $fileKey = "$module|$file";
        return isset($segment[$fileKey]) ? $segment[$fileKey] : null;
    }

    /**
     * Set file path for the combination of view parameters
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string|null $skin
     * @param string|null $locale
     * @param string|null $module
     * @param string $filePath
     * @return Mage_Core_Model_Design_Fallback_Map
     */
    public function setFilePath($file, $area, $package, $theme, $skin, $locale, $module, $filePath)
    {
        $segment = $this->_getSegment($area, $package, $theme, $skin, $locale);
        $fileKey = "$module|$file";
        if (isset($segment[$fileKey]) && ($segment[$fileKey] == $filePath)) {
            return $this;
        }

        $segmentKey = "$area|$package|$theme|$skin|$locale";
        unset($segment); // Memory optimization, so PHP won't need to perform copy-on-write operation
        $this->_segments[$segmentKey][$fileKey] = $filePath;
        $this->_changedSegments[$segmentKey] = true;
        return $this;
    }

    /**
     * Return segment of a map according to view parameters
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string|null $skin
     * @param string|null $locale
     * @return array
     */
    protected function _getSegment($area, $package, $theme, $skin, $locale)
    {
        $segmentKey = "$area|$package|$theme|$skin|$locale";
        if (!isset($this->_segments[$segmentKey])) {
            $this->_loadSegment($segmentKey);
        }
        return $this->_segments[$segmentKey];
    }

    /**
     * Load segment of the fallback map according to parameters
     *
     * @param string $segmentKey
     * @return Mage_Core_Model_Design_Fallback_Map
     */
    protected function _loadSegment($segmentKey)
    {
        $fileName = $this->_getSegmentFilename($segmentKey);
        if (file_exists($fileName)) {
            $segment = unserialize(file_get_contents($fileName));
        } else {
            $segment = array();
        }
        $this->_segments[$segmentKey] = $segment;
        $this->_changedSegments[$segmentKey] = false;

        return $this;
    }

    /**
     * Return filename for storing map segment
     *
     * @param string $segmentKey
     * @return string
     */
    protected function _getSegmentFilename($segmentKey)
    {
        return $this->_segmentsPath . str_replace('|', '_', $segmentKey) . '.ser';
    }

    /**
     * Save all changed segments
     *
     * @return Mage_Core_Model_Design_Fallback_Map
     */
    public function save()
    {
        if (!is_dir($this->_segmentsPath)) {
            mkdir($this->_segmentsPath, 0777, true);
        }

        foreach ($this->_changedSegments as $segmentKey => $isChanged)
        {
            if (!$isChanged) {
                continue;
            }

            $fileName = $this->_getSegmentFileName($segmentKey);
            file_put_contents($fileName, serialize($this->_segments[$segmentKey]), LOCK_EX);
            $this->_changedSegments[$segmentKey] = false;
        }

        return $this;
    }
}
