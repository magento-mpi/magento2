<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File\Source;

use Magento\View\File\SourceInterface;
use Magento\View\Design\ThemeInterface;
use Magento\App\Filesystem;
use Magento\Filesystem\Directory\ReadInterface;
use Magento\View\File\Factory;

/**
 * Source of base layout files introduced by modules
 */
class Base implements SourceInterface
{
    /**
     * @var Factory
     */
    protected $fileFactory;

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
        $this->modulesDirectory = $filesystem->getDirectoryRead(Filesystem::MODULES_DIR);
        $this->fileFactory = $fileFactory;
    }

    /**
     * Retrieve files
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return array|\Magento\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*')
    {
        //Import module base styles

        $filePath = pathinfo($filePath, PATHINFO_EXTENSION) ? $filePath : rtrim($filePath, '.') . '.less';

        $namespace = $module = '*';
        $area = $theme->getArea();
        $files = $this->modulesDirectory->search("$namespace/$module/view/{$area}/{$filePath}");
        $result = array();
        $filePath = strtr(preg_quote($filePath), array('\*' => '[^/]+'));
        $pattern = "#(?<namespace>[^/]+)/(?<module>[^/]+)/view/{$area}/" . $filePath . "$#i";
        foreach ($files as $file) {
            $filename = $this->modulesDirectory->getAbsolutePath($file);
            if (!preg_match($pattern, $filename, $matches)) {
                continue;
            }
            $moduleFull = "{$matches['namespace']}_{$matches['module']}";
            $result[] = $this->fileFactory->create($filename, $moduleFull, $theme);
        }
        return $result;
    }
}
