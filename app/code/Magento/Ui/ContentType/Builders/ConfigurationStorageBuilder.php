<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ui\ContentType\Builders;

use Magento\Ui\ConfigurationInterface;
use Magento\Ui\ConfigurationStorageInterface;

/**
 * Class ConfigurationStorageBuilder
 */
class ConfigurationStorageBuilder implements ConfigStorageBuilderInterface
{
    /**
     * Config storage data to JSON by output
     *
     * @param ConfigurationStorageInterface $storage
     * @param string $parentName
     * @return string
     */
    public function toJson(ConfigurationStorageInterface $storage, $parentName)
    {
        $rootComponent = $storage->getComponentsData($parentName);
        $result = [];
        $result['name'] = $rootComponent->getName();
        $result['parent_name'] = $rootComponent->getParentName();
        $components = $storage->getComponentsData();
        if (!empty($components)) {
            /** @var ConfigurationInterface $component */
            foreach ($components as $name => $component) {
                    $result['config']['components'][$name] = $component->getData();
            }
        }
        $result['config'] += $storage->getCloudData();
        $result['dump']['extenders'] = [];
        $result['meta'] = $storage->getMeta($parentName);
        $result['data'] = $storage->getData($parentName);

        return json_encode($result);
    }
}
