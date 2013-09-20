<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cron\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cron\Model\Observer
     */
    private $_model = null;

    public function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Cron\Model\Observer');
        $this->_model->dispatch('this argument is not used');
    }

    public function testDispatchScheduled()
    {
        $collection = \Mage::getResourceModel('Magento\Cron\Model\Resource\Schedule\Collection');
        $collection->addFieldToFilter('status', \Magento\Cron\Model\Schedule::STATUS_PENDING);
        $this->assertGreaterThan(0, $collection->count(), 'Cron has failed to schedule tasks for itself for future.');
    }

    public function testDispatchNoFailed()
    {
        $collection = \Mage::getResourceModel('Magento\Cron\Model\Resource\Schedule\Collection');
        $collection->addFieldToFilter('status', \Magento\Cron\Model\Schedule::STATUS_ERROR);
        foreach ($collection as $item) {
            $this->fail($item->getMessages());
        }
    }
}
