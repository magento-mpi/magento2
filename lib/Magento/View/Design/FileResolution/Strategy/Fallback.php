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
 * Resolver, which performs full search of files, according to fallback rules
 */
namespace Magento\View\Design\FileResolution\Strategy;

use Magento\Filesystem;
use Magento\View\Design\Fallback\Factory;
use Magento\View\Design\Fallback\Rule\RuleInterface;
use Magento\View\Design\ThemeInterface;
use Magento\Filesystem\DirectoryList;
use Magento\Filesystem\Directory\Read;

/**
 * Fallback
 *
 * @package Magento\View
 */
class Fallback implements FileInterface, LocaleInterface, ViewInterface
{
    /**
     * @var Factory
     */
    protected $fallbackFactory;

    /**
     * @var RuleInterface
     */
    protected $ruleFile;

    /**
     * @var RuleInterface
     */
    protected $ruleLocaleFile;

    /**
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
     * @param Filesystem $filesystem
     * @param Factory $fallbackFactory
     */
    public function __construct(Filesystem $filesystem, Factory $fallbackFactory)
    {
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::THEMES);
        $this->_filesystem = $filesystem;
        $this->fallbackFactory = $fallbackFactory;
    }

    /**
     * Get existing file name, using fallback mechanism
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $file
     * @param string|null $module
     * @return string
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
     * @return string
     */
    public function getLocaleFile($area, ThemeInterface $themeModel, $locale, $file)
    {
        $params = array('area' => $area, 'theme' => $themeModel, 'locale' => $locale);
        return $this->resolveFile($this->getLocaleFileRule(), $file, $params);
    }

    /**
     * Get theme file name, using fallback mechanism
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($area, ThemeInterface $themeModel, $locale, $file, $module = null)
    {
        $params = array(
            'area' => $area, 'theme' => $themeModel, 'locale' => $locale, 'namespace' => null, 'module' => null
        );
        if ($module) {
            list($params['namespace'], $params['module']) = explode('_', $module, 2);
        }
        return $this->resolveFile($this->getViewFileRule(), $file, $params);
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
     * @return string
     */
    protected function resolveFile(RuleInterface $fallbackRule, $file, $params = array())
    {
        $path = '';
        foreach ($fallbackRule->getPatternDirs($params) as $dir) {
            $path = str_replace('/', DIRECTORY_SEPARATOR, "{$dir}/{$file}");
            if ($this->rootDirectory->isExist($this->rootDirectory->getRelativePath($path))) {
                return $path;
            }
        }
        return $path;
    }
}
