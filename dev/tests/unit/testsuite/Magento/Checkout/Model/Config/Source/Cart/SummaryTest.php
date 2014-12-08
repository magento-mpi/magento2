<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Model\Config\Source\Cart;

class SummaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Summary
     */
    private $model;

    protected function setUp()
    {
        $this->model = new Summary();
    }

    public function testToOptionArray()
    {
        $expectedResult = [
            ['value' => 0, 'label' => __('Display number of items in cart')],
            ['value' => 1, 'label' => __('Display item quantities')],
        ];
        $this->assertEquals($expectedResult, $this->model->toOptionArray());
    }
}
