<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * @magentoDataFixture Api/Mage/Catalog/Product/_fixture/CustomOptionValue.php
 */
class Mage_Catalog_Product_CustomOptionValueCRUDTest extends PHPUnit_Framework_TestCase
{
    protected static $_lastAddedOption;

    /**
     * Product Custom Option Value CRUD test
     */
    public function testCustomOptionValueCRUD()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionValue.xml');
        $customOptionValues = Magento_Test_Helper_Api::simpleXmlToArray($valueFixture->CustomOptionValues);

        $store = (string)$valueFixture->store;
        $fixtureCustomOptionId = PHPUnit_Framework_TestCase::getFixture('customOptionId');

        $createdOptionValuesBefore = Magento_Test_Helper_Api::call(
            $this,
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
        $addResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueAdd',
            array(
                'optionId' => $fixtureCustomOptionId,
                'data' => $customOptionValuesData,
                'store' => $store
            )
        );
        $this->assertTrue((bool)$addResult);

        // Get list test
        $createdOptionValuesAfter = Magento_Test_Helper_Api::call(
            $this,
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
        $customOptionValuesToUpdate = Magento_Test_Helper_Api::simpleXmlToArray(
            $valueFixture->CustomOptionValuesToUpdate
        );
        $toUpdateValues = $customOptionValuesToUpdate['value_1'];

        $updateOptionValueResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueUpdate',
            array(
                'valueId' => self::$_lastAddedOption['value_id'],
                'data' => $toUpdateValues
            )
        );
        $this->assertTrue((bool)$updateOptionValueResult);

        $optionValueInfoAfterUpdate = Magento_Test_Helper_Api::call(
            $this,
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
        $customOptionValues = Magento_Test_Helper_Api::simpleXmlToArray($valueFixture->CustomOptionValues);
        // for wsi complexObjectArray
        $customOptionValuesData = array(reset($customOptionValues));
        Magento_Test_Helper_Api::call(
            $this,
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
        Magento_Test_Helper_Api::call($this, 'catalogProductCustomOptionValueList', array('optionId' => 'invalid_id'));
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
        $customOptionValuesToUpdate = Magento_Test_Helper_Api::simpleXmlToArray(
            $valueFixture->CustomOptionValuesToUpdate
        );

        Magento_Test_Helper_Api::call(
            $this,
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
        $customOptionValuesToUpdate = Magento_Test_Helper_Api::simpleXmlToArray(
            $valueFixture->CustomOptionValuesToUpdate
        );
        $customOptionValuesToUpdate['value_1']['title'] = false;

        Magento_Test_Helper_Api::call(
            $this,
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
        $removeOptionValueResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueRemove',
            array(
                'valueId' => self::$_lastAddedOption['value_id']
            )
        );
        $this->assertTrue((bool)$removeOptionValueResult);

        // Delete exception test
        $this->setExpectedException(self::DEFAULT_EXCEPTION, 'Option value with requested ID does not exist.');
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueRemove',
            array('valueId' => self::$_lastAddedOption['value_id'])
        );
    }
}
