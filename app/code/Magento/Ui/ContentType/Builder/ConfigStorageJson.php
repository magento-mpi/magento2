<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType\Builder;

use Magento\Framework\View\Element\UiComponent\ConfigStorageBuilderInterface;
use Magento\Framework\View\Element\UiComponent\ConfigInterface;
use Magento\Framework\View\Element\UiComponent\ConfigStorageInterface;

/**
 * Class ConfigStorageBuilder
 */
class ConfigStorageJson implements ConfigStorageBuilderInterface
{
    /**
     * Config storage data to JSON by output
     *
     * @param ConfigStorageInterface $storage
     * @param string $parentName
     * @return string
     */
    public function toJson(ConfigStorageInterface $storage, $parentName = null)
    {
        $result = [
            'config' => []
        ];
        $result['meta'] = $storage->getMeta($parentName);
        if ($parentName !== null) {
            $rootComponent = $storage->getComponentsData($parentName);
            $result['name'] = $rootComponent->getName();
            $result['parent_name'] = $rootComponent->getParentName();
            $result['data'] = $storage->getData($parentName);
            $result['config']['components'][$rootComponent->getName()] = $rootComponent->getData();
        } else {
            $components = $storage->getComponentsData();
            if (!empty($components)) {
                /** @var ConfigInterface $component */
                foreach ($components as $name => $component) {
                    $result['config']['components'][$name] = $component->getData();
                }
            }
            $result['data'] = $storage->getData();
        }

        $result['config'] += $storage->getGlobalData();
        $result['dump']['extenders'] = [];

        return json_encode($result);
    }

    /**
     * Config storage data to JSON by output
     *
     * @param ConfigStorageInterface $storage
     * @return string
     */
    public function toJsonNew(ConfigStorageInterface $storage)
    {
        $result = [];
        foreach ($storage->getData() as $key => $data) {
            $result['providers'][$key] = [
                'path' => "Magento_Ui/js/form/provider/provider",
                'data' => $data
            ];
        }
        $result['renderer']= [
            'types' => $storage->getComponents(),
            'layout' => $storage->getLayoutNodes()
        ];
        return json_encode($result);
    }
}
