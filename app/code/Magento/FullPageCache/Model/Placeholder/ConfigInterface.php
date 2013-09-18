<?php
/**
 * Placeholder configuration interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Placeholder;

interface ConfigInterface
{
    /**
     * Get placeholders config by block instance name
     *
     * @param $name
     * @return array
     */
    public function getPlaceholders($name);
}
