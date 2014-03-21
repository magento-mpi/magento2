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
            $dom = new \DOMDocument();
            $dom->loadXML(file_get_contents($fileName));
            $xpath = new \DOMXPath($dom);
            /** @var $node \DOMNode */
            foreach ($xpath->query('//type/plugin|//virtualType/plugin') as $node) {
                $pluginTypeNode = $node->attributes->getNamedItem('type');
                if (!is_null($pluginTypeNode)) {
                    $pluginClassNames[] = $pluginTypeNode->nodeValue;
                }
            }
        }
        return $pluginClassNames;
    }
}
