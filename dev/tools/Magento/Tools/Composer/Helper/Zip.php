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
     * @param string $excludes
     * @return int
     */
    public static function Zip($source, $destination, array $excludes)
    {
        $noOfZips = 0;

        try{
            if (!extension_loaded('zip') || !file_exists($source)) {
                throw new \Exception("Error while ziiping.", "1");
            }

            $zip = new \ZipArchive();
            if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
                throw new \Exception("Error while ziiping.", "1");
            }

            if (is_dir($source) === true) {
                $files = Zip::getFiles($source, $excludes);
                foreach ($files as $file) {
                    $file = str_replace('\\', '/', realpath($file));
                    if ( in_array(substr($file, strrpos($file, '/')+1), array('.', '..'))) {
                        continue;
                    }
                    $relativePath = str_replace($source . '/', '', $file);
                    if (is_dir($file) === true) {
                        $relativePath .= '/';
                        $zip->addEmptyDir($relativePath);
                    } else if (is_file($file) === true) {
                        $zip->addFromString($relativePath, file_get_contents($file));
                    } else {
                        throw new \Exception("The path $file is not a directory or file!", "1");
                    }
                }
                $noOfZips += sizeof($files);
            } else if (is_file($source) === true) {
                $zip->addFromString(basename($source), file_get_contents($source));
                $noOfZips++;
            }

            $zip->close();
            return $noOfZips;
        } catch(\Exception $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Creating the iterator for zipping
     *
     * @param string $source
     * @param string $excludes
     * @return RecursiveIteratorIterator
     */
    private static function getFiles($source, $excludes)
    {
        $directory = new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS);
        if (sizeof($excludes) > 0) {
            $directory = new ExcludeFilter($directory, $excludes);
        }
        $files = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);

        return $files;
    }
}