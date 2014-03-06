<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Config\Source\Address;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Config\Source\Address\Type
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new \Magento\Customer\Model\Config\Source\Address\Type();
    }

    public function test()
    {
        $expected = array('billing' => 'Billing Address','shipping' => 'Shipping Address');
        $this->assertEquals($expected, $this->model->toOptionArray());
    }
}
