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
     * Scan directory
     *
     * @param string $dir
     * @param array $patterns
     * @return array
     */
    public function scan($dir, array $patterns = array())
    {
        $output = array();
        /** @var $file \DirectoryIterator */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $file) {
            if ($file->isDir()) {
                continue;
            }

            foreach ($patterns as $type => $pattern) {
                $filePath = str_replace('\\', '/', $file->getRealPath());
                if (preg_match($pattern, $filePath)) {
                    $output[$type][] = $filePath;
                    break;
                }
            }
        }
        return $output;
    }
}
