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
        foreach ($xpath->query('//type[plugin]|//virtualType[plugin]') as $entityNode) {
            $attributes = $entityNode->attributes;
            $type = $attributes->getNamedItem('type');
            if (!is_null($type)) {
                array_push($output, $type->nodeValue);
            } else {
                array_push($output, $attributes->getNamedItem('name')->nodeValue);
            }
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
            // @todo the controller handling logic below must be removed when controllers become PSR-0 compliant
            $controllerSuffix = 'Controller';
            $pathParts = explode('_', $entityName);
            if (strrpos($entityName, $controllerSuffix) === strlen($entityName) - strlen($controllerSuffix)
                && isset($pathParts[2])
                && !in_array($pathParts[2], array('Block', 'Helper', 'Model'))
            ) {
                $this->_handleControllerClassName($entityName);
            }
            if (class_exists($entityName) || interface_exists($entityName)) {
                array_push($filteredEntities, $entityName . '_Interceptor');
            }
        }
        return $filteredEntities;
    }

    /**
     * Include file with controller declaration if needed
     *
     * @param string $className
     * @todo this method must be removed when controllers become PSR-0 compliant
     */
    protected function _handleControllerClassName($className)
    {
        if (!class_exists($className)) {
            $className = preg_replace('/[^a-zA-Z0-9_]/', '', $className);
            $className = preg_replace('/^([0-9A-Za-z]*)_([0-9A-Za-z]*)/', '\\1_\\2_controllers', $className);
            $filePath = stream_resolve_include_path(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
            if (file_exists($filePath)) {
                require_once $filePath;
            }
        }
    }
}
