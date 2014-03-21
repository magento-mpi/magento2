<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution;

use Magento\App\Filesystem;
use Magento\View\Design\Fallback\Factory;
use Magento\View\Design\Fallback\Rule\RuleInterface;
use Magento\View\Design\ThemeInterface;
use Magento\Filesystem\Directory\Read;

/**
 * Class Fallback
 *
 * Resolver, which performs full search of files, according to fallback rules
 */
class Fallback
{
    /**
     * @var \Magento\View\Design\FileResolution\Fallback\Cache
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
     * Rule locale file
     *
     * @var RuleInterface
     */
    protected $ruleLocaleFile;

    /**
     * Rule view file
     *
     * @var RuleInterface
     */
    protected $ruleViewFile;

    /**
     * Root directory with read access
     *
     * @var Read
     */
    protected $rootDirectory;

    /**
     * @var array
     */
    private $staticExtensionRule;

    /**
     * Constructor
     *
     * @param \Magento\View\Design\FileResolution\Fallback\Cache $cache
     * @param Filesystem $filesystem
     * @param Factory $fallbackFactory
     * @param array $staticExtensionRule
     */
    public function __construct(
        \Magento\View\Design\FileResolution\Fallback\Cache $cache,
        Filesystem $filesystem,
        Factory $fallbackFactory,
        array $staticExtensionRule = array()
    ) {
        $this->cache = $cache;
        $this->rootDirectory = $filesystem->getDirectoryRead(Filesystem::ROOT_DIR);
        $this->fallbackFactory = $fallbackFactory;
        $this->staticExtensionRule = $staticExtensionRule;
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
        $result = $this->getFromCache('file', $file, $params);
        if (!$result) {
            $result = $this->resolveFile($this->getFileRule(), $file, $params);
            $this->saveToCache($result, 'file', $file, $params);
        }
        return $result;
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
        $result = $this->getFromCache('locale', $file, $params);
        if (!$result) {
            $result = $this->resolveFile($this->getLocaleFileRule(), $file, $params);
            $this->saveToCache($result, 'locale', $file, $params);
        }
        return $result;
    }

    /**
     * Get a static view file name, using fallback mechanism
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string|bool
     */
    public function getViewFile($area, ThemeInterface $themeModel, $locale, $file, $module = null)
    {
        $params = array(
            'area' => $area, 'theme' => $themeModel, 'locale' => $locale, 'namespace' => null, 'module' => null
        );
        if ($module) {
            list($params['namespace'], $params['module']) = explode('_', $module, 2);
        }
        $result = $this->getFromCache('view', $file, $params);
        if (!$result) {
            $rule = $this->getViewFileRule();
            $result = $this->resolveFile($rule, $file, $params);
            if (!$result) {
                $result = $this->lookupAdditionalExtensions($rule, $file, $params);
            }
            $this->saveToCache($result, 'view', $file, $params);
        }
        return $result;
    }

    /**
     * Retrieve cached file path
     *
     * @param string $type
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function getFromCache($type, $file, array $params)
    {
        $cacheId = $this->getCacheId($type, $file, $params);
        $path = $this->cache->load($cacheId);
        if ($path) {
            $path = $this->rootDirectory->getAbsolutePath($path);
        }
        return $path;
    }

    /**
     * Save calculated file path
     *
     * @param string $relativePath
     * @param string $type
     * @param string $file
     * @param array $params
     * @return bool
     */
    protected function saveToCache($relativePath, $type, $file, array $params)
    {
        $relativePath = $this->rootDirectory->getRelativePath($relativePath);
        $cacheId = $this->getCacheId($type, $file, $params);
        return $this->cache->save($relativePath, $cacheId);
    }

    /**
     * Generate cache ID
     *
     * @param string $type
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function getCacheId($type, $file, array $params)
    {
        return sprintf(
            "type:%s|area:%s|theme:%s|locale:%s|module:%s_%s|file:%s",
            $type,
            !empty($params['area']) ? $params['area'] : '',
            !empty($params['theme']) ? $params['theme']->getThemePath() : '',
            !empty($params['locale']) ? $params['locale'] : '',
            !empty($params['namespace']) ? $params['namespace'] : '',
            !empty($params['module']) ? $params['module'] : '',
            $file
        );
    }

    /**
     * Using additional rule for static view file extensions, lookup specified file with these extensions
     *
     * A first matched file with alternative extension will be returned
     *
     * @param $rule
     * @param $file
     * @param $params
     * @return string|bool
     */
    private function lookupAdditionalExtensions($rule, $file, $params)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (isset($this->staticExtensionRule[$extension])) {
            foreach ($this->staticExtensionRule[$extension] as $newExtension) {
                $newFile = substr($file, 0, strlen($file) - strlen($extension)) . $newExtension;
                $result = $this->resolveFile($rule, $newFile, $params);
                if ($result) {
                    return $result;
                }
            }
        }
        return false;
    }

    /**
     * Retrieve fallback rule for dynamic view files
     *
     * @return RuleInterface
     */
    protected function getFileRule()
    {
        if (!$this->ruleFile) {
            $this->ruleFile = $this->fallbackFactory->createFileRule();
        }
        return $this->ruleFile;
    }

    /**
     * Retrieve fallback rule for locale files
     *
     * @return RuleInterface
     */
    protected function getLocaleFileRule()
    {
        if (!$this->ruleLocaleFile) {
            $this->ruleLocaleFile = $this->fallbackFactory->createLocaleFileRule();
        }
        return $this->ruleLocaleFile;
    }

    /**
     * Retrieve fallback rule for static view files
     *
     * @return RuleInterface
     */
    protected function getViewFileRule()
    {
        if (!$this->ruleViewFile) {
            $this->ruleViewFile = $this->fallbackFactory->createViewFileRule();
        }
        return $this->ruleViewFile;
    }

    /**
     * Get path of file after using fallback rules
     *
     * @param RuleInterface $fallbackRule
     * @param string $file
     * @param array $params
     * @return string|bool
     */
    protected function resolveFile(RuleInterface $fallbackRule, $file, $params = array())
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
