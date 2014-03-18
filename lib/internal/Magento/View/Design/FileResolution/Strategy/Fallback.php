<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Strategy;

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
class Fallback implements FileInterface, LocaleInterface, ViewInterface
{
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
     * @param Filesystem $filesystem
     * @param Factory $fallbackFactory
     * @param array $staticExtensionRule
     */
    public function __construct(
        Filesystem $filesystem,
        Factory $fallbackFactory,
        array $staticExtensionRule = array()
    ) {
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
        return $this->resolveFile($this->getFileRule(), $file, $params);
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
        return $this->resolveFile($this->getLocaleFileRule(), $file, $params);
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
        $rule = $this->getViewFileRule();
        $result = $this->resolveFile($rule, $file, $params);
        if (!$result) {
            $result = $this->lookupAdditionalExtensions($rule, $file, $params);
        }
        return $result;
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
