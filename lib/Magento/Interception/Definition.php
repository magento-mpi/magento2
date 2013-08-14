<?php
/**
 * Plugin method definitions. Provide the list of interception methods in specified plugin.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Interception_Definition
{
    /**
     * Retrieve list of methods
     *
     * @param string $type
     * @return array
     */
    public function getMethodList($type);
}
