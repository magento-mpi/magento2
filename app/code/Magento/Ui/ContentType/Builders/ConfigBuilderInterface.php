<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType\Builders;

use Magento\Ui\ConfigurationInterface;

/**
 * Interface ConfigBuilderInterface
 */
interface ConfigBuilderInterface
{
    /**
     * Config data to JSON by output
     *
     * @param ConfigurationInterface $configuration
     * @return string
     */
    public function toJson(ConfigurationInterface $configuration);
}
