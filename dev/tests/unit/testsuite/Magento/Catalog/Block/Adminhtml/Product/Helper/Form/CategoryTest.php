<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

class CategoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider isAllowedDataProvider
     * @param $isAllowed
     */
    public function testIsAllowed($isAllowed)
    {
        $authorization = $this->getMockBuilder('Magento\Framework\AuthorizationInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $authorization->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue($isAllowed));
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var Category $model */
        $model = $objectManager->getObject(
            '\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category',
            ['authorization' => $authorization]
        );
        switch ($isAllowed) {
            case true:
                $this->assertEquals('select', $model->getType());
                $this->assertNull($model->getClass());
                break;
            case false:
                $this->assertEquals('hidden', $model->getType());
                $this->assertContains('hidden', $model->getClass());
                break;
        }
    }

    public function isAllowedDataProvider()
    {
        return [
            [true],
            [false],
        ];
    }
}
