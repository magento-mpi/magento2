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
namespace Magento\Core\Model\Design\FileResolution\Strategy;

class Fallback
    implements \Magento\Core\Model\Design\FileResolution\Strategy\FileInterface,
    \Magento\Core\Model\Design\FileResolution\Strategy\LocaleInterface,
    \Magento\Core\Model\Design\FileResolution\Strategy\ViewInterface
{
    /**
     * @var \Magento\Core\Model\Design\Fallback\Factory
     */
    protected $_fallbackFactory;

    /**
     * @var \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    protected $_ruleFile;

    /**
     * @var \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    protected $_ruleLocaleFile;

    /**
     * @var \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    protected $_ruleViewFile;

    /**
     * Constructor
     *
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Design\Fallback\Factory $fallbackFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Design\Fallback\Factory $fallbackFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_fallbackFactory = $fallbackFactory;
    }

    /**
     * Get existing file name, using fallback mechanism
     *
     * @param string $area
     * @param \Magento\View\Design\ThemeInterface $themeModel
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($area, \Magento\View\Design\ThemeInterface $themeModel, $file, $module = null)
    {
        $params = array('area' => $area, 'theme' => $themeModel, 'namespace' => null, 'module' => null);
        if ($module) {
            list($params['namespace'], $params['module']) = explode('_', $module, 2);
        }
        return $this->_resolveFile($this->_getFileRule(), $file, $params);
    }

    /**
     * Get locale file name, using fallback mechanism
     *
     * @param string $area
     * @param \Magento\View\Design\ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @return string
     */
    public function getLocaleFile($area, \Magento\View\Design\ThemeInterface $themeModel, $locale, $file)
    {
        $params = array('area' => $area, 'theme' => $themeModel, 'locale' => $locale);
        return $this->_resolveFile($this->_getLocaleFileRule(), $file, $params);
    }

    /**
     * Get theme file name, using fallback mechanism
     *
     * @param string $area
     * @param \Magento\View\Design\ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($area, \Magento\View\Design\ThemeInterface $themeModel, $locale, $file, $module = null)
    {
        $params = array(
            'area' => $area, 'theme' => $themeModel, 'locale' => $locale, 'namespace' => null, 'module' => null
        );
        if ($module) {
            list($params['namespace'], $params['module']) = explode('_', $module, 2);
        }
        return $this->_resolveFile($this->_getViewFileRule(), $file, $params);
    }

    /**
     * Retrieve fallback rule for dynamic view files
     *
     * @return \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    protected function _getFileRule()
    {
        if (!$this->_ruleFile) {
            $this->_ruleFile = $this->_fallbackFactory->createFileRule();
        }
        return $this->_ruleFile;
    }

    /**
     * Retrieve fallback rule for locale files
     *
     * @return \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    protected function _getLocaleFileRule()
    {
        if (!$this->_ruleLocaleFile) {
            $this->_ruleLocaleFile = $this->_fallbackFactory->createLocaleFileRule();
        }
        return $this->_ruleLocaleFile;
    }

    /**
     * Retrieve fallback rule for static view files
     *
     * @return \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    protected function _getViewFileRule()
    {
        if (!$this->_ruleViewFile) {
            $this->_ruleViewFile = $this->_fallbackFactory->createViewFileRule();
        }
        return $this->_ruleViewFile;
    }

    /**
     * Get path of file after using fallback rules
     *
     * @param \Magento\Core\Model\Design\Fallback\Rule\RuleInterface $fallbackRule
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function _resolveFile(
        \Magento\Core\Model\Design\Fallback\Rule\RuleInterface $fallbackRule, $file, $params = array()
    ) {
        $path = '';
        foreach ($fallbackRule->getPatternDirs($params) as $dir) {
            $path = str_replace('/', DIRECTORY_SEPARATOR, "{$dir}/{$file}");
            if ($this->_filesystem->has($path)) {
                return $path;
            }
        }
        return $path;
    }
}
