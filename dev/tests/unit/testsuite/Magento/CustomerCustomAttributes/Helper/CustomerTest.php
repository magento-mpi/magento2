<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_CustomerCustomAttributes_Helper_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_inputValidatorMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_contextMock = $this->getMockBuilder('Magento_Core_Helper_Context')
            ->disableOriginalConstructor()->getMock();

        $this->_dataHelperMock = $this->getMockBuilder('Magento_CustomerCustomAttributes_Helper_Data')
            ->disableOriginalConstructor()->getMock();
        $this->_dataHelperMock->expects($this->any())
            ->method('getAttributeInputTypes')
            ->will($this->returnValue(array()));

        $this->_inputValidatorMock =
            $this->getMockBuilder('Magento_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator')
                ->disableOriginalConstructor()->getMock();

        $abstractHelperMock = $this->getMockBuilder('Magento_Core_Helper_Abstract')
            ->disableOriginalConstructor()->getMock();

        $objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')->getMock();
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($abstractHelperMock));
        Mage::reset();
        Mage::setObjectManager($objectManagerMock);
    }

    /**
     * Clean up after test
     */
    public function tearDown()
    {
        Mage::reset();
    }

    /**
     * @test
     * @param array $data
     * @param bool $validatorResult
     * @dataProvider getFilterExceptionDataProvider
     */
    public function filterPostDataExceptionTest($data, $validatorResult)
    {
        $this->_inputValidatorMock->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue($validatorResult));

        $this->_inputValidatorMock->expects($this->any())
            ->method('getMessages')
            ->will($this->returnValue(array('Some error message')));

        $helper = new Magento_CustomerCustomAttributes_Helper_Customer(
            $this->_contextMock,
            $this->_dataHelperMock,
            $this->_inputValidatorMock
        );

        $this->setExpectedException('Magento_Core_Exception');
        $helper->filterPostData($data);
    }

    /**
     *
     * @param array $data
     * @param array $expectedResultData
     * @dataProvider getFilterDataProvider
     * @test
     */
    public function filterPostDataTest($data, $expectedResultData)
    {
        $this->_inputValidatorMock->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $this->_inputValidatorMock->expects($this->never())
            ->method('getMessages');

        $helper = new Magento_CustomerCustomAttributes_Helper_Customer(
            $this->_contextMock,
            $this->_dataHelperMock,
            $this->_inputValidatorMock
        );

        $dataResult = $helper->filterPostData($data);

        $this->assertEquals($dataResult, $expectedResultData);
    }

    /**
     * Test exception data provider
     *
     * @return array
     */
    public function getFilterExceptionDataProvider()
    {
        return array(
            array(
                array(
                    'frontend_label' => array(),
                    'frontend_input' => 'file',
                    'attribute_code' => 'correct_code'
                ),
                false
            ),
            array(
                array(
                    'frontend_label' => array(),
                    'frontend_input' => 'select',
                    'attribute_code' => 'inCorrect_code'
                ),
                true
            ),
            array(
                array(
                    'frontend_label' => array(),
                    'frontend_input' => 'select',
                    'attribute_code' => 'in!correct_code'
                ),
                true
            ),
        );
    }

    /**
     * Test filter data provider
     *
     * @return array
     */
    public function getFilterDataProvider()
    {
        return array(
            array(
                array(
                    'frontend_label' => array('<script></script>'),
                    'frontend_input' => 'file',
                    'attribute_code' => 'correct_code'
                ),
                array(
                    'frontend_label' => array(''),
                    'frontend_input' => 'file',
                    'attribute_code' => 'correct_code'
                ),
            )
        );
    }
}
