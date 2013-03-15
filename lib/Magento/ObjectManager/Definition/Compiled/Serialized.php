<?php
/**
 * Serialized definition reader
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Definition_Compiled_Serialized extends Magento_ObjectManager_Definition_Compiled
{
    /**
     * Unpack signature
     *
     * @param string $signature
     * @return mixed
     */
    protected function _unpack($signature)
    {
        return unserialize($signature);
    }
}
