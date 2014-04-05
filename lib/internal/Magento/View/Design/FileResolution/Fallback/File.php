<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

use Magento\App\Filesystem;
use Magento\View\Design\Fallback\Factory;
use Magento\View\Design\Fallback\Rule\RuleInterface;
use Magento\View\Design\ThemeInterface;
use Magento\Filesystem\Directory\Read;

/**
 * Provider of dynamic view files
 */
class File
{
    /**
     * @var CacheDataInterface
     */
    private $cache;

    /**
     * Fallback factory
     *
     * @var Factory
     */
    protected $fallbackFactory;

    /**
     * Rule file
     *
     * @var RuleInterface
     */
    protected $ruleFile;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * Root directory with read access
     *
     * @var Read
     */
    protected $rootDirectory;

    /**
     * Constructor
     *
     * @param CacheDataInterface $cache
     * @param Filesystem $filesystem
     * @param Factory $fallbackFactory
     * @param Resolver $resolver
     */
    public function __construct(
        CacheDataInterface $cache,
        Filesystem $filesystem,
        Factory $fallbackFactory,
        Resolver $resolver
    ) {
        $this->cache = $cache;
        $this->rootDirectory = $filesystem->getDirectoryRead(Filesystem::ROOT_DIR);
        $this->fallbackFactory = $fallbackFactory;
        $this->resolver = $resolver;
    }

    /**
     * Get existing file name, using fallback mechanism
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $file
     * @param string|null $module
     * @return string|bool
     */
    public function getFile($area, ThemeInterface $themeModel, $file, $module = null)
    {
        $params = array('area' => $area, 'theme' => $themeModel, 'namespace' => null, 'module' => null);
        if ($module) {
            list($params['namespace'], $params['module']) = explode('_', $module, 2);
        }
        $path = $this->cache->getFromCache('file', $file, $params);
        if ($path) {
            $path = $this->rootDirectory->getAbsolutePath($path);
        } else {
            $path = $this->resolver->resolveFile($this->rootDirectory, $this->getRule(), $file, $params);
            $this->cache->saveToCache($this->rootDirectory->getRelativePath($path), 'file', $file, $params);
        }
        return $path;
    }

    /**
     * Retrieve fallback rule
     *
     * @return RuleInterface
     */
    protected function getRule()
    {
        if (!$this->ruleFile) {
            $this->ruleFile = $this->fallbackFactory->createFileRule();
        }
        return $this->ruleFile;
    }
}
