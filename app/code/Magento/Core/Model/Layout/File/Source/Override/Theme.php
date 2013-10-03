<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of layout files that explicitly override files of ancestor themes
 */
namespace Magento\Core\Model\Layout\File\Source\Override;

class Theme implements \Magento\Core\Model\Layout\File\SourceInterface
{
    /**
     * @var \Magento\Filesystem
     */
    private $_filesystem;

    /**
     * @var \Magento\Core\Model\Dir
     */
    private $_dirs;

    /**
     * @var \Magento\Core\Model\Layout\File\Factory
     */
    private $_fileFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Dir $dirs
     * @param \Magento\Core\Model\Layout\File\Factory $fileFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Dir $dirs,
        \Magento\Core\Model\Layout\File\Factory $fileFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(\Magento\Core\Model\ThemeInterface $theme)
    {
        $namespace = $module = '*';
        $themePath = $theme->getFullPath();
        $files = $this->_filesystem->searchKeys(
            $this->_dirs->getDir(\Magento\Core\Model\Dir::THEMES),
            "{$themePath}/{$namespace}_{$module}/layout/override/*/*.xml"
        );

        if (empty($files)) {
            return array();
        }

        $themes = array();
        $currentTheme = $theme;
        while ($currentTheme = $currentTheme->getParentTheme()) {
            $themes[$currentTheme->getCode()] = $currentTheme;
        }

        $result = array();
        foreach ($files as $filename) {
            if (preg_match("#([^/]+)/layout/override/([^/]+)/[^/]+\.xml$#i", $filename, $matches)) {
                $moduleFull = $matches[1];
                $ancestorThemeCode = $matches[2];
                if (!isset($themes[$ancestorThemeCode])) {
                    throw new \Magento\Core\Exception(sprintf(
                        "Trying to override layout file '%s' for theme '%s', which is not ancestor of theme '%s'",
                        $filename, $ancestorThemeCode, $theme->getCode()
                    ));
                }
                $result[] = $this->_fileFactory->create($filename, $moduleFull, $themes[$ancestorThemeCode]);
            }
        }
        return $result;
    }
}
