<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType\Builders;

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
    public function toJson(ConfigurationStorageInterface $storage, $parentName = null)
    {
        $result = [];
        if ($parentName !== null) {
            $rootComponent = $storage->getComponentsData($parentName);
            $result['name'] = $rootComponent->getName();
            $result['parent_name'] = $rootComponent->getParentName();
            $result['meta'] = $storage->getMeta($parentName);
            $result['data'] = $storage->getData($parentName);
        } else {
            $components = $storage->getComponentsData();
            if (!empty($components)) {
                /** @var ConfigurationInterface $component */
                foreach ($components as $name => $component) {
                    $result['config']['components'][$name] = $component->getData();
                }
            }
            $result['meta'] = $storage->getMeta();
            $result['data'] = $storage->getData();
        }

        $result['config'] += $storage->getCloudData();
        $result['dump']['extenders'] = [];

        return json_encode($result);
    }
}
