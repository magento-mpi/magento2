<?php
/**
 * Routes configuration converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Route\Config;

class Converter implements \Magento\Config\ConverterInterface
{
    /**
     * Convert config
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();

        /** @var \DOMNodeList $routers */
        $routers = $source->getElementsByTagName('router');

        /** @var \DOMNode $router */
        foreach ($routers as $router) {
            $routerConfig = array();
            foreach ($router->attributes as $attribute) {
                $routerConfig[$attribute->nodeName] = $attribute->nodeValue;
            }

            /** @var \DOMNode $routeData */
            foreach ($router->getElementsByTagName('route') as $routeData) {
                $routeConfig = array();
                foreach ($routeData->attributes as $routeAttribute) {
                    $routeConfig[$routeAttribute->nodeName] = $routeAttribute->nodeValue;
                }

                /** @var \DOMNode $module */
                foreach ($routeData->getElementsByTagName('module') as $moduleData) {
                    $moduleConfig = array();
                    foreach ($moduleData->attributes as $moduleAttribute) {
                        $moduleConfig[$moduleAttribute->nodeName] = $moduleAttribute->nodeValue;
                    }
                    $routeConfig['modules'][] = $moduleConfig;
                }
                $routeConfig['modules'] = $this->_sortModulesList($routeConfig['modules']);
                $routerConfig['routes'][$routeData->attributes->getNamedItem('id')->nodeValue] = $routeConfig;
            }

            $output[$router->attributes->getNamedItem('id')->nodeValue] = $routerConfig;
        }

        return $output;
    }

    /**
     * Sort modules list according to before/after attributes
     *
     * @param array $modulesList
     * @return array
     */
    protected function _sortModulesList($modulesList)
    {
        $sortedModulesList = array();

        foreach ($modulesList as $moduleData) {
            if (isset($moduleData['before'])) {
                $position = array_search($moduleData['before'], $sortedModulesList);
                if ($position === false) {
                    $position = 0;
                }
                array_splice($sortedModulesList, $position, 0, $moduleData['name']);
            } elseif (isset($moduleData['after'])) {
                $position = array_search($moduleData['after'], $sortedModulesList);
                if ($position === false) {
                    $position = count($modulesList);
                }
                array_splice($sortedModulesList, $position+1, 0, $moduleData['name']);
            } else {
                $sortedModulesList[] = $moduleData['name'];
            }
        }

        return $sortedModulesList;
    }
}
