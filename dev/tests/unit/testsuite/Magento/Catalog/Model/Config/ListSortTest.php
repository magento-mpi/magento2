<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config;

use Magento\TestFramework\Helper\ObjectManager;

class ListSortTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Config\Source\ListSort
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $catalogConfig;

    protected function setUp()
    {
        $this->catalogConfig = $this->getMockBuilder('Magento\Catalog\Model\Config')->
            disableOriginalConstructor()->
            getMock();

        $helper = new ObjectManager($this);
        $this->model = $helper->getObject(
            'Magento\Catalog\Model\Config\Source\ListSort',
            ['catalogConfig' => $this->catalogConfig]
        );
    }

    public function testToOptionalArray()
    {
        $except = [
            ['label' => __('Position'), 'value' => 'position'],
            ['label' => 'testLabel', 'value' => 'testAttributeCode']
        ];
        $this->catalogConfig->expects($this->any())->method('getAttributesUsedForSortBy')
            ->will($this->returnValue([['frontend_label' => 'testLabel', 'attribute_code' => 'testAttributeCode']]));

        $this->assertEquals($except, $this->model->toOptionArray());
    }
} 