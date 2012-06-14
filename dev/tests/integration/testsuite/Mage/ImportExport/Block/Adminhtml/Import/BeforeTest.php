<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for block Mage_ImportExport_Block_Adminhtml_Import_BeforeTest
 *
 * @group module:Mage_ImportExport
 */
class Mage_ImportExport_Block_Adminhtml_Import_BeforeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test getter for JS array behaviour string
     */
    public function testGetJsAllowedCustomerBehaviours()
    {
        /** @var $helper Mage_ImportExport_Helper_Data */
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');
        $existingVersion = 'existing_version';
        $customerBehaviours = array('behaviour_1', 'behaviour_2');
        $notExistingVersion = 'not_existing_version';

        $reflectionBehaviours = new ReflectionProperty('Mage_ImportExport_Helper_Data', '_allowedCustomerBehaviours');
        $reflectionBehaviours->setAccessible(true);
        $reflectionBehaviours->setValue($helper, array($existingVersion => $customerBehaviours));

        $block = new Mage_ImportExport_Block_Adminhtml_Import_Before();

        $testJsBehaviours = $block->getJsAllowedCustomerBehaviours($existingVersion);
        $correctJsBehaviours = '["' . implode('", "', $customerBehaviours) . '"]';
        $this->assertEquals($correctJsBehaviours, $testJsBehaviours, 'Incorrect JS array string.');

        $testJsBehaviours = $block->getJsAllowedCustomerBehaviours($notExistingVersion);
        $correctJsBehaviours = '[]';
        $this->assertEquals($correctJsBehaviours, $testJsBehaviours, 'Incorrect JS array string.');
    }
}
