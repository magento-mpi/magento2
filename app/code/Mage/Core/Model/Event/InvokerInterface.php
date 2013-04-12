<?php
/**
 * Event invoker interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Event_InvokerInterface
{
    /**
     * Dispatch event
     *
     * @param array $configuration
     * @param Varien_Event_Observer $observer
     */
    public function dispatch(array $configuration, Varien_Event_Observer $observer);
}
