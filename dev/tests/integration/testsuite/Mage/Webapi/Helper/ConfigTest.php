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
 * Class implements tests for Mage_Webapi_Helper_Data class.
 */
class Mage_Webapi_Helper_ConfigTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Helper_Config */
    protected $_helper;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $objectManager = Mage::getObjectManager();
        $this->_helper = $objectManager->get('Mage_Webapi_Helper_Config');
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
            array('Mage_Customer_Model_Webapi_CustomerData', 'CustomerData'),
            array('Mage_Catalog_Model_Webapi_ProductData', 'CatalogProductData'),
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

}
