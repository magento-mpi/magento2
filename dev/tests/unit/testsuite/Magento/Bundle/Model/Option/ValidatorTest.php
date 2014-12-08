<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Option;

use Magento\TestFramework\Helper\ObjectManager;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Model\Option\Validator
     */
    private $validator;

    /**
     * SetUp method for unit test
     */
    protected function setUp()
    {
        $helper = new ObjectManager($this);
        $this->validator = $helper->getObject('Magento\Bundle\Model\Option\Validator');
    }

    /**
     * Test for method isValid
     *
     * @param string $title
     * @param string $type
     * @param bool $isValid
     * @param string[] $expectedMessages
     * @dataProvider providerIsValid
     */
    public function testIsValid($title, $type, $isValid, $expectedMessages)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Bundle\Model\Option $option */
        $option = $this->getMockBuilder('Magento\Bundle\Model\Option')
            ->setMethods(['getTitle', 'getType'])
            ->disableOriginalConstructor()
            ->getMock();
        $option->expects($this->once())
            ->method('getTitle')
            ->willReturn($title);
        $option->expects($this->once())
            ->method('getType')
            ->willReturn($type);

        $this->assertEquals($isValid, $this->validator->isValid($option));
        $this->assertEquals($expectedMessages, $this->validator->getMessages());
    }

    /**
     * Provider for testIsValid
     */
    public function providerIsValid()
    {
        return [
            ['title', 'select', true, []],
            ['title', null, false, ['type' => 'type is a required field.']],
            [null, 'select', false, ['title' => 'title is a required field.']],
            [null, null, false, ['type' => 'type is a required field.', 'title' => 'title is a required field.']]
        ];
    }
}
