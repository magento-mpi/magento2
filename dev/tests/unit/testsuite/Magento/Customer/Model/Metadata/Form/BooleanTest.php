<?php
/**
 * test Magento\Customer\Model\Model\Metadata\Form\Boolean
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

class BooleanTest extends \PHPUnit_Framework_TestCase
{

    /** @var \Magento\Core\Model\LocaleInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $localeMock;

    /** @var \Magento\Logger | \PHPUnit_Framework_MockObject_MockObject */
    protected $loggerMock;

    /** @var \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata | \PHPUnit_Framework_MockObject_MockObject */
    protected $attributeMetadataMock;

    protected function setUp()
    {
        $this->localeMock = $this->getMockBuilder('Magento\Core\Model\LocaleInterface')->getMock();
        $this->loggerMock = $this->getMockBuilder('Magento\Logger')->disableOriginalConstructor()->getMock();
        $this->attributeMetadataMock = $this->getMockBuilder('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param mixed $value to assign to boolean
     * @param mixed $expected text output
     * @dataProvider getOptionTextDataProvider
     */
    public function testGetOptionText($value, $expected)
    {
        // calling outputValue() will cause the protected method getOptionText() to be called
        $boolean = new Boolean($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);
        $this->assertSame($expected, $boolean->outputValue());
    }

    public function getOptionTextDataProvider()
    {
        return [
            '0' => ['0', 'No'],
            '1' => ['1', 'Yes'],
            'int 5' => [5, ''],
            'Null' => [null, ''],
            'Invalid' => ['Invalid', ''],
            'Empty string' => ['', ''],
        ];
    }
}
