<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class for choosing the strategy for file resolution
 */
namespace Magento\Core\Model\Design\FileResolution;

class StrategyPool
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
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var string
     */
    protected $_appState;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirs;

    /**
     * Pool of strategy objects
     *
     * @var array
     */
    protected $_strategyPool = array();

    /**
     * Settings for strategies that are used to resolve file paths
     *
     * @var array
     */
    protected $_strategies = array(
        'production_mode' => array(
            'file' => 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback\CachingProxy',
            'locale' => 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback',
            'view' => 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback',
        ),
        'caching_map' => array(
            'file' => 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback\CachingProxy',
            'locale' => 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback\CachingProxy',
            'view' => 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback\CachingProxy',
        ),
        'full_check' => array(
            'file' => 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback',
            'locale' => 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback',
            'view' => 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback',
        ),
    );

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\App\State $appState
     * @param \Magento\Core\Model\Dir $dirs
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\App\State $appState,
        \Magento\Core\Model\Dir $dirs,
        \Magento\Filesystem $filesystem
    ) {
        $this->_objectManager = $objectManager;
        $this->_appState = $appState;
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
    }

    /**
     * Get strategy to resolve dynamic files (e.g. templates)
     *
     * @param bool $skipProxy
     * @return \Magento\Core\Model\Design\FileResolution\Strategy\FileInterface
     */
    public function getFileStrategy($skipProxy = false)
    {
        return $this->_getStrategy('file', $skipProxy);
    }

    /**
     * * Get strategy to resolve locale files (e.g. locale settings)
     *
     * @param bool $skipProxy
     * @return \Magento\Core\Model\Design\FileResolution\Strategy\LocaleInterface
     */
    public function getLocaleStrategy($skipProxy = false)
    {
        return $this->_getStrategy('locale', $skipProxy);
    }

    /**
     * Get strategy to resolve static view files (e.g. javascripts)
     *
     * @param bool $skipProxy
     * @return \Magento\Core\Model\Design\FileResolution\Strategy\ViewInterface
     */
    public function getViewStrategy($skipProxy = false)
    {
        return $this->_getStrategy('view', $skipProxy);
    }

    /**
     * Determine the strategy to be used. Create or get it from the pool.
     *
     * @param string $fileType
     * @param bool $skipProxy
     * @return mixed
     */
    protected function _getStrategy($fileType, $skipProxy = false)
    {
        $strategyClass = $this->_getStrategyClass($fileType, $skipProxy);
        if (!isset($this->_strategyPool[$strategyClass])) {
            $this->_strategyPool[$strategyClass] = $this->_createStrategy($strategyClass);
        }
        return $this->_strategyPool[$strategyClass];
    }

    /**
     * Find the class of strategy, that must be used to resolve files of $fileType
     *
     * @param string $fileType
     * @param bool $skipProxy
     * @return string
     * @throws \Magento\Core\Exception
     */
    protected function _getStrategyClass($fileType, $skipProxy = false)
    {
        $mode = $this->_appState->getMode();
        if ($mode == \Magento\Core\Model\App\State::MODE_PRODUCTION) {
            $strategyClasses = $this->_strategies['production_mode'];
        } else if (($mode == \Magento\Core\Model\App\State::MODE_DEVELOPER) || $skipProxy) {
            $strategyClasses = $this->_strategies['full_check'];
        } else if ($mode == \Magento\Core\Model\App\State::MODE_DEFAULT) {
            $strategyClasses = $this->_strategies['caching_map'];
        } else {
            throw new \Magento\Core\Exception("Unknown mode to choose strategy: {$mode}");
        }
        return $strategyClasses[$fileType];
    }

    /**
     * Create strategy by its class name
     *
     * @param string $className
     * @return mixed
     */
    protected function _createStrategy($className)
    {
        switch ($className) {
            case 'Magento\Core\Model\Design\FileResolution\Strategy\Fallback\CachingProxy':
                $mapDir = $this->_dirs->getDir(\Magento\Core\Model\Dir::VAR_DIR) . DIRECTORY_SEPARATOR
                    . self::FALLBACK_MAP_DIR;
                $arguments = array(
                    'mapDir' => str_replace('/', DIRECTORY_SEPARATOR, $mapDir),
                    'baseDir' => $this->_dirs->getDir(\Magento\Core\Model\Dir::ROOT),
                    'canSaveMap' => (bool)(string)$this->_objectManager->get('Magento\Core\Model\Config')
                        ->getNode(self::XML_PATH_ALLOW_MAP_UPDATE),
                );
                break;
            default:
                $arguments = array();
                break;
        }
        return $this->_objectManager->create($className, $arguments);
    }
}
