<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element\UiComponent;

/**
 * Interface ConfigStorageBuilderInterface
 */
interface ConfigStorageBuilderInterface
{
    /**
     * Config storage data to JSON by output
     *
     * @param ConfigStorageInterface $storage
     * @param string $parentName
     * @return string
     */
    public function toJson(ConfigStorageInterface $storage, $parentName = null);
}
