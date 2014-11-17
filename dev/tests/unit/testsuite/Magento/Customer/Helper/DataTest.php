<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper;

/**
 * Class DataTest
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    const FORM_CODE = 'FORM_CODE';

    const ENTITY = 'ENTITY';

    const SCOPE = 'SCOPE';

    protected $_expected = [
        'filter_key' => 'filter_value',
        'is_in_request_data' => 'request_data_value',
        'is_not_in_request_data' => false,
        'attribute_is_front_end_input' => true
    ];

    /** @var \Magento\Customer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $_dataHelper;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_mockRequest;

    /** @var array */
    protected $_additionalAttributes;

    /** @var \Magento\Customer\Model\Metadata\Form|\PHPUnit_Framework_MockObject_MockObject */
    protected $_mockMetadataForm;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Framework\ObjectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectFactoryMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    /** @var \Magento\Customer\Helper\Data */
    protected $model;

    public function setUp()
    {
        $this->objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_mockRequest = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            ['getPost', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam', 'getCookie'],
            [],
            '',
            false
        );
        $this->_additionalAttributes = ['is_in_request_data', 'is_not_in_request_data'];
        $this->_mockMetadataForm = $this->getMockBuilder(
            '\Magento\Customer\Model\Metadata\Form'
        )->disableOriginalConstructor()->getMock();

        $this->scopeConfigMock = $this->getMock(
            'Magento\Framework\App\Config\ScopeConfigInterface',
            ['getValue', 'isSetFlag'],
            [],
            '',
            false
        );
        $this->objectFactoryMock = $this->getMock(
            'Magento\Framework\ObjectFactory',
            ['create'],
            [],
            '',
            false
        );
    }

    protected function prepareExtractCustomerData()
    {
        $requestData = ['is_in_request_data' => 'request_data_value'];

        $objectMock = $this->getMock(
            'Magento\Framework\Object',
            ['getData'],
            [],
            '',
            false
        );

        $this->_dataHelper = $this->objectManagerHelper->getObject(
            'Magento\Customer\Helper\Data',
            [
                'objectFactory' => $this->objectFactoryMock
            ]
        );
        $this->objectFactoryMock->expects($this->once())
            ->method('create')
            ->with(['data' => $requestData])
            ->will($this->returnValue($objectMock));
        $objectMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($requestData));

        $filteredData = [
            'filter_key' => 'filter_value',
            'attribute_is_not_front_end_input' => false,
            'attribute_is_front_end_input' => true
        ];
        $this->_mockMetadataForm->expects($this->once())
            ->method('extractData')
            ->with($this->_mockRequest, self::SCOPE)
            ->will($this->returnValue($filteredData));

        $this->_mockRequest->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($requestData));

        $attributeIsFrontEndInput = $this->getMockBuilder(
            '\Magento\Customer\Service\V1\Data\Eav\AttributeMetadata'
        )->disableOriginalConstructor()->getMock();
        $attributeIsFrontEndInput->expects(
            $this->once()
        )->method(
            'getAttributeCode'
        )->will(
            $this->returnValue('attribute_is_front_end_input')
        );
        $attributeIsFrontEndInput->expects(
            $this->once()
        )->method(
            'getFrontendInput'
        )->will(
            $this->returnValue('boolean')
        );

        $attributeIsNotFrontEndInput = $this->getMockBuilder(
            '\Magento\Customer\Service\V1\Data\Eav\AttributeMetadata'
        )->disableOriginalConstructor()->getMock();
        $attributeIsNotFrontEndInput->expects(
            $this->once()
        )->method(
            'getAttributeCode'
        )->will(
            $this->returnValue('attribute_is_not_front_end_input')
        );
        $attributeIsNotFrontEndInput->expects(
            $this->once()
        )->method(
            'getFrontendInput'
        )->will(
            $this->returnValue(false)
        );

        $formAttributes = [$attributeIsFrontEndInput, $attributeIsNotFrontEndInput];
        $this->_mockMetadataForm->expects(
            $this->once()
        )->method(
            'getAttributes'
        )->will(
            $this->returnValue($formAttributes)
        );
    }

    public function testExtractCustomerData()
    {
        $this->prepareExtractCustomerData();
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
        $this->prepareExtractCustomerData();
        /** @var \Magento\Customer\Model\Metadata\FormFactory|\PHPUnit_Framework_MockObject_MockObject */
        $mockFormFactory = $this->getMockBuilder('Magento\Customer\Model\Metadata\FormFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $mockFormFactory->expects($this->once())
            ->method('create')
            ->with(
                self::ENTITY,
                self::FORM_CODE,
                [],
                false,
                \Magento\Customer\Model\Metadata\Form::DONT_IGNORE_INVISIBLE
            )->will($this->returnValue($this->_mockMetadataForm));

        $this->_dataHelper = $this->objectManagerHelper->getObject(
            'Magento\Customer\Helper\Data',
            [
                'objectFactory' => $this->objectFactoryMock,
                'formFactory' => $mockFormFactory
            ]
        );;

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

    public function testGetCustomerGroupIdBasedOnVatNumberWithoutAutoAssign()
    {
        $arguments = [
            'scopeConfig' => $this->scopeConfigMock
        ];
        $this->model = $this->objectManagerHelper->getObject('Magento\Customer\Helper\Data', $arguments);

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_GROUP_AUTO_ASSIGN,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                'store'
            )->will($this->returnValue(false));

        $vatResult = $this->getMock(
            'Magento\Framework\Object',
            [],
            [],
            '',
            false
        );

        $this->assertNull($this->model->getCustomerGroupIdBasedOnVatNumber('GB', $vatResult, 'store'));
    }

    /**
     * @param string $countryCode
     * @param bool $resultValid
     * @param bool $resultSuccess
     * @param string $merchantCountryCode
     * @param int $vatDomestic
     * @param int $vatIntra
     * @param int $vatInvalid
     * @param int $vatError
     * @param int|null $groupId
     * @dataProvider dataProviderGetCustomerGroupIdBasedOnVatNumber
     */
    public function testGetCustomerGroupIdBasedOnVatNumber(
        $countryCode,
        $resultValid,
        $resultSuccess,
        $merchantCountryCode,
        $vatDomestic,
        $vatIntra,
        $vatInvalid,
        $vatError,
        $groupId
    ) {
        $arguments = [
            'scopeConfig' => $this->scopeConfigMock
        ];
        $this->model = $this->objectManagerHelper->getObject('Magento\Customer\Helper\Data', $arguments);

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_GROUP_AUTO_ASSIGN,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                'store'
            )->will($this->returnValue(true));

        $configMap = [
            [
                \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_VIV_DOMESTIC_GROUP,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                'store',
                $vatDomestic
            ],
            [
                \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_VIV_INTRA_UNION_GROUP,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                'store',
                $vatIntra
            ],
            [
                \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_VIV_INVALID_GROUP,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                'store',
                $vatInvalid
            ],
            [
                \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_VIV_ERROR_GROUP,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                'store',
                $vatError
            ],
            [
                \Magento\Customer\Helper\Data::XML_PATH_MERCHANT_COUNTRY_CODE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                'store',
                $merchantCountryCode
            ],
        ];
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->will($this->returnValueMap($configMap));

        $vatResult = $this->getMock(
            'Magento\Framework\Object',
            ['getIsValid', 'getRequestSuccess'],
            [],
            '',
            false
        );
        $vatResult->expects($this->any())
            ->method('getIsValid')
            ->will($this->returnValue($resultValid));
        $vatResult->expects($this->any())
            ->method('getRequestSuccess')
            ->will($this->returnValue($resultSuccess));

        $this->assertEquals(
            $groupId,
            $this->model->getCustomerGroupIdBasedOnVatNumber($countryCode, $vatResult, 'store')
        );
    }

    /**
     * @return array
     */
    public function dataProviderGetCustomerGroupIdBasedOnVatNumber()
    {
        return [
            ['US', false, false, 'US', null, null, null, null, 0],
            ['US', false, false, 'GB', null, null, null, null, 0],
            ['US', true, false, 'US', null, null, null, null, 0],
            ['US', false, true, 'US', null, null, null, null, 0],
            ['GB', false, false, 'GB', 3, 4, 5, 6, 6],
            ['GB', false, false, 'DE', 3, 4, 5, 6, 6],
            ['GB', true, true, 'GB', 3, 4, 5, 6, 3],
            ['GB', true, true, 'DE', 3, 4, 5, 6, 4],
            ['GB', false, true, 'DE', 3, 4, 5, 6, 5],
            ['GB', false, true, 'GB', 3, 4, 5, 6, 5],
            ['GB', false, false, 'GB', null, null, null, null, 0],
            ['GB', false, false, 'DE', null, null, null, null, 0],
            ['GB', true, true, 'GB', null, null, null, null, 0],
            ['GB', true, true, 'DE', null, null, null, null, 0],
            ['GB', false, true, 'DE', null, null, null, null, 0],
            ['GB', false, true, 'GB', null, null, null, null, 0],
        ];
    }
}
