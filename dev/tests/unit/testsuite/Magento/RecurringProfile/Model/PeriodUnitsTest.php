<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model;

use Magento\TestFramework\Helper\ObjectManager;

class PeriodUnitsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\RecurringProfile\Model\PeriodUnits */
    protected $object;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject('Magento\RecurringProfile\Model\PeriodUnits');
    }

    public function testToOptionArray()
    {
        $this->assertEquals(
            [
                'day' => 'Day',
                'week' => 'Week',
                'semi_month' => 'Two Weeks',
                'month' => 'Month',
                'year' => 'Year',
            ],
            $this->object->toOptionArray()
        );
    }
}
