<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub class to catch events in Mage::dispatchEvent()
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Stub_Event
{
    /**
     * Main method which is run at dispatchEvent.
     * Can be mocked to add callback.
     *
     * @param Varien_Event_Observer $observer
     */
    public function run($observer)
    {
    }
}
