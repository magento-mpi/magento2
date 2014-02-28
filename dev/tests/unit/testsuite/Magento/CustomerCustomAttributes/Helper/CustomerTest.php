<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\CustomerCustomAttributes\Helper;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_inputValidatorMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_contextMock = $this->getMockBuilder('Magento\App\Helper\Context')
            ->disableOriginalConstructor()->getMock();

        $this->_dataHelperMock = $this->getMockBuilder('Magento\CustomerCustomAttributes\Helper\Data')
            ->disableOriginalConstructor()->getMock();
        $this->_dataHelperMock->expects($this->any())
            ->method('getAttributeInputTypes')
            ->will($this->returnValue(array()));

        $this->_inputValidatorMock =
            $this->getMockBuilder('Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator')
                ->disableOriginalConstructor()->getMock();
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

        $helper = new \Magento\CustomerCustomAttributes\Helper\Customer(
            $this->_contextMock,
            $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false),
            $this->getMockForAbstractClass('Magento\Core\Model\LocaleInterface'),
            $this->getMock('Magento\Filter\FilterManager', array(), array(), '', false),
            $this->_dataHelperMock,
            $this->_inputValidatorMock
        );

        $this->setExpectedException('Magento\Core\Exception');
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

        $helper = new \Magento\CustomerCustomAttributes\Helper\Customer(
            $this->_contextMock,
            $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false),
            $this->getMockForAbstractClass('Magento\Core\Model\LocaleInterface'),
            $this->getMock('Magento\Filter\FilterManager', array(), array(), '', false),
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
