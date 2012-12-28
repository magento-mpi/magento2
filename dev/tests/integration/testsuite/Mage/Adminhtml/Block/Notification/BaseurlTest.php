<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Notification_BaseurlTest extends PHPUnit_Framework_TestCase
{
    public function testGetConfigUrl()
    {
        /** @var $block Mage_Adminhtml_Block_Notification_Baseurl */
        $block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Notification_Baseurl');
        $this->assertStringStartsWith('http://localhost/', $block->getConfigUrl());
    }
}
