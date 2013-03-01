<?php
/**
 * Igbinary serialized definition reader
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Definition_Compiled_Binary extends Magento_ObjectManager_Definition_Compiled
{
    /**
     * Unpack signature
     *
     * @param string $signature
     * @return mixed
     */
    protected function _unpack($signature)
    {
        return igbinary_unserialize($signature);
    }
}
