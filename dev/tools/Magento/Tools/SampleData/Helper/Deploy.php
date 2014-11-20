<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Tools\SampleData\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use SebastianBergmann\Exporter\Exception;

class Deploy
{
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @param DirectoryList $directoryList
     */
    public function __construct(DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * Launch deploy media process
     *
     * @throws \Exception
     * @return void
     */
    public function run()
    {
        $vendorPathConfig = $this->directoryList->getPath(DirectoryList::CONFIG) . '/vendor_path.php';
        if (!file_exists($vendorPathConfig)) {
            return;
        }
        $vendorPath = include($vendorPathConfig);
        $vendorsMagentoDir = $this->directoryList->getPath(DirectoryList::ROOT) . '/' . $vendorPath . '/magento';
        if (!file_exists($vendorsMagentoDir)) {
            return;
        }
        $vendorsMagentoMedia = $vendorsMagentoDir . '/sample-data-media';
        if (file_exists($vendorsMagentoMedia)) {
            $mediaDir = $this->directoryList->getPath(DirectoryList::MEDIA);
            $this->copyAll($vendorsMagentoMedia, $mediaDir, array('/composer.json', '/.git'));
        }
        $vendorsMagentoTheme = $vendorsMagentoDir . '/sample-data-styles';
        if (file_exists($vendorsMagentoTheme)) {
            $themesDir = $this->directoryList->getPath(DirectoryList::THEMES);
            $this->copyAll($vendorsMagentoTheme, $themesDir, array('/composer.json', '/.git'));
        }
    }

    /**
     * Copy all files maintaining the directory structure except excluded
     *
     * @param string $from
     * @param string $to
     * @param array $exclude
     * @return void
     */
    protected function copyAll($from, $to, $exclude = array())
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($from));
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $source = $file->getPathname();
                $relative = substr($source, strlen($from));
                if ($this->isExcluded($relative, $exclude)) {
                    continue;
                }
                $target = $to . $relative;
                if (file_exists($target) && md5_file($source) == md5_file($target)) {
                    continue;
                }
                $targetDir = dirname($target);
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                copy($source, $target);
            }
        }
    }

    /**
     * @param string $path
     * @param array $exclude
     * @return bool
     */
    protected function isExcluded($path, $exclude)
    {
        $pathNormalized = str_replace('\\', '/', $path);

        foreach($exclude as $item) {
            if (strpos($pathNormalized, $item) !== false) {
                return true;
            }
        }

        return false;
    }
}
