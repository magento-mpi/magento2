<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class XmlScanner extends FileScanner
{
    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $_pattern = '/[\n\'"<>]{1}([A-Z]{1}[a-zA-Z0-9]*[_\\\\][A-Z]{1}[a-zA-Z0-9_\\\\]*(Proxy|Factory))[\n\'"<>]{1}/';

    /**
     * Prepare xml file content
     *
     * @param string $content
     * @return string
     */
    protected function _prepareContent($content)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($content);

        $xpath = new \DOMXPath($dom);
        /** @var $comment \DOMComment */
        foreach ($xpath->query('//comment()') as $comment) {
            $comment->parentNode->removeChild($comment);
        }
        $output = $dom->saveXML();

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
        foreach ($output as $className) {
            $entityName = rtrim(preg_replace('/(Proxy|Factory)$/', '', $className), '\\');
            // Skip aliases that are declared in Di configuration
            if (class_exists($entityName)) {
                array_push($filteredEntities, $className);
            }
        }
        return $filteredEntities;
    }
}
