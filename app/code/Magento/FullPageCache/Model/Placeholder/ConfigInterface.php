<?php
/**
 * Placeholder configuration interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_FullPageCache_Model_Placeholder_ConfigInterface
{
    /**
     * Get placeholders config by block instance name
     *
     * @param $name
     * @return array
     */
    public function getPlaceholders($name);
}
