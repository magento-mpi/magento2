<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element;

class FormKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Element\FormKey
     */
    protected $formKeyElement;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $formKeyMock = $this->getMockBuilder('Magento\Framework\Data\Form\FormKey')
            ->setMethods(['getFormKey'])->disableOriginalConstructor()->getMock();

        $formKeyMock->expects($this->any())
            ->method('getFormKey')
            ->will($this->returnValue('form_key'));

        $this->formKeyElement = $objectManagerHelper->getObject(
            'Magento\Framework\View\Element\FormKey',
            ['formKey' => $formKeyMock]
        );
    }

    public function testGetFormKey()
    {
        $this->assertEquals('form_key', $this->formKeyElement->getFormKey());
    }

    public function testToHtml()
    {
        $this->assertEquals(
            '<input name="form_key" type="hidden" value="form_key" />',
            $this->formKeyElement->toHtml()
        );
    }
}
