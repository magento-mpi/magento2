<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cron_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cron_Model_Observer
     */
    private $_model = null;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Cron_Model_Observer');
        $this->_model->dispatch('this argument is not used');
    }

    public function testDispatchScheduled()
    {
        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Cron_Model_Resource_Schedule_Collection');
        $collection->addFieldToFilter('status', Magento_Cron_Model_Schedule::STATUS_PENDING);
        $this->assertGreaterThan(0, $collection->count(), 'Cron has failed to schedule tasks for itself for future.');
    }

    public function testDispatchNoFailed()
    {
        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Cron_Model_Resource_Schedule_Collection');
        $collection->addFieldToFilter('status', Magento_Cron_Model_Schedule::STATUS_ERROR);
        foreach ($collection as $item) {
            $this->fail($item->getMessages());
        }
    }
}
