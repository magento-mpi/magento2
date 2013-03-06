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
 * A map holder for the caching proxy resolver
 */
class Mage_Core_Model_File_Resolver_Fallback_CachingProxy_Map
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Path to maps directory
     *
     * @var string
     */
    protected $_mapDir;

    /**
     * Path to Magento base directory
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Whether object can save map changes upon destruction
     *
     * @var bool
     */
    protected $_canSaveMap;

    /**
     * Cached fallback map sections
     *
     * @var array
     */
    protected $_sections = array();

    /**
     * @param Magento_Filesystem $filesystem
     * @param string $mapDir
     * @param string $baseDir
     * @param bool $canSaveMap
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        $mapDir,
        $baseDir,
        $canSaveMap = true
    ) {
        $this->_filesystem = $filesystem;
        if (!$filesystem->isDirectory($baseDir)) {
            throw new InvalidArgumentException("Wrong base directory specified: '{$baseDir}'");
        }
        $this->_baseDir = $baseDir;
        $this->_canSaveMap = $canSaveMap;
        $this->_mapDir = $mapDir;
    }

    /**
     * Write the serialized map to the section files
     */
    public function __destruct()
    {
        if (!$this->_canSaveMap) {
            return;
        }
        if (!$this->_filesystem->isDirectory($this->_mapDir)) {
            $this->_filesystem->createDirectory($this->_mapDir, 0777);
        }
        foreach ($this->_sections as $sectionFile => $section) {
            if (!$section['is_changed']) {
                continue;
            }
            $filePath = $this->_mapDir . DIRECTORY_SEPARATOR . $sectionFile;
            $this->_filesystem->write($filePath, serialize($section['data']));
        }
    }

    /**
     * Get stored full file path
     *
     * @param string $fileType
     * @param string $area
     * @param Mage_Core_Model_Theme $theme
     * @param string|null $locale
     * @param string|null $module
     * @param string $file
     * @return null|string
     */
    public function get($fileType, $area, Mage_Core_Model_Theme $theme, $locale, $module, $file)
    {
        $sectionKey = $this->_loadSection($area, $theme, $locale);
        $fileKey = "$fileType|$file|$module";
        if (isset($this->_sections[$sectionKey]['data'][$fileKey])) {
            $value = $this->_sections[$sectionKey]['data'][$fileKey];
            if ('' !== (string)$value) {
                $value = $this->_baseDir . DIRECTORY_SEPARATOR . $value;
            }
            return $value;
        }
        return null;
    }

    /**
     * Set stored full file path
     *
     * @param string $fileType
     * @param string $area
     * @param Mage_Core_Model_Theme $theme
     * @param string|null $locale
     * @param string|null $module
     * @param string $file
     * @param string $filePath
     * @throws Magento_Exception
     */
    public function set($fileType, $area, Mage_Core_Model_Theme $theme, $locale, $module, $file, $filePath)
    {
        $pattern = $this->_baseDir . DIRECTORY_SEPARATOR;
        if (0 !== strpos($filePath, $pattern)) {
            throw new Magento_Exception(
                "Attempt to store fallback path '{$filePath}', which is not within '{$pattern}'"
            );
        }
        $value = substr($filePath, strlen($pattern));

        $sectionKey = $this->_loadSection($area, $theme, $locale);
        $fileKey = "$fileType|$file|$module";
        $this->_sections[$sectionKey]['data'][$fileKey] = $value;
        $this->_sections[$sectionKey]['is_changed'] = true;
    }

    /**
     * Compose section file name
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string|null $locale
     * @return string
     */
    protected function _getSectionFile($area, Mage_Core_Model_Theme $themeModel, $locale)
    {
        $theme = $themeModel->getId() ?: $themeModel->getThemePath();
        return "{$area}_{$theme}_{$locale}.ser";
    }

    /**
     * Load section and return its key
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string|null $locale
     * @return string
     */
    protected function _loadSection($area, Mage_Core_Model_Theme $themeModel, $locale)
    {
        $sectionFile = $this->_getSectionFile($area, $themeModel, $locale);
        if (!isset($this->_sections[$sectionFile])) {
            $filePath = $this->_mapDir . DIRECTORY_SEPARATOR . $sectionFile;
            $this->_sections[$sectionFile] = array(
                'data' => array(),
                'is_changed' => false,
            );
            if ($this->_filesystem->isFile($filePath)) {
                $this->_sections[$sectionFile]['data'] = unserialize($this->_filesystem->read($filePath));
            }
        }
        return $sectionFile;
    }
}

