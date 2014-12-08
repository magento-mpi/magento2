<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model;

use Magento\TestFramework\Helper\ObjectManager;

class PeriodUnitsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\RecurringPayment\Model\PeriodUnits */
    protected $object;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject('Magento\RecurringPayment\Model\PeriodUnits');
    }

    public function testToOptionArray()
    {
        $this->assertEquals(
            ['day' => 'Day', 'week' => 'Week', 'semi_month' => 'Two Weeks', 'month' => 'Month', 'year' => 'Year'],
            $this->object->toOptionArray()
        );
    }
}
