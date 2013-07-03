<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class PluginScanner implements ScannerInterface
{
    /**
     * Get array of class names
     *
     * @param array $files
     * @return array
     */
    public function collectEntities(array $files)
    {
        $pluginClassNames = array();
        foreach ($files as $fileName) {
            $xml = simplexml_load_file($fileName);
            foreach ($xml->xpath('//di/*/plugins/*/instance') as $node) {
                $pluginClassNames[] = (string) $node;
            }
        }
        return $pluginClassNames;
    }
}
