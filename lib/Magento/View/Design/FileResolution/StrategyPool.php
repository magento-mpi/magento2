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
namespace Magento\View\Design\FileResolution;

use Magento\Core\Exception;
use Magento\Core\Model\App\State;
use Magento\Core\Model\Dir;
use Magento\Filesystem;
use Magento\ObjectManager;

/**
 * Strategy Pool
 *
 * @package Magento\View
 */
class StrategyPool
{
    /**
     * Sub-directory where to store maps of view files fallback (if used)
     */
    const FALLBACK_MAP_DIR = 'maps/fallback';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $appState;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Dir
     */
    protected $dirs;

    /**
     * Pool of strategy objects
     *
     * @var array
     */
    protected $strategyPool = array();

    /**
     * Settings for strategies that are used to resolve file paths
     *
     * @var array
     */
    protected $strategies = array(
        'production_mode' => array(
            'file' => 'Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy',
            'locale' => 'Magento\View\Design\FileResolution\Strategy\Fallback',
            'view' => 'Magento\View\Design\FileResolution\Strategy\Fallback',
        ),
        'caching_map' => array(
            'file' => 'Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy',
            'locale' => 'Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy',
            'view' => 'Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy',
        ),
        'full_check' => array(
            'file' => 'Magento\View\Design\FileResolution\Strategy\Fallback',
            'locale' => 'Magento\View\Design\FileResolution\Strategy\Fallback',
            'view' => 'Magento\View\Design\FileResolution\Strategy\Fallback',
        ),
    );

    /**
     * @param ObjectManager $objectManager
     * @param State $appState
     * @param Dir $dirs
     * @param Filesystem $filesystem
     */
    public function __construct(
        ObjectManager $objectManager,
        State $appState,
        Dir $dirs,
        Filesystem $filesystem
    ) {
        $this->objectManager = $objectManager;
        $this->appState = $appState;
        $this->filesystem = $filesystem;
        $this->dirs = $dirs;
    }

    /**
     * Get strategy to resolve dynamic files (e.g. templates)
     *
     * @param bool $skipProxy
     * @return \Magento\View\Design\FileResolution\Strategy\FileInterface
     */
    public function getFileStrategy($skipProxy = false)
    {
        return $this->getStrategy('file', $skipProxy);
    }

    /**
     * * Get strategy to resolve locale files (e.g. locale settings)
     *
     * @param bool $skipProxy
     * @return \Magento\View\Design\FileResolution\Strategy\LocaleInterface
     */
    public function getLocaleStrategy($skipProxy = false)
    {
        return $this->getStrategy('locale', $skipProxy);
    }

    /**
     * Get strategy to resolve static view files (e.g. javascripts)
     *
     * @param bool $skipProxy
     * @return \Magento\View\Design\FileResolution\Strategy\ViewInterface
     */
    public function getViewStrategy($skipProxy = false)
    {
        return $this->getStrategy('view', $skipProxy);
    }

    /**
     * Determine the strategy to be used. Create or get it from the pool.
     *
     * @param string $fileType
     * @param bool $skipProxy
     * @return mixed
     */
    protected function getStrategy($fileType, $skipProxy = false)
    {
        $strategyClass = $this->getStrategyClass($fileType, $skipProxy);
        if (!isset($this->strategyPool[$strategyClass])) {
            $this->strategyPool[$strategyClass] = $this->createStrategy($strategyClass);
        }
        return $this->strategyPool[$strategyClass];
    }

    /**
     * Find the class of strategy, that must be used to resolve files of $fileType
     *
     * @param string $fileType
     * @param bool $skipProxy
     * @return string
     * @throws Exception
     */
    protected function getStrategyClass($fileType, $skipProxy = false)
    {
        $mode = $this->appState->getMode();
        if ($mode == State::MODE_PRODUCTION) {
            $strategyClasses = $this->strategies['production_mode'];
        } elseif (($mode == State::MODE_DEVELOPER) || $skipProxy) {
            $strategyClasses = $this->strategies['full_check'];
        } elseif ($mode == State::MODE_DEFAULT) {
            $strategyClasses = $this->strategies['caching_map'];
        } else {
            throw new Exception("Unknown mode to choose strategy: {$mode}");
        }
        return $strategyClasses[$fileType];
    }

    /**
     * Create strategy by its class name
     *
     * @param string $className
     * @return mixed
     */
    protected function createStrategy($className)
    {
        switch ($className) {
            case 'Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy':
                $mapDir = $this->dirs->getDir(Dir::VAR_DIR) . DIRECTORY_SEPARATOR
                    . self::FALLBACK_MAP_DIR;
                $arguments = array(
                    'mapDir' => str_replace('/', DIRECTORY_SEPARATOR, $mapDir),
                    'baseDir' => $this->dirs->getDir(Dir::ROOT),
                );
                break;
            default:
                $arguments = array();
                break;
        }
        return $this->objectManager->create($className, $arguments);
    }
}
