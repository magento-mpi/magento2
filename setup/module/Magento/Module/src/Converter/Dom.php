<?php
/**
 * Module declaration xml converter. Converts declaration DOM Document to internal array representation.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Module\Converter;

use Magento\Config\Converter\ConverterInterface;

class Dom implements ConverterInterface
{
    /**
     * Convert config
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \Exception
     */
    public function convert($source)
    {
        $modules = array();
        $xpath = new \DOMXPath($source);
        /** @var $moduleNode \DOMNode */
        foreach ($xpath->query('/config/module') as $moduleNode) {
            $moduleData = array();
            $moduleAttributes = $moduleNode->attributes;
            $nameNode = $moduleAttributes->getNamedItem('name');
            if (is_null($nameNode)) {
                throw new \Exception('Attribute "name" is required for module node.');
            }
            $moduleData['name'] = $nameNode->nodeValue;
            $versionNode = $moduleAttributes->getNamedItem('version');
            if (is_null($versionNode)) {
                throw new \Exception('Attribute "version" is required for module node.');
            }
            $moduleData['version'] = $versionNode->nodeValue;
            $activeNode = $moduleAttributes->getNamedItem('active');
            if (is_null($activeNode)) {
                throw new \Exception('Attribute "active" is required for module node.');
            }
            $moduleData['active'] = $activeNode->nodeValue == 'false' ? false : true;
            $moduleData['dependencies'] = array(
                'modules' => array(),
                'extensions' => array('strict' => array(), 'alternatives' => array())
            );
            /** @var $childNode \DOMNode */
            foreach ($moduleNode->childNodes as $childNode) {
                switch ($childNode->nodeName) {
                    case 'depends':
                        $moduleData['dependencies'] = array_merge(
                            $moduleData['dependencies'],
                            $this->convertExtensionDependencies($childNode)
                        );
                        break;
                    case 'sequence':
                        $moduleData['dependencies'] = array_merge(
                            $moduleData['dependencies'],
                            $this->convertModuleDependencies($childNode)
                        );
                        break;
                }
            }
            // Use module name as a key in the result array to allow quick access to module configuration
            $modules[$nameNode->nodeValue] = $moduleData;
        }
        return $modules;
    }

    /**
     * Convert extension depends node into assoc array
     *
     * @param \DOMNode $dependsNode
     * @return array
     * @throws \Exception
     */
    protected function convertExtensionDependencies(\DOMNode $dependsNode)
    {
        $dependencies = array('extensions' => array('strict' => array(), 'alternatives' => array()));
        /** @var $childNode \DOMNode */
        foreach ($dependsNode->childNodes as $childNode) {
            switch ($childNode->nodeName) {
                case 'extension':
                    $dependencies['extensions']['strict'][] = $this->convertExtensionNode($childNode);
                    break;
                case 'choice':
                    $alternatives = array();
                    /** @var $extensionNode \DOMNode */
                    foreach ($childNode->childNodes as $extensionNode) {
                        switch ($extensionNode->nodeName) {
                            case 'extension':
                                $alternatives[] = $this->convertExtensionNode($extensionNode);
                                break;
                        }
                    }
                    if (empty($alternatives)) {
                        throw new \Exception('Node "choice" cannot be empty.');
                    }
                    $dependencies['extensions']['alternatives'][] = $alternatives;
                    break;
            }
        }
        return $dependencies;
    }

    /**
     * Convert extension node into assoc array
     *
     * @param \DOMNode $extensionNode
     * @return array
     * @throws \Exception
     */
    protected function convertExtensionNode(\DOMNode $extensionNode)
    {
        $extensionData = array();
        $nameNode = $extensionNode->attributes->getNamedItem('name');
        if (is_null($nameNode)) {
            throw new \Exception('Attribute "name" is required for extension node.');
        }
        $extensionData['name'] = $nameNode->nodeValue;
        $minVersionNode = $extensionNode->attributes->getNamedItem('minVersion');
        if (!is_null($minVersionNode)) {
            $extensionData['minVersion'] = $minVersionNode->nodeValue;
        }
        return $extensionData;
    }

    /**
     * Convert module depends node into assoc array
     *
     * @param \DOMNode $dependsNode
     * @return array
     * @throws \Exception
     */
    protected function convertModuleDependencies(\DOMNode $dependsNode)
    {
        $dependencies = array('modules' => array());
        /** @var $childNode \DOMNode */
        foreach ($dependsNode->childNodes as $childNode) {
            switch ($childNode->nodeName) {
                case 'module':
                    $nameNode = $childNode->attributes->getNamedItem('name');
                    if (is_null($nameNode)) {
                        throw new \Exception('Attribute "name" is required for module node.');
                    }
                    $dependencies['modules'][] = $nameNode->nodeValue;
                    break;
            }
        }
        return $dependencies;
    }
}
