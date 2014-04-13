<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Payment\Method;

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
    protected $_converter;

    /**
     * ObjectManager helper
     *
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        /** @var $encryptor \PHPUnit_Framework_MockObject_MockObject|\Magento\Encryption\EncryptorInterface */
        $encryptor = $this->getMock('\Magento\Encryption\EncryptorInterface');
        $encryptor->expects($this->any())
            ->method('encrypt')
            ->will($this->returnValue(self::ENCRYPTED_VALUE));
        $encryptor->expects($this->any())
            ->method('decrypt')
            ->will($this->returnValue(self::DECRYPTED_VALUE));

        $this->_converter = new \Magento\Sales\Model\Payment\Method\Converter($encryptor);

        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * Create mock AbstractModel object
     *
     * @param string $method
     * @param string $fieldName
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Model\AbstractModel
     */
    protected function _mockModelObject($method, $fieldName)
    {
        $arguments = $this->_objectManager->getConstructArguments('\Magento\Model\AbstractModel');
        $arguments['data'] = array(
            'method' => $method,
            $fieldName => self::INPUT_VALUE,
        );

        $modelMock = $this->getMockForAbstractClass(
            'Magento\Model\AbstractModel',
            $arguments
        );

        return $modelMock;
    }

    /**
     * Test positive calls to decode(), value should be decrypted
     *
     * @dataProvider positiveDataProvider
     */
    public function testDecodePositive($method, $fieldName)
    {
        $modelMock = $this->_mockModelObject($method, $fieldName);

        $returnValue = $this->_converter->decode($modelMock, $fieldName);
        $this->assertEquals(self::DECRYPTED_VALUE, $returnValue);
    }

    /**
     * Test the positive calls to encode(), return value should encrypted
     *
     * @dataProvider positiveDataProvider
     */
    public function testEncodePositive($method, $fieldName)
    {
        $modelMock = $this->_mockModelObject($method, $fieldName);

        $returnValue = $this->_converter->encode($modelMock, $fieldName);
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
        $data = array();
        $data[] = array('ccsave', 'cc_owner');
        $data[] = array('ccsave', 'cc_exp_year');
        $data[] = array('ccsave', 'cc_exp_month');

        return $data;
    }

    /**
     * Test the negative calls to decode(), value should be original value, not decrypted
     *
     * @dataProvider negativeDataProvider
     */
    public function testDecodeNegative($method, $fieldName)
    {
        $modelMock = $this->_mockModelObject($method, $fieldName);

        $returnValue = $this->_converter->decode($modelMock, $fieldName);
        $this->assertEquals(self::INPUT_VALUE, $returnValue);
    }

    /**
     * Test the negative calls to encode(), return value should be original value, not encrypted
     *
     * @dataProvider negativeDataProvider
     */
    public function testEncodeNegative($method, $fieldName)
    {
        $modelMock = $this->_mockModelObject($method, $fieldName);

        $returnValue = $this->_converter->encode($modelMock, $fieldName);
        $this->assertEquals(self::INPUT_VALUE, $returnValue);
    }

    /**
     * Negative dataProvider
     *
     * @return array
     */
    public function negativeDataProvider()
    {
        $data = array();
        //Incorrect method name
        $data[] = array('ccsave_incorrect', 'cc_owner');
        //Incorrect fieldName
        $data[] = array('ccsave', 'cc_exp_year_incorrect');

        return $data;
    }
}
