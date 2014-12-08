<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Config\Source;

class AllspecificcountriesTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        $expectedArray = [
            ['value' => 0, 'label' => __('All Allowed Countries')],
            ['value' => 1, 'label' => __('Specific Countries')],
        ];
        $model = new Allspecificcountries();
        $this->assertEquals($expectedArray, $model->toOptionArray());
    }
}
