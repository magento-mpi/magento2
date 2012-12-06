<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Implementation of the Observer class
 */
class Mage_Listener_Observers_EmptyObserver
{
    public function startTestSuite(Mage_Listener_EventListener $listener)
    {
        $listener->getCurrentSuite();
    }

    public function endTestSuite(Mage_Listener_EventListener $listener)
    {
        $listener->getCurrentSuite();
    }

    public function testFailed(Mage_Listener_EventListener $listener)
    {
        $listener->getCurrentTest();
    }
 }