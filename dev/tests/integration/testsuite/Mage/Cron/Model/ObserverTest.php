<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Cron_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Cron_Model_Observer
     */
    private $_model = null;

    public function setUp()
    {
        $this->_model = Mage::getModel('Mage_Cron_Model_Observer');
        $this->_model->dispatch('this argument is not used');
    }

    public function testDispatchScheduled()
    {
        $collection = Mage::getResourceModel('Mage_Cron_Model_Resource_Schedule_Collection');
        $collection->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_PENDING);
        $this->assertGreaterThan(0, $collection->count(), 'Cron has failed to schedule tasks for itself for future.');
    }

    public function testDispatchNoFailed()
    {
        $collection = Mage::getResourceModel('Mage_Cron_Model_Resource_Schedule_Collection');
        $collection->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_ERROR);
        foreach ($collection as $item) {
            $this->fail($item->getMessages());
        }
    }
}
