<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Interception_Custom_Module_Model_ItemContainerPlugin_Simple
{
    /**
     * @param string $invocationResult
     * @return string
     */
    public function afterGetName($invocationResult)
    {
        return $invocationResult . '|';
    }
}
