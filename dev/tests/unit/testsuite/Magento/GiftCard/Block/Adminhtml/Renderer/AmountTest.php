<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Block\Adminhtml\Renderer;

use Magento\TestFramework\Helper\ObjectManager;

class AmountTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\GiftCard\Block\Adminhtml\Renderer\Amount
     */
    protected $block;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\GiftCard\Block\Adminhtml\Renderer\Amount');
    }

    public function testGetValues()
    {
        $data = [
            [
                'website_id' => '1',
                'value' => '15.000',
            ],
            [
                'website_id' => '2',
                'value' => '1.000',
            ],
            [
                'website_id' => '0',
                'value' => '3.000',
            ],
            [
                'website_id' => '1',
                'value' => '6.000',
            ],
            [
                'website_id' => '2',
                'value' => '8.000',
            ],
        ];

        $expected = [
            [
                'website_id' => '0',
                'value' => '3.000',
            ],
            [
                'website_id' => '1',
                'value' => '6.000',
            ],
            [
                'website_id' => '1',
                'value' => '15.000',
            ],
            [
                'website_id' => '2',
                'value' => '1.000',
            ],
            [
                'website_id' => '2',
                'value' => '8.000',
            ]
        ];

        $element = $this->getMockBuilder('Magento\Framework\Data\Form\Element\AbstractElement')
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->block->setElement($element);

        $element->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($data));

        $this->assertEquals($expected, $this->block->getValues());
    }
}
