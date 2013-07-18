<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_ObjectManager_Interception_Definition
{
    /**
     * Retrieve list of methods
     *
     * @param string $type
     * @return array
     */
    public function getMethodList($type);
}
