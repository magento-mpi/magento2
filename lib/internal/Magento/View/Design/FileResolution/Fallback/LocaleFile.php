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
 * Provider of localized view files
 */
class LocaleFile
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
     * Rule locale file
     *
     * @var RuleInterface
     */
    protected $ruleLocaleFile;

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
     * Get locale file name, using fallback mechanism
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @return string|bool
     */
    public function getLocaleFile($area, ThemeInterface $themeModel, $locale, $file)
    {
        $params = array('area' => $area, 'theme' => $themeModel, 'locale' => $locale);
        $path = $this->cache->getFromCache('locale', $file, $params);
        if (false !== $path) {
            $path = $path ? $this->rootDirectory->getAbsolutePath($path) : false;
        } else {
            $path = $this->resolver->resolveFile($this->rootDirectory, $this->getRule(), $file, $params);
            $cachedValue = $path ? $this->rootDirectory->getRelativePath($path) : '';
            $this->cache->saveToCache($cachedValue, 'locale', $file, $params);
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
        if (!$this->ruleLocaleFile) {
            $this->ruleLocaleFile = $this->fallbackFactory->createLocaleFileRule();
        }
        return $this->ruleLocaleFile;
    }
}
