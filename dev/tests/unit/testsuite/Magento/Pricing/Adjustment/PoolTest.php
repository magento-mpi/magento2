<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pricing\Adjustment;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pricing\Adjustment\Pool
     */
    public $model;

    public function setUp()
    {
        $adjustmentsData = [
            'adj1' => ['className' => 'adj1_class', 'sortOrder' => 10],
            'adj2' => ['className' => 'adj2_class', 'sortOrder' => 20],
            'adj3' => ['className' => 'adj3_class', 'sortOrder' => 5],
            'adj4' => ['className' => 'adj4_class', 'sortOrder' => null],
            'adj5' => ['className' => 'adj5_class'],
        ];

        /** @var \Magento\Pricing\Adjustment\Factory|\PHPUnit_Framework_MockObject_MockObject $adjustmentFactory */
        $adjustmentFactory = $this->getMockBuilder('Magento\Pricing\Adjustment\Factory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $adjustmentFactory->expects($this->any())->method('create')->will($this->returnCallback(
            function ($className, $data) {
            return $className . '|' . $data['sortOrder'];
        }));

        $this->model = new Pool($adjustmentFactory, $adjustmentsData);
    }

    public function testGetAdjustments()
    {
        $expectedResult = [
            'adj1' => 'adj1_class|10',
            'adj2' => 'adj2_class|20',
            'adj3' => 'adj3_class|5',
            'adj4' => 'adj4_class|' . \Magento\Pricing\Adjustment\Pool::DEFAULT_SORT_ORDER,
            'adj5' => 'adj5_class|' . \Magento\Pricing\Adjustment\Pool::DEFAULT_SORT_ORDER,
        ];

        $result = $this->model->getAdjustments();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider getAdjustmentByCodeDataProvider
     */
    public function testGetAdjustmentByCode($code, $expectedResult)
    {
        $result = $this->model->getAdjustmentByCode($code);

        $this->assertEquals($expectedResult, $result);
    }

    public function getAdjustmentByCodeDataProvider()
    {
        return [
            ['adj1', 'adj1_class|10'],
            ['adj2', 'adj2_class|20'],
            ['adj3', 'adj3_class|5'],
            ['adj4', 'adj4_class|' . \Magento\Pricing\Adjustment\Pool::DEFAULT_SORT_ORDER],
            ['adj5', 'adj5_class|' . \Magento\Pricing\Adjustment\Pool::DEFAULT_SORT_ORDER],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetAdjustmentByNotExistingCode()
    {
        $this->model->getAdjustmentByCode('not_existing_code');
    }
}
