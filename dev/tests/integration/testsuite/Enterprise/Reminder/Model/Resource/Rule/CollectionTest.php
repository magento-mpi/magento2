<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Reminder
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Reminder_Model_Resource_Rule_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Enterprise/Reminder/_files/rules.php
     */
    public function testAddDateFilter()
    {
        $dateModel = Mage::getModel('Magento_Core_Model_Date');
        $collection = Mage::getResourceModel('Enterprise_Reminder_Model_Resource_Rule_Collection');
        $collection->addDateFilter($dateModel->date());
        $this->markTestIncomplete('MAGE-5166 is incomplete');
        $this->assertEquals(1, $collection->count());
        foreach ($collection as $rule) {
            $this->assertInstanceOf('Enterprise_Reminder_Model_Rule', $rule);
            $this->assertEquals('Rule 2', $rule->getName());
            return;
        }
        $this->fail('Collection has not been loaded properly.');
    }
}
