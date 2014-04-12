<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback\Resolver;

use Magento\App\Filesystem;
use Magento\View\Design\FileResolution\Fallback;
use Magento\View\Design\Fallback\Rule\RuleInterface;
use Magento\View\Design\Fallback\RulePool;
use Magento\Filesystem\Directory\ReadInterface;
use Magento\View\Design\ThemeInterface;

/**
 * Resolver for view files
 */
class Simple implements Fallback\ResolverInterface
{
    /**
     * @var ReadInterface
     */
    protected $rootDirectory;

    /**
     * Fallback factory
     *
     * @var RulePool
     */
    protected $rulePool;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param RulePool $rulePool
     * @param Fallback\CacheDataInterface $cache
     */
    public function __construct(Filesystem $filesystem, RulePool $rulePool, Fallback\CacheDataInterface $cache)
    {
        $this->rootDirectory = $filesystem->getDirectoryRead(Filesystem::ROOT_DIR);
        $this->rulePool = $rulePool;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($type, $file, $area = null, ThemeInterface $theme = null, $locale = null, $module = null)
    {
        $themePath = $theme ? $theme->getThemePath() : '';
        $path = $this->cache->getFromCache($type, $file, $area, $themePath, $locale, $module);
        if (false !== $path) {
            $path = $path ? $this->rootDirectory->getAbsolutePath($path) : false;
        } else {
            $params = [];
            if (!empty($area)) {
                $params['area'] = $area;
            }
            if (!empty($theme)) {
                $params['theme'] = $theme;
            }
            if (!empty($locale)) {
                $params['locale'] = $locale;
            }
            if (!empty($namespace)) {
                $params['namespace'] = $namespace;
            }
            if (!empty($module)) {
                list($params['namespace'], $params['module']) = explode('_', $module, 2);
            }
            $path = $this->resolveFile($this->rulePool->getRule($type), $file, $params);
            $cachedValue = $path ? $this->rootDirectory->getRelativePath($path) : '';

            $this->cache->saveToCache($cachedValue, $type, $file, $area, $themePath, $locale, $module);
        }
        return $path;
    }

    /**
     * Get path of file after using fallback rules
     *
     * @param RuleInterface $fallbackRule
     * @param string $file
     * @param array $params
     * @return string|bool
     */
    protected function resolveFile(RuleInterface $fallbackRule, $file, array $params = array())
    {
        foreach ($fallbackRule->getPatternDirs($params) as $dir) {
            $path = "{$dir}/{$file}";
            if ($this->rootDirectory->isExist($this->rootDirectory->getRelativePath($path))) {
                return $path;
            }
        }
        return false;
    }
}
