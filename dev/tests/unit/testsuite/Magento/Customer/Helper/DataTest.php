<?php

namespace Magento\Customer\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    const FORM_CODE = 'FORM_CODE';
    const ENTITY = 'ENTITY';
    const SCOPE = 'SCOPE';

    protected $_expected
        = [
            'filter_key'                   => 'filter_value',
            'is_in_request_data'           => 'request_data_value',
            'is_not_in_request_data'       => false,
            'attribute_is_front_end_input' => true
        ];

    /** @var \Magento\Customer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $_dataHelper;

    /** @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_mockRequest;

    /** @var array */
    protected $_additionalAttributes;

    /** @var \Magento\Customer\Model\Metadata\Form|\PHPUnit_Framework_MockObject_MockObject */
    protected $_mockMetadataForm;

    public function setUp()
    {
        $this->_dataHelper = $this->getMockBuilder('\Magento\Customer\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(['__construct'])
            ->getMock();

        $this->_mockRequest = $this->getMock(
            'Magento\App\RequestInterface',
            ['getPost', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam'],
            [],
            '',
            false
        );
        $this->_additionalAttributes = ['is_in_request_data', 'is_not_in_request_data'];
        $this->_mockMetadataForm = $this->getMockBuilder('\Magento\Customer\Model\Metadata\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $filteredData = [
            'filter_key'                       => 'filter_value',
            'attribute_is_not_front_end_input' => false,
            'attribute_is_front_end_input'     => true
        ];
        $this->_mockMetadataForm->expects($this->once())
            ->method('extractData')
            ->with($this->_mockRequest, self::SCOPE)
            ->will($this->returnValue($filteredData));

        $requestData = ['is_in_request_data' => 'request_data_value'];
        $this->_mockRequest->expects($this->once())
            ->method('getPost')
            ->with(self::SCOPE)
            ->will($this->returnValue($requestData));

        $attributeIsFrontEndInput = $this->getMockBuilder('\Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $attributeIsFrontEndInput->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_is_front_end_input'));
        $attributeIsFrontEndInput->expects($this->once())
            ->method('getFrontendInput')
            ->will($this->returnValue('boolean'));

        $attributeIsNotFrontEndInput = $this->getMockBuilder('\Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $attributeIsNotFrontEndInput->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_is_not_front_end_input'));
        $attributeIsNotFrontEndInput->expects($this->once())
            ->method('getFrontendInput')
            ->will($this->returnValue(false));

        $formAttributes = [$attributeIsFrontEndInput, $attributeIsNotFrontEndInput];
        $this->_mockMetadataForm->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue($formAttributes));
    }

    public function testExtractCustomerData()
    {
        $this->assertEquals(
            $this->_expected,
            $this->_dataHelper->extractCustomerData(
                $this->_mockRequest,
                self::FORM_CODE,
                self::ENTITY,
                $this->_additionalAttributes,
                self::SCOPE,
                $this->_mockMetadataForm
            )
        );
    }

    public function testExtractCustomerDataWithFactory()
    {
        /** @var \Magento\Customer\Model\Metadata\FormFactory|\PHPUnit_Framework_MockObject_MockObject */
        $mockFormFactory = $this->getMockBuilder('\Magento\Customer\Model\Metadata\FormFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = ['formFactory' => $mockFormFactory];
        $this->_dataHelper = $objectManagerHelper
            ->getObject('\Magento\Customer\Helper\Data', $arguments);

        $mockFormFactory->expects($this->once())
            ->method('create')
            ->with(
                self::ENTITY,
                self::FORM_CODE,
                [],
                false,
                \Magento\Customer\Model\Metadata\Form::DONT_IGNORE_INVISIBLE
            )
            ->will($this->returnValue($this->_mockMetadataForm));

        $this->assertEquals(
            $this->_expected,
            $this->_dataHelper->extractCustomerData(
                $this->_mockRequest,
                self::FORM_CODE,
                self::ENTITY,
                $this->_additionalAttributes,
                self::SCOPE
            )
        );
    }
}
