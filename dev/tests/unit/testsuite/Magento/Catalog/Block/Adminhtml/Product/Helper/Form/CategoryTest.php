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
     * @param bool $isAllowed
     * @param array $data
     * @param bool $expected
     * @dataProvider getNoDisplayDataProvider
     */
    public function testGetNoDisplay($isAllowed, $data, $expected)
    {
        $authorizationMock = $this->getMockBuilder('Magento\Framework\AuthorizationInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue($isAllowed));
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var Category $element */
        $element = $objectManager->getObject(
            '\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category',
            ['authorization' => $authorizationMock, 'data' => $data]
        );

        $this->assertEquals($expected, $element->getNoDisplay());
    }

    public function getNoDisplayDataProvider()
    {
        return [
            [true, [], false],
            [false, [], true],
            [true, ['no_display' => false], false],
            [true, ['no_display' => true], true],
            [false, ['no_display' => false], true],
            [false, ['no_display' => true], true],
        ];
    }
}
