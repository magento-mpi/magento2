<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * @magentoApiDataFixture Mage/Catalog/Product/_fixture/CustomOptionValue.php
 */
class Mage_Catalog_Product_CustomOptionValueCRUDTest extends Magento_Test_TestCase_ApiAbstract
{
    protected static $_lastAddedOption;

    /**
     * Product Custom Option Value CRUD test
     */
    public function testCustomOptionValueCRUD()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionValue.xml');
        $customOptionValues = self::simpleXmlToObject($valueFixture->CustomOptionValues);

        $store = (string)$valueFixture->store;
        $fixtureCustomOptionId = Magento_Test_TestCase_ApiAbstract::getFixture('customOptionId');

        $createdOptionValuesBefore = $this->call(
            'catalogProductCustomOptionValueList',
            array(
                'optionId' => $fixtureCustomOptionId
            )
        );
        $this->assertTrue(is_array($createdOptionValuesBefore));
        $this->assertCount(2, $createdOptionValuesBefore);

        // for wsi complexObjectArray
        $customOptionValuesData = array(reset($customOptionValues));
        // Add test
        $addResult = $this->call(
            'catalogProductCustomOptionValueAdd',
            array(
                'optionId' => $fixtureCustomOptionId,
                'data' => $customOptionValuesData,
                'store' => $store
            )
        );
        $this->assertTrue((bool)$addResult);

        // Get list test
        $createdOptionValuesAfter = $this->call(
            'catalogProductCustomOptionValueList',
            array(
                'optionId' => $fixtureCustomOptionId,
                'store' => $store
            )
        );
        $this->assertTrue(is_array($createdOptionValuesAfter));
        $this->assertCount(3, $createdOptionValuesAfter);

        self::$_lastAddedOption = array_pop($createdOptionValuesAfter);
        $this->assertEquals($customOptionValues['value_1']['title'], self::$_lastAddedOption['title']);

        // Update & info tests
        $customOptionValuesToUpdate = self::simpleXmlToObject($valueFixture->CustomOptionValuesToUpdate);
        $toUpdateValues = $customOptionValuesToUpdate['value_1'];

        $updateOptionValueResult = $this->call(
            'catalogProductCustomOptionValueUpdate',
            array(
                'valueId' => self::$_lastAddedOption['value_id'],
                'data' => $toUpdateValues
            )
        );
        $this->assertTrue((bool)$updateOptionValueResult);

        $optionValueInfoAfterUpdate = $this->call(
            'catalogProductCustomOptionValueInfo',
            array(
                'valueId' => self::$_lastAddedOption['value_id']
            )
        );

        foreach ($toUpdateValues as $key => $value) {
            if (is_string($value)) {
                $this->assertEquals($value, $optionValueInfoAfterUpdate[$key]);
            }
        }
    }

    /**
     * Test successful option value add with invalid option id
     *
     * @expectedException SoapFault
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueAddExceptionInvalidOptionId()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionValue.xml');
        $customOptionValues = self::simpleXmlToObject($valueFixture->CustomOptionValues);
        // for wsi complexObjectArray
        $customOptionValuesData = array(reset($customOptionValues));
        $this->call(
            'catalogProductCustomOptionValueAdd',
            array(
                'optionId' => 'invalid_id',
                'data' => $customOptionValuesData,
                'store' => (string)$valueFixture->store
            )
        );
    }

    /**
     * Test product custom option values list with invalid option id (exception)
     *
     * @expectedException SoapFault
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueListExceptionInvalidOptionId()
    {
        $this->call('catalogProductCustomOptionValueList', array('optionId' => 'invalid_id'));
    }

    /**
     * Test product custom option values update with invalid value id
     *
     * @expectedException SoapFault
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueUpdateExceptionValueId()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionValue.xml');
        $customOptionValuesToUpdate = self::simpleXmlToObject($valueFixture->CustomOptionValuesToUpdate);

        $this->call(
            'catalogProductCustomOptionValueUpdate',
            array(
                'valueId' => 'invalid_id',
                'data' => $customOptionValuesToUpdate['value_1']
            )
        );
    }

    /**
     * Test product custom option values update with invalid title
     *
     * @expectedException SoapFault
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueUpdateExceptionTitle()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionValue.xml');
        $customOptionValuesToUpdate = self::simpleXmlToObject($valueFixture->CustomOptionValuesToUpdate);
        $customOptionValuesToUpdate['value_1']['title'] = false;

        $this->call(
            'catalogProductCustomOptionValueUpdate',
            array(
                'valueId' => self::$_lastAddedOption['value_id'],
                'data' => $customOptionValuesToUpdate['value_1']
            )
        );
    }

    /**
     * Test successful option value remove
     *
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueRemove()
    {
        // Remove
        $removeOptionValueResult = $this->call(
            'catalogProductCustomOptionValueRemove',
            array(
                'valueId' => self::$_lastAddedOption['value_id']
            )
        );
        $this->assertTrue((bool)$removeOptionValueResult);

        // Delete exception test
        $this->setExpectedException(self::DEFAULT_EXCEPTION, 'Option value with requested ID does not exist.');
        $this->call('catalogProductCustomOptionValueRemove', array('valueId' => self::$_lastAddedOption['value_id']));
    }
}
