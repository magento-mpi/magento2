<?php
/**
 * Event invoker interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_Event_InvokerInterface
{
    /**
     * Dispatch event
     *
     * @param array $configuration
     * @param Magento_Event_Observer $observer
     */
    public function dispatch(array $configuration, Magento_Event_Observer $observer);
}
