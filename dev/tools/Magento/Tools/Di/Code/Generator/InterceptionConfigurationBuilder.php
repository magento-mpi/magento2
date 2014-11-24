<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Di\Code\Generator;

use Magento\Framework\ObjectManager\Config;
use Magento\Tools\Di\Code\Scanner;
use Magento\Framework\Interception\Config\Config as InterceptionConfig;

class InterceptionConfigurationBuilder
{
    /**
     * Area code list: global, frontend, etc.
     *
     * @var array
     */
    private $areaCodesList = [];

    /**
     * @var InterceptionConfig
     */
    private $interceptionConfig;

    /**
     * @var PluginList
     */
    private $pluginList;

    /**
     * @var string
     */
    const GLOBAL_CONFIG = 'global';

    /**
     * @param InterceptionConfig $interceptionConfig
     * @param PluginList $pluginList
     */
    public function __construct(InterceptionConfig $interceptionConfig, PluginList $pluginList)
    {
        $this->interceptionConfig = $interceptionConfig;
        $this->pluginList = $pluginList;
    }


    /**
     * Adds area code
     *
     * @param string $areaCode
     * @return void
     */
    public function addAreaCode($areaCode)
    {
        if (empty($this->areaCodesList[$areaCode])) {
            $this->areaCodesList[] = $areaCode;
        }
    }

    /**
     * Builds interception configuration for all defined classes
     *
     * @return array
     */
    public function getInterceptionConfiguration()
    {
        $definedClasses = get_declared_classes();
        $interceptedInstances = $this->getInterceptedClasses($definedClasses);
        $inheritedConfig = $this->getPluginsList($interceptedInstances);
        $mergedAreaPlugins = $this->mergeAreaPlugins($inheritedConfig);
        $interceptedMethods = $this->getInterceptedMethods($mergedAreaPlugins);

        return $interceptedMethods;
    }

    /**
     * Get intercepted instances from defined class list
     *
     * @param array $definedClasses
     * @return array
     */
    private function getInterceptedClasses($definedClasses)
    {
        $intercepted = [];
        foreach ($definedClasses as $definedClass) {
            if ($this->interceptionConfig->hasPlugins($definedClass) && $this->isConcrete($definedClass)) {
                $intercepted[] = $definedClass;
            }
        }
        return $intercepted;
    }

    /**
     * Returns plugin list:
     * 'concrete class name' => ['plugin name' => [plugin data]]
     *
     * @param array $interceptedInstances
     * @return array
     */
    private function getPluginsList($interceptedInstances)
    {
        $this->pluginList->setInterceptedClasses($interceptedInstances);

        $inheritedConfig = [];
        foreach ($this->areaCodesList as $areaKey) {
            $scopePriority = [self::GLOBAL_CONFIG];
            $pluginListCloned = clone $this->pluginList;
            if ($areaKey != self::GLOBAL_CONFIG) {
                $scopePriority[] = $areaKey;
                $pluginListCloned->setScopePriorityScheme($scopePriority);
            }
            $key = implode('', $scopePriority);
            $inheritedConfig[$key] = $this->filterNullInheritance($pluginListCloned->getPluginsConfig());
        }
        return $inheritedConfig;
    }

    /**
     * Filters plugin inheritance list for instances without plugins, and abstract/interface
     *
     * @param array $pluginInheritance
     * @return array
     */
    private function filterNullInheritance($pluginInheritance)
    {
        $filteredData = [];
        foreach ($pluginInheritance as $instance => $plugins) {
            if (is_null($plugins) || !$this->isConcrete($instance)) {
                continue;
            }

            $pluginInstances = [];
            foreach ($plugins as $plugin) {
                if (in_array($plugin['instance'], $pluginInstances)) {
                    continue;
                }
                $pluginInstances[] = $plugin['instance'];
            }
            $filteredData[$instance] = $pluginInstances;

        }

        return $filteredData;
    }

    /**
     * Merge plugins in areas
     *
     * @param array $inheritedConfig
     * @return array
     */
    private function mergeAreaPlugins($inheritedConfig)
    {
        $mergedConfig = [];
        foreach ($inheritedConfig as $configuration) {
            $mergedConfig = array_merge_recursive($mergedConfig, $configuration);
        }
        foreach ($mergedConfig as &$plugins) {
            $plugins = array_unique($plugins);
        }

        return $mergedConfig;
    }

    /**
     * Returns interception configuration with plugin methods
     *
     * @param array $interceptionConfiguration
     * @return array
     */
    private function getInterceptedMethods($interceptionConfiguration)
    {
        $pluginDefinitionList = new \Magento\Framework\Interception\Definition\Runtime();
        foreach ($interceptionConfiguration as &$plugins) {
            $pluginsMethods = [];
            foreach ($plugins as $plugin) {
                $pluginsMethods = array_unique(
                    array_merge($pluginsMethods, array_keys($pluginDefinitionList->getMethodList($plugin)))
                );
            }
            $plugins = $pluginsMethods;
        }
        return $interceptionConfiguration;
    }

    /**
     * Whether instance is concrete implementation
     *
     * @param string $instance
     * @return bool
     */
    private function isConcrete($instance)
    {
        $instance = new \ReflectionClass($instance);
        return !$instance->isAbstract() && !$instance->isInterface();
    }
}
