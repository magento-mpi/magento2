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
 * Provider of static view files
 */
class ViewFile
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
     * Rule view file
     *
     * @var RuleInterface
     */
    protected $ruleViewFile;

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
     * @var array
     */
    private $staticExtensionRule;

    /**
     * Constructor
     *
     * @param CacheDataInterface $cache
     * @param Filesystem $filesystem
     * @param Factory $fallbackFactory
     * @param Resolver $resolver
     * @param array $staticExtensionRule
     * @throws \InvalidArgumentException
     */
    public function __construct(
        CacheDataInterface $cache,
        Filesystem $filesystem,
        Factory $fallbackFactory,
        Resolver $resolver,
        array $staticExtensionRule = array()
    ) {
        $this->cache = $cache;
        $this->rootDirectory = $filesystem->getDirectoryRead(Filesystem::ROOT_DIR);
        $this->fallbackFactory = $fallbackFactory;

        foreach ($staticExtensionRule as $extension => $newExtensions) {
            if (!is_string($extension) || !is_array($newExtensions)) {
                throw new \InvalidArgumentException("\$staticExtensionRule must be an array with format: "
                    . "array('ext1' => array('ext1', 'ext2'), 'ext3' => array(...)]");
            }
        }

        $this->staticExtensionRule = $staticExtensionRule;
        $this->resolver = $resolver;
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
        $path = $this->cache->getFromCache('view', $file, $params);
        if ($path) {
            $path = $this->rootDirectory->getAbsolutePath($path);
        } else {
            $rule = $this->getRule();
            $path = $this->resolver->resolveFile($this->rootDirectory, $rule, $file, $params);
            if (!$path) {
                $path = $this->lookupAdditionalExtensions($rule, $file, $params);
            }
            $this->cache->saveToCache($this->rootDirectory->getRelativePath($path), 'view', $file, $params);
        }
        return $path;
    }

    /**
     * Using additional rule for static view file extensions, lookup specified file with these extensions
     *
     * A first matched file with alternative extension will be returned
     *
     * @param RuleInterface $rule
     * @param string $file
     * @param array $params
     * @return string|bool
     */
    protected function lookupAdditionalExtensions(RuleInterface $rule, $file, array $params)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (isset($this->staticExtensionRule[$extension])) {
            foreach ($this->staticExtensionRule[$extension] as $newExtension) {
                $newFile = substr($file, 0, strlen($file) - strlen($extension)) . $newExtension;
                $result = $this->resolver->resolveFile($this->rootDirectory, $rule, $newFile, $params);
                if ($result) {
                    return $result;
                }
            }
        }
        return false;
    }

    /**
     * Retrieve fallback rule
     *
     * @return RuleInterface
     */
    protected function getRule()
    {
        if (!$this->ruleViewFile) {
            $this->ruleViewFile = $this->fallbackFactory->createViewFileRule();
        }
        return $this->ruleViewFile;
    }
}
