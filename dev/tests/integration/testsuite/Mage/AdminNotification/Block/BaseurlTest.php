<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_BaseurlTest extends Mage_Backend_Area_TestCase
{
    public function testGetConfigUrl()
    {
        /** @var $block Mage_AdminNotification_Block_Baseurl */
        $block = Mage::app()->getLayout()->createBlock('Mage_AdminNotification_Block_Baseurl');
        $this->assertStringStartsWith('http://localhost/', $block->getConfigUrl());
    }
}
