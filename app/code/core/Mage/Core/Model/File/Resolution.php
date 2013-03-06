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
    const XML_PATH_ALLOW_MAP_UPDATE = 'global/dev/file_fallback/allow_map_update';

    /**
     * Sub-directory where to store maps of view files fallback (if used)
     */
    const FALLBACK_MAP_DIR = 'maps/fallback';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var bool
     */
    protected $_isProductionMode;

    /**
     * @var bool
     */
    protected $_isDeveloperMode;

    /**
     * Array of resolvers, which are used to resolve the file with params to actual file paths
     *
     * @var array
     */
    protected $_resolvers = array();

    /**
     * Settings for strategies that are used to resolve file paths
     *
     * @var array
     */
    protected $_strategies = array(
        'production_mode' => array(
            'file' => 'Mage_Core_Model_File_Resolver_Fallback_CachingProxy',
            'locale' => 'Mage_Core_Model_File_Resolver_Fallback_CachingProxy',
            'view' => 'Mage_Core_Model_File_Resolver_View_ByParamsOnly',
        ),
        'caching_map' => array(
            'file' => 'Mage_Core_Model_File_Resolver_Fallback_CachingProxy',
            'locale' => 'Mage_Core_Model_File_Resolver_Fallback_CachingProxy',
            'view' => 'Mage_Core_Model_File_Resolver_Fallback_CachingProxy',
        ),
        'full_check' => array(
            'file' => 'Mage_Core_Model_File_Resolver_Fallback',
            'locale' => 'Mage_Core_Model_File_Resolver_Fallback',
            'view' => 'Mage_Core_Model_File_Resolver_Fallback',
        ),
    );

    /**
     * Map to be used with caching resolver
     *
     * @var Mage_Core_Model_File_Resolver_Fallback_CachingProxy_Map
     */
    protected $_cachingMap;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_App_State $appState
     * @param Mage_Core_Model_Dir $dirs
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_App_State $appState,
        Mage_Core_Model_Dir $dirs,
        Magento_Filesystem $filesystem
    ) {
        $this->_objectManager = $objectManager;
        $this->_isDeveloperMode = $appState->isDeveloperMode();
        $this->_isProductionMode = $appState->isProductionMode();
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
    }

    /**
     * Get existing file name, using fallback mechanism
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFile($file, $params)
    {
        /** @var $resolver Mage_Core_Model_File_Resolver_FileInterface */
        $resolver = $this->_getResolver('file', $params);
        return $resolver->getFile($params['area'], $params['themeModel'], $file, $params['module']);
    }

    /**
     * Get locale file name, using fallback mechanism
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getLocaleFile($file, $params)
    {
        /** @var $resolver Mage_Core_Model_File_Resolver_LocaleInterface */
        $resolver = $this->_getResolver('locale', $params);
        return $resolver->getLocaleFile($params['area'], $params['themeModel'], $params['locale'], $file);
    }

    /**
     * Get view file name, using fallback mechanism
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getViewFile($file, $params)
    {
        /** @var $resolver Mage_Core_Model_File_Resolver_ViewInterface */
        $resolver = $this->_getResolver('locale', $params);
        return $resolver->getViewFile($params['area'], $params['themeModel'], $params['locale'], $file, $params['module']);
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
        $resolver = $this->_getResolver('view', $params);
        if ($resolver instanceof Mage_Core_Model_File_Resolver_Fallback_CachingProxy) {
            /** @var $resolver Mage_Core_Model_File_Resolver_Fallback_CachingProxy */
            $resolver->setViewFilePathToMap($params['area'], $params['themeModel'], $params['locale'],
                $params['module'], $themeFile, $targetPath);
        }
        return $this;
    }

    /**
     * Creates or get cached resolved model according to the passed parameters and type of file being resolved
     *
     * @param string $fileType
     * @param array $params
     * @return mixed
     */
    protected function _getResolver($fileType, $params)
    {
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        $resolverClass = $this->_getResolverClass($fileType, $skipProxy);
        if (!isset($this->_resolvers[$resolverClass])) {
            $this->_resolvers[$resolverClass] = $this->_createResolver($resolverClass);
        }
        return $this->_resolvers[$resolverClass];
    }

    /**
     * Find the class of resolver, that must be used to resolve files of $fileType
     *
     * @param string $fileType
     * @param bool $skipProxy
     * @return string
     */
    protected function _getResolverClass($fileType, $skipProxy = false)
    {
        if ($this->_isProductionMode) {
            $strategy = $this->_strategies['production_mode'];
        } else if ($this->_isDeveloperMode || $skipProxy) {
            $strategy = $this->_strategies['full_check'];
        } else {
            $strategy = $this->_strategies['caching_map'];
        }
        return $strategy[$fileType];
    }

    /**
     * Create resolver by its class name
     *
     * @param string $className
     * @return mixed
     */
    protected function _createResolver($className)
    {
        switch ($className) {
            case 'Mage_Core_Model_File_Resolver_Fallback_CachingProxy':
                $arguments = array('map' => $this->_getCachingMap());
                break;
            default:
                $arguments = array();
                break;
        }
        return $this->_objectManager->create($className, $arguments);
    }

    /**
     * Return the map object to be used with caching resolver. Creates that object, if needed.
     *
     * @return Mage_Core_Model_File_Resolver_Fallback_CachingProxy_Map
     */
    protected function _getCachingMap()
    {
        if (!$this->_cachingMap) {
            $mapArguments = array(
                'mapDir' => $this->_dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . DIRECTORY_SEPARATOR
                    . self::FALLBACK_MAP_DIR,
                'baseDir' => $this->_dirs->getDir(Mage_Core_Model_Dir::ROOT),
                'canSaveMap' => (bool)(string)$this->_objectManager->get('Mage_Core_Model_Config')
                    ->getNode(self::XML_PATH_ALLOW_MAP_UPDATE),
            );
            $this->_cachingMap = $this->_objectManager->create(
                'Mage_Core_Model_File_Resolver_Fallback_CachingProxy_Map',
                $mapArguments
            );
        }
        return $this->_cachingMap;
    }
}
