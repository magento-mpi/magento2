<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Helper;

/**
 * Class for Zipping Components
 */
class Zip
{

    /**
     * Zip Components
     *
     * @param string $source
     * @param string $destination
     * @return int
     */
    public static function zip($source, $destination, array $excludes)
    {
        $noOfZips = 0;

        if (!extension_loaded('zip') || !file_exists($source)) {
            return $noOfZips;
        }

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
            return $noOfZips;
        }

        $source = str_replace('\\', '/', realpath($source));
        $splitSourcePath = explode('/', $source);
        $sourceName = $splitSourcePath[count($splitSourcePath)-1];

        if (is_dir($source) === true) {
            $files = Zip::getFiles($source, $excludes);

            $noOfZips += sizeof($files);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', realpath($file));

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/')+1), array('.', '..'))) {
                    continue;
                }
                //Ignoring Magento Framework for lib folder
                if (($sourceName === 'lib') && strpos($file, 'lib/Magento')) {
                    continue;
                }
                if (is_dir($file) === true) {
                    $str = str_replace($source . '/', '', $file . '/');
                    $zip->addEmptyDir($str);
                } else if (is_file($file) === true) {
                    $str = str_replace($source . '/', '', $file);
                    $zip->addFromString($str, file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
            $noOfZips++;
        }

        $zip->close();
        return $noOfZips;
    }

    public static function getFiles($source, $excludes)
    {
        if (sizeof($excludes) == 0) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source),
                \RecursiveIteratorIterator::SELF_FIRST
            );
        } else {
            $directory = new \RecursiveDirectoryIterator($source);
            $filtered = new ExcludeFilter($directory, $excludes);
            $files = new \RecursiveIteratorIterator($filtered, \RecursiveIteratorIterator::SELF_FIRST);
        }

        return $files;
    }
}
