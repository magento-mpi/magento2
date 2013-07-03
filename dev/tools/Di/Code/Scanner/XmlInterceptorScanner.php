<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class XmlInterceptorScanner implements ScannerInterface
{
    /**
     * Get array of interceptor class names
     *
     * @param array $files
     * @return array
     */
    public function collectEntities(array $files)
    {
        $output = array();
        foreach ($files as $file) {
            $output = array_merge($output, $this->_collectEntitiesFromString(file_get_contents($file)));
        }
        $output = array_unique($output);
        $output = $this->_filterEntities($output);
        return $output;
    }

    /**
     * Collect entities from XML string
     *
     *
     * @param string $content
     * @return array
     */
    protected function _collectEntitiesFromString($content)
    {
        $output = array();
        $dom = new \DOMDocument();
        $dom->loadXML($content);
        $xpath = new \DOMXPath($dom);
        /** @var $entityNode \DOMNode */
        foreach ($xpath->query('//*[not (type) and plugins]') as $entityNode) {
            array_push($output, $entityNode->nodeName);
        }
        /** @var $typeNode \DOMNode */
        foreach ($xpath->query('//*[type and plugins]/type') as $typeNode) {
            array_push($output, $typeNode->nodeValue);
        }
        return $output;
    }

    /**
     * Filter found entities if needed
     *
     * @param array $output
     * @return array
     */
    protected function _filterEntities(array $output)
    {
        $filteredEntities = array();
        foreach ($output as $entityName) {
            if (class_exists($entityName) || interface_exists($entityName)) {
                array_push($filteredEntities, $entityName . '_Interceptor');
            }
        }
        return $filteredEntities;
    }
}
