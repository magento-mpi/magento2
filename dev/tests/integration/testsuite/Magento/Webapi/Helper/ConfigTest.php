<?php
/**
 * Config helper tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Class implements tests for Magento_Webapi_Helper_Data class.
 */
class Magento_Webapi_Helper_ConfigTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Helper_Config */
    protected $_helper;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $objectManager = Mage::getObjectManager();
        $this->_helper = $objectManager->get('Magento_Webapi_Helper_Config');
        parent::setUp();
    }

    /**
     * @dataProvider dataProviderForTestConvertSingularToPlural
     */
    public function testConvertSingularToPlural($singular, $expectedPlural)
    {
        $this->assertEquals(
            $expectedPlural,
            $this->_helper->convertSingularToPlural($singular),
            "Conversion from singular to plural was performed incorrectly."
        );
    }

    public static function dataProviderForTestConvertSingularToPlural()
    {
        return array(
            array('customer', 'customers'),
            array('category', 'categories'),
            array('webapi', 'webapis'),
            array('downloadable', 'downloadables'),
            array('eway', 'eways'),
            array('tax', 'taxes'),
            array('', '')
        );
    }

    /**
     * @dataProvider dataProviderTestTranslateArrayTypeName
     * @param string $typeToBeTranslated
     * @param string $expectedResult
     */
    public function testTranslateArrayTypeName($typeToBeTranslated, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->_helper->translateArrayTypeName($typeToBeTranslated),
            "Array type was translated incorrectly."
        );
    }

    public static function dataProviderTestTranslateArrayTypeName()
    {
        return array(
            array('ComplexType[]', 'ArrayOfComplexType'),
            array('string[]', 'ArrayOfString'),
            array('integer[]', 'ArrayOfInt'),
            array('bool[]', 'ArrayOfBoolean'),
        );
    }

    /**
     * @dataProvider dataProviderForTestTranslateTypeName
     * @param string $typeName
     * @param string $expectedResult
     */
    public function testTranslateTypeName($typeName, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->_helper->translateTypeName($typeName),
            "Type translation was performed incorrectly."
        );
    }

    public static function dataProviderForTestTranslateTypeName()
    {
        return array(
            array('Magento_Customer_Model_Webapi_CustomerData', 'CustomerData'),
            array('Magento_Catalog_Model_Webapi_ProductData', 'CatalogProductData'),
            array('Vendor_Customer_Model_Webapi_Customer_AddressData', 'VendorCustomerAddressData'),
            array('Producer_Module_Model_Webapi_ProducerData', 'ProducerModuleProducerData'),
            array('Producer_Module_Model_Webapi_ProducerModuleData', 'ProducerModuleProducerModuleData'),
        );
    }

    public function testTranslateTypeNameInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid parameter type "Invalid_Type_Name".');
        $this->_helper->translateTypeName('Invalid_Type_Name');
    }

    public function testGetBodyParamNameInvalidInterface()
    {
        $methodName = 'updateV1';
        $bodyPosition = 2;
        $this->setExpectedException(
            'LogicException',
            sprintf(
                'Method "%s" must have parameter for passing request body. '
                    . 'Its position must be "%s" in method interface.',
                $methodName,
                $bodyPosition
            )
        );
        $this->_helper->getOperationBodyParamName(
            Magento_Webapi_Helper_Data::createMethodReflection(
                'Vendor_Module_Controller_Webapi_Invalid_Interface',
                $methodName
            )
        );
    }

    public function testGetIdParamNameEmptyMethodInterface()
    {
        $this->setExpectedException('LogicException', 'must have at least one parameter: resource ID.');
        $this->_helper->getOperationIdParamName(
            Magento_Webapi_Helper_Data::createMethodReflection(
                'Vendor_Module_Controller_Webapi_Invalid_Interface',
                'emptyInterfaceV2'
            )
        );
    }

    public function testGetResourceNamePartsException()
    {
        $className = 'Vendor_Module_Webapi_Resource_Invalid';
        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf('The controller class name "%s" is invalid.', $className)
        );
        $this->_helper->getResourceNameParts($className);
    }

    /**
     * @dataProvider dataProviderForTestGetResourceNameParts
     * @param $className
     * @param $expectedParts
     */
    public function testGetResourceNameParts($className, $expectedParts)
    {
        $this->assertEquals(
            $expectedParts,
            $this->_helper->getResourceNameParts($className),
            "Resource parts for REST route were identified incorrectly."
        );
    }

    public static function dataProviderForTestGetResourceNameParts()
    {
        return array(
            array('Vendor_Customer_Controller_Webapi_Customer_Address', array('VendorCustomer', 'Address')),
            /** Check removal of 'Magento' prefix as well as duplicating parts ('Customer') */
            array('Magento_Customer_Controller_Webapi_Customer_Address', array('Customer', 'Address')),
        );
    }

    public function testGetIdParamException()
    {
        $className = 'Vendor_Module_Webapi_Resource_Invalid';
        $this->setExpectedException('LogicException', sprintf('"%s" is not a valid resource class.', $className));
        $this->_helper->getOperationIdParamName(
            Magento_Webapi_Helper_Data::createMethodReflection($className, 'updateV1')
        );
    }
}
