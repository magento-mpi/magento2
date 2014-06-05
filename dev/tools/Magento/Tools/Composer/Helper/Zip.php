<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Helper;

class Zip
{

    public static function Zip($source, $destination)
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
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source),
                \RecursiveIteratorIterator::SELF_FIRST);
            $noOfZips += sizeof($files);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', realpath($file));

                // Ignore "." and ".." folders
                if ( in_array(substr($file, strrpos($file, '/')+1), array('.', '..'))) {
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
}