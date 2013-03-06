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
     * Settings for strategies used
     *
     * @var array
     */
    protected $_strategies = array(
        'production_mode' => array(
            'file' => 'Mage_Core_Model_File_Resolver_CachingProxy',
            'locale' => 'Mage_Core_Model_File_Resolver_CachingProxy',
            'view' => 'Mage_Core_Model_File_Resolver_ByParamsOnly',
        ),
        'caching_map' => array(
            'file' => 'Mage_Core_Model_File_Resolver_CachingProxy',
            'locale' => 'Mage_Core_Model_File_Resolver_CachingProxy',
            'view' => 'Mage_Core_Model_File_Resolver_CachingProxy',
        ),
        'full_check' => array(
            'file' => 'Mage_Core_Model_File_Resolver_Fallback',
            'locale' => 'Mage_Core_Model_File_Resolver_Fallback',
            'view' => 'Mage_Core_Model_File_Resolver_Fallback',
        ),
    );

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_App_State $appState
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dirs
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_App_State $appState,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $dirs
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
        return  $this->_getResolver('file', $params)->getFile($file, $params['module']);
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
        return $this->_getResolver('locale', $params)->getLocaleFile($file);
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
        return $this->_getResolver('view', $params)->getViewFile($file, $params['module']);
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
            /** @var $fallback Mage_Core_Model_File_Resolver_Fallback_CachingProxy */
            $resolver->setFilePathToMap($targetPath, $themeFile, $params['module']);
        }
        return $this;
    }

    /**
     * Creates or get cached resolved model according to the passed parameters and type of file being resolved
     *
     * @param string $fileType
     * @param $params
     * @return Mage_Core_Model_File_ResolverInterface
     */
    protected function _getResolver($fileType, $params)
    {
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        $resolverClass = $this->_getResolverClass($fileType, $skipProxy);

        $cacheKey = join('|', array(
            $resolverClass,
            $params['area'],
            $params['themeModel']->getCacheKey(),
            $params['locale']
        ));

        if (!isset($this->_resolvers[$cacheKey])) {
            $this->_resolvers[$cacheKey] = $this->_createResolver($resolverClass, $params);
        }
        return $this->_resolvers[$cacheKey];
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
     * Create resolver object
     *
     * @param string $resolverClass
     * @param array $params
     * @return Mage_Core_Model_File_ResolverInterface
     * @throws Mage_Core_Exception
     */
    protected function _createResolver($resolverClass, $params)
    {
        switch ($resolverClass) {
            case 'Mage_Core_Model_File_Resolver_ByParamsOnly':
                $arguments = array();
                break;
            case 'Mage_Core_Model_File_Resolver_CachingProxy':
                $arguments = array(
                    'fallback' => $this->_createResolver('Mage_Core_Model_File_Resolver_Fallback', $params),
                    'mapDir' => $dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . DIRECTORY_SEPARATOR
                        . self::FALLBACK_MAP_DIR,
                    'baseDir' => $dirs->getDir(Mage_Core_Model_Dir::ROOT),
                    'canSaveMap' => (bool)(string)Mage::getConfig()->getNode(self::XML_PATH_ALLOW_MAP_UPDATE),
                );
                break;
            case 'Mage_Core_Model_File_Resolver_Fallback':
                $arguments = array(
                    'params' => $params
                );
                break;
            default:
                throw new Mage_Core_Exception("Unknown file path resolver: {$resolverClass}");
        }
        return $this->_objectManager->create($resolverClass, $arguments);
    }
}
