<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Interception_Definition_Runtime implements Magento_ObjectManager_Interception_Definition
{
    /**
     * Retrieve list of methods
     *
     * @param string $type
     * @return array
     */
    public function getMethodList($type)
    {
        return get_class_methods($type);
    }
}
