<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Category\Attribute\Source;

use Magento\TestFramework\Helper\ObjectManager;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    private $testArray = ['test1', ['test1']];
    /**
     * @var \Magento\Catalog\Model\Category\Attribute\Source\Layout
     */
    private $model;

    public function testGetAllOptions()
    {
        $assertArray = $this->testArray;
        array_unshift($assertArray, ['value' => '', 'label' => __('No layout updates')]);
        $this->assertEquals($assertArray, $this->model->getAllOptions());
    }

    protected function setUp()
    {
        $helper = new ObjectManager($this);
        $this->model = $helper->getObject(
            '\Magento\Catalog\Model\Category\Attribute\Source\Layout',
            [
                'pageSourceLayout' => $this->getMockedLayout()
            ]
        );
    }

    /**
     * @return \Magento\Theme\Model\Layout\Source\Layout
     */
    private function getMockedLayout()
    {
        $mockBuilder = $this->getMockBuilder('\Magento\Theme\Model\Layout\Source\Layout');
        $mockBuilder->disableOriginalConstructor();
        $mock = $mockBuilder->getMock();

        $mock->expects($this->any())
            ->method('toOptionArray')
            ->will($this->returnValue($this->testArray));

        return $mock;
    }
}