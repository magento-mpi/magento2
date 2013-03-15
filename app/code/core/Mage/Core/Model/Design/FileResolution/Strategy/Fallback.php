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
 * Resolver, which performs full search of files, according to fallback rules
 */
class Mage_Core_Model_Design_FileResolution_Strategy_Fallback
    implements Mage_Core_Model_Design_FileResolution_Strategy_FileInterface,
    Mage_Core_Model_Design_FileResolution_Strategy_LocaleInterface,
    Mage_Core_Model_Design_FileResolution_Strategy_ViewInterface
{
    /**
     * @var array
     */
    protected $_themeList = array();

    /**
     * @var Mage_Core_Model_Design_Fallback_List_File
     */
    protected $_fallbackFile;

    /**
     * @var Mage_Core_Model_Design_Fallback_List_Locale
     */
    protected $_fallbackLocale;

    /**
     * @var Mage_Core_Model_Design_Fallback_List_View
     */
    protected $_fallbackViewFile;

    /**
     * Constructor.
     * Following entries in $params are required: 'area', 'themeModel', 'locale'. The 'appConfig' and
     * 'themeConfig' may contain application config and theme config, respectively. If these these entries are not
     * present or null, then they will be retrieved from global application instance.
     *
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dirs
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $dirs
    ) {
        $this->_dirs = $dirs;
        $this->_objectManager = $objectManager;
        $this->_filesystem = $filesystem;
        $this->_fallbackFile = new Mage_Core_Model_Design_Fallback_List_File($this->_dirs);
        $this->_fallbackLocale = new Mage_Core_Model_Design_Fallback_List_Locale($this->_dirs);
        $this->_fallbackViewFile = new Mage_Core_Model_Design_Fallback_List_View($this->_dirs);
    }

    /**
     * Get existing file name, using fallback mechanism
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($area, Mage_Core_Model_Theme $themeModel, $file, $module = null)
    {
        $params = array();
        if ($module) {
            list($params['namespace'], $params['module']) = explode('_', $module);
            $params['pool'] =
                (string)$this->_objectManager->get('Mage_Core_Model_Config')->getModuleConfig($module)->codePool;
        } else {
            $params['namespace'] = null;
            $params['module'] = null;
        }
        return $this->_getFallbackFile($area, $themeModel, $file, $this->_fallbackFile, $params);
    }

    /**
     * Get locale file name, using fallback mechanism
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $locale
     * @param string $file
     * @return string
     */
    public function getLocaleFile($area, Mage_Core_Model_Theme $themeModel, $locale, $file)
    {
        $params = array('locale' => $locale);

        return $this->_getFallbackFile($area, $themeModel, $file, $this->_fallbackLocale, $params);
    }

    /**
     * Get theme file name, using fallback mechanism
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
        $params = array();
        if ($module) {
            list($params['namespace'], $params['module']) = explode('_', $module);
            $params['pool'] =
                (string)$this->_objectManager->get('Mage_Core_Model_Config')->getModuleConfig($module)->codePool;
        } else {
            $params['namespace'] = null;
            $params['module'] = null;
        }
        $params['locale'] = $locale;

        return $this->_getFallbackFile($area, $themeModel, $file, $this->_fallbackViewFile, $params);
    }

    /**
     * Get path of file after using fallback rules
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $file
     * @param Mage_Core_Model_Design_Fallback_Rule_RuleInterface $fallbackList
     * @param array $specificParams
     * @return string
     */
    protected function _getFallbackFile($area, Mage_Core_Model_Theme $themeModel, $file, $fallbackList,
        $specificParams = array()
    ) {
        $params = array(
            'area'          => $area,
            'theme'         => $themeModel,
        );
        $params = array_merge($params, $specificParams);
        $path = '';

        foreach ($fallbackList->getPatternDirs($params) as $dir) {
            $path = $dir . DS . $file;
            if ($this->_filesystem->has($path)) {
                return $path;
            }
        }
        return $path;
    }
}
