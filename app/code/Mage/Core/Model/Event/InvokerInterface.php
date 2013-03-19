<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Event invoker interface.
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
