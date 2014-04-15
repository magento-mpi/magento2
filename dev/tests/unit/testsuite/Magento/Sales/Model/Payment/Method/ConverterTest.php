<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Payment\Method;

use Magento\Sales\Model\Payment\Method\Converter;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    const ENCRYPTED_VALUE = 'encrypted_value';
    const DECRYPTED_VALUE = 'decrypted_value';
    const INPUT_VALUE = 'input_value';

    /**
     * The Converter object to be tested
     *
     * @var \Magento\Sales\Model\Payment\Method\Converter
     */
    private $converter;

    protected function setUp()
    {
        /** @var $encryptor \PHPUnit_Framework_MockObject_MockObject|\Magento\Encryption\EncryptorInterface */
        $encryptor = $this->getMock('Magento\Encryption\EncryptorInterface');
        $encryptor->expects($this->any())
            ->method('encrypt')
            ->will($this->returnValue(self::ENCRYPTED_VALUE));
        $encryptor->expects($this->any())
            ->method('decrypt')
            ->will($this->returnValue(self::DECRYPTED_VALUE));

        $this->converter = new Converter($encryptor);
    }

    /**
     * Create mock AbstractModel object
     *
     * @param string $method
     * @param string $fieldName
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Model\AbstractModel
     */
    private function mockModelObject($method, $fieldName)
    {
        $modelMock = $this->getMockBuilder('Magento\Model\AbstractModel')
            ->setMethods(['__wakeup', 'getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        //map arguments to return values, including optional parameters
        $valueMap = [
            ['method', null, $method],
            [$fieldName, null, self::INPUT_VALUE],
        ];
        $modelMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap($valueMap));

        return $modelMock;
    }

    /**
     * Test positive calls to decode(), value should be decrypted
     *
     * @param string $method
     * @param string $fieldName
     * @dataProvider positiveDataProvider
     */
    public function testDecodePositive($method, $fieldName)
    {
        $modelMock = $this->mockModelObject($method, $fieldName);

        $returnValue = $this->converter->decode($modelMock, $fieldName);
        $this->assertEquals(self::DECRYPTED_VALUE, $returnValue);
    }

    /**
     * Test the positive calls to encode(), return value should encrypted
     *
     * @param string $method
     * @param string $fieldName
     * @dataProvider positiveDataProvider
     */
    public function testEncodePositive($method, $fieldName)
    {
        $modelMock = $this->mockModelObject($method, $fieldName);

        $returnValue = $this->converter->encode($modelMock, $fieldName);
        $this->assertEquals(self::ENCRYPTED_VALUE, $returnValue);
    }

    /**
     * Positive dataProvider
     *
     * @see \Magento\Sales\Model\Payment\Method\Converter::$_encryptFields
     * @return array
     */
    public function positiveDataProvider()
    {
        $data = [
            'owner' => ['ccsave', 'cc_owner'],
            'exp_year' => ['ccsave', 'cc_exp_year'],
            'exp_month' => ['ccsave', 'cc_exp_month'],
        ];

        return $data;
    }

    /**
     * Test the negative calls to decode(), value should be original value, not decrypted
     *
     * @param string $method
     * @param string $fieldName
     * @dataProvider negativeDataProvider
     */
    public function testDecodeNegative($method, $fieldName)
    {
        $modelMock = $this->mockModelObject($method, $fieldName);

        $returnValue = $this->converter->decode($modelMock, $fieldName);
        $this->assertEquals(self::INPUT_VALUE, $returnValue);
    }

    /**
     * Test the negative calls to encode(), return value should be original value, not encrypted
     *
     * @param string $method
     * @param string $fieldName
     * @dataProvider negativeDataProvider
     */
    public function testEncodeNegative($method, $fieldName)
    {
        $modelMock = $this->mockModelObject($method, $fieldName);

        $returnValue = $this->converter->encode($modelMock, $fieldName);
        $this->assertEquals(self::INPUT_VALUE, $returnValue);
    }

    /**
     * Negative dataProvider
     *
     * @return array
     */
    public function negativeDataProvider()
    {
        $data = [
            'incorrect_method_name' => ['ccsave_incorrect', 'cc_owner'],
            'incorrect_field_name' => ['ccsave', 'cc_exp_year_incorrect'],
        ];
        return $data;
    }
}
