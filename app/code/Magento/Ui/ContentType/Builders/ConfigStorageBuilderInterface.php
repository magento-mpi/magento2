<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType\Builders;

use Magento\Ui\ConfigurationStorageInterface;

/**
 * Interface ConfigStorageBuilderInterface
 */
interface ConfigStorageBuilderInterface
{
    /**
     * Config storage data to JSON by output
     *
     * @param ConfigurationStorageInterface $storage
     * @param string $parentName
     * @return string
     */
    public function toJson(ConfigurationStorageInterface $storage, $parentName);
}
