<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class DirectoryScanner
{
    /**
     * @param $dir
     * @param array $extensions
     * @return array
     */
    public function scan($dir, array $extensions = array())
    {
        $output = array();
        /** @var $file \DirectoryIterator */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $file) {
            if (!$file->isDir() && in_array($file->getExtension(), $extensions)) {
                $output[$file->getExtension()][] = $file->getRealPath();
            }
        }

        return $output;
    }
}
