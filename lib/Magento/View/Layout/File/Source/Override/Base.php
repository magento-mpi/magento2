<?php
/**
 * Source of layout files that explicitly override base files introduced by modules
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source\Override;

use Magento\View\Layout\File\SourceInterface;
use Magento\View\Design\ThemeInterface;
use Magento\Filesystem;
use Magento\Filesystem\Directory\ReadInterface;
use Magento\View\Layout\File\Factory;

class Base implements SourceInterface
{
    /**
     * @var Factory
     */
    private $fileFactory;

    /**
     * @var ReadInterface
     */
    protected $themesDirectory;

    /**
     * @param Filesystem $filesystem
     * @param Factory $fileFactory
     */
    public function __construct(
        Filesystem $filesystem,
        Factory $fileFactory
    ) {
        $this->themesDirectory = $filesystem->getDirectoryRead(Filesystem::THEMES);
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*')
    {
        $namespace = $module = '*';
        $themePath = $theme->getFullPath();
        $patternForSearch = str_replace(
            array('/', '\*'),
            array('\/', '[\S]+'),
            preg_quote("~{$themePath}/{$namespace}_{$module}/layout/override/base/{$filePath}.xml~")
        );
        $files = $this->themesDirectory
            ->search($patternForSearch);
        foreach ($files as $key => $file) {
            $files[$key] = $this->themesDirectory->getAbsolutePath($file);
        }
        $result = array();
        $pattern = "#/(?<moduleName>[^/]+)/layout/override/base/"
            . preg_quote(rtrim($filePath, '*'))
            . "[^/]*\.xml$#i";
        foreach ($files as $filename) {
            if (!preg_match($pattern, $filename, $matches)) {
                continue;
            }
            $result[] = $this->fileFactory->create($filename, $matches['moduleName']);
        }
        return $result;
    }
}
