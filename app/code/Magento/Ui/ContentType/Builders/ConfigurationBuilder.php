<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ui\ContentType\Builders;

use Magento\Ui\ConfigurationInterface;

/**
 * Class ConfigurationBuilder
 */
class ConfigurationBuilder implements ConfigBuilderInterface
{
    /**
     * Config data to JSON by output
     *
     * @param ConfigurationInterface $configuration
     * @return string
     */
    public function toJson(ConfigurationInterface $configuration)
    {
        $result = $configuration->getData();
        $result['name'] = $configuration->getName();
        $result['parent_name'] = $configuration->getParentName();

        return json_encode($result);
    }
}
