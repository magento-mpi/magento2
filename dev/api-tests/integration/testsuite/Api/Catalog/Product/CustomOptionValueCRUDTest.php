<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture testsuite/Api/Catalog/Product/_fixture/CustomOptionValue.php
 */
class Api_Catalog_Product_CustomOptionValueCRUDTest extends Magento_Test_Webservice
{
    protected static $_lastAddedOption;

    /**
     * Product Custom Option Value CRUD test
     */
    public function testCustomOptionValueCRUD()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionValue.xml');
        $customOptionValues = self::simpleXmlToArray($valueFixture->CustomOptionValues);

        $store = (string) $valueFixture->store;
        $fixtureCustomOptionId = Magento_Test_Webservice::getFixture('customOptionId');

        $createdOptionValuesBefore = $this->call('product_custom_option_value.list', array(
            'optionId' => $fixtureCustomOptionId
        ));
        $this->assertTrue(is_array($createdOptionValuesBefore));
        $this->assertCount(2, $createdOptionValuesBefore);

        // for wsi complexObjectArray
        $customOptionValuesData = array(reset($customOptionValues));
        // Add test
        $addResult = $this->call('product_custom_option_value.add', array(
            'optionId' => $fixtureCustomOptionId,
            'data' => $customOptionValuesData,
            'store' => $store
        ));
        $this->assertTrue((bool)$addResult);

        // Get list test
        $createdOptionValuesAfter = $this->call('product_custom_option_value.list', array(
            'optionId' => $fixtureCustomOptionId,
            'store' => $store
        ));
        $this->assertTrue(is_array($createdOptionValuesAfter));
        $this->assertCount(3, $createdOptionValuesAfter);

        self::$_lastAddedOption = array_pop($createdOptionValuesAfter);
        $this->assertEquals($customOptionValues['value_1']['title'], self::$_lastAddedOption['title']);

        // Update & info tests
        $customOptionValuesToUpdate = self::simpleXmlToArray($valueFixture->CustomOptionValuesToUpdate);
        $toUpdateValues = $customOptionValuesToUpdate['value_1'];

        $updateOptionValueResult = $this->call('product_custom_option_value.update', array(
            'valueId' => self::$_lastAddedOption['value_id'],
            'data' => $toUpdateValues
        ));
        $this->assertTrue((bool)$updateOptionValueResult);

        $optionValueInfoAfterUpdate = $this->call('product_custom_option_value.info', array(
            'valueId' => self::$_lastAddedOption['value_id']
        ));

        foreach($toUpdateValues as $key => $value) {
            if(is_string($value)) {
                $this->assertEquals($value, $optionValueInfoAfterUpdate[$key]);
            }
        }
    }

    /**
     * Test successful option value add with invalid option id
     *
     * @expectedException DEFAULT_EXCEPTION
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueAddExceptionInvalidOptionId()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionValue.xml');
        $customOptionValues = self::simpleXmlToArray($valueFixture->CustomOptionValues);
        // for wsi complexObjectArray
        $customOptionValuesData = array(reset($customOptionValues));
        $this->call('product_custom_option_value.add', array(
            'optionId' => 'invalid_id',
            'data' => $customOptionValuesData,
            'store' => (string) $valueFixture->store
        ));
    }

    /**
     * Test product custom option values list with invalid option id (exception)
     *
     * @expectedException DEFAULT_EXCEPTION
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueListExceptionInvalidOptionId()
    {
        $this->call('product_custom_option_value.list', array('optionId' => 'invalid_id'));
    }

    /**
     * Test product custom option values update with invalid value id
     *
     * @expectedException DEFAULT_EXCEPTION
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueUpdateExceptionValueId()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionValue.xml');
        $customOptionValuesToUpdate = self::simpleXmlToArray($valueFixture->CustomOptionValuesToUpdate);

        $this->call('product_custom_option_value.update', array(
            'valueId' => 'invalid_id',
            'data' => $customOptionValuesToUpdate['value_1']
        ));
    }

    /**
     * Test product custom option values update with invalid title
     *
     * @expectedException DEFAULT_EXCEPTION
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueUpdateExceptionTitle()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionValue.xml');
        $customOptionValuesToUpdate = self::simpleXmlToArray($valueFixture->CustomOptionValuesToUpdate);
        $customOptionValuesToUpdate['value_1']['title'] = false;

        $this->call('product_custom_option_value.update', array(
            'valueId' => self::$_lastAddedOption['value_id'],
            'data' => $customOptionValuesToUpdate['value_1']
        ));
    }

    /**
     * Test successful option value remove
     *
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueRemove()
    {
        // Remove
        $removeOptionValueResult = $this->call('product_custom_option_value.remove', array(
            'valueId' => self::$_lastAddedOption['value_id']
        ));
        $this->assertTrue((bool)$removeOptionValueResult);

        // Delete exception test
        $this->setExpectedException(self::DEFAULT_EXCEPTION, 'Option value with requested id does not exist.');
        $this->call('product_custom_option_value.remove', array('valueId' => self::$_lastAddedOption['value_id']));
    }
}
