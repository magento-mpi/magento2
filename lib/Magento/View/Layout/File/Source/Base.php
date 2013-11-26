<?php
/**
 * Source of base layout files introduced by modules
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source;

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
    protected $modulesDirectory;

    /**
     * @param Filesystem $filesystem
     * @param Factory $fileFactory
     */
    public function __construct(
        Filesystem $filesystem,
        Factory $fileFactory
    ) {
        $this->modulesDirectory = $filesystem->getDirectoryRead(Filesystem::MODULES);
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*')
    {
        $namespace = $module = '*';
        $area = $theme->getArea();
        $patternForSearch = str_replace(
            array('/', '\*'),
            array('\/', '[\S]+'),
            preg_quote("~{$namespace}/{$module}/view/{$area}/layout/{$filePath}.xml~")
        );
        $files = $this->modulesDirectory->search($patternForSearch);
        foreach ($files as $key => $file) {
            $files[$key] = $this->modulesDirectory->getAbsolutePath($file);
        }
        $pattern = "#(?<namespace>[^/]+)/(?<module>[^/]+)/view/"
            . preg_quote($area)
            . "/layout/"
            . preg_quote(rtrim($filePath, '*'))
            . "[^/]*\.xml$#i";
        $result = array();
        foreach ($files as $filename) {
            if (!preg_match($pattern, $filename, $matches)) {
                continue;
            }
            $moduleFull = "{$matches['namespace']}_{$matches['module']}";
            $result[] = $this->fileFactory->create($filename, $moduleFull);
        }
        return $result;
    }
}
