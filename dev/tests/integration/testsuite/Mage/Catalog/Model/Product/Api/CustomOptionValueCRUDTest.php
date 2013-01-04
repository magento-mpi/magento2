<?php
/**
 * Product custom options API model test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Mage/Catalog/Model/Product/Api/_files/CustomOptionValue.php
 * @magentoDbIsolation enabled
 */
class Mage_Catalog_Model_Product_Api_CustomOptionValueCRUDTest extends PHPUnit_Framework_TestCase
{
    protected static $_lastAddedOption;

    /**
     * Product Custom Option Value CRUD test
     */
    public function testCustomOptionValueCRUD()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_files/_data/xml/CustomOptionValue.xml');
        $customOptionValues = Magento_Test_Helper_Api::simpleXmlToArray($valueFixture->customOptionValues);

        $store = (string)$valueFixture->store;
        $customOptionId = Mage::registry('customOptionId');

        $optionsBefore = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueList',
            array(
                'optionId' => $customOptionId
            )
        );
        $this->assertTrue(is_array($optionsBefore));
        $this->assertCount(2, $optionsBefore);

        // for wsi complexObjectArray
        $customOptionValue = array(reset($customOptionValues));
        // Add test
        $addResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueAdd',
            array(
                'optionId' => $customOptionId,
                'data' => $customOptionValue,
                'store' => $store
            )
        );
        $this->assertTrue((bool)$addResult);

        // Get list test
        $optionsAfter = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueList',
            array(
                'optionId' => $customOptionId,
                'store' => $store
            )
        );
        $this->assertTrue(is_array($optionsAfter));
        $this->assertCount(3, $optionsAfter);

        self::$_lastAddedOption = (array)array_pop($optionsAfter);
        $this->assertEquals($customOptionValues['value_1']['title'], self::$_lastAddedOption['title']);

        // Update & info tests
        $optionValuesToUpdate = Magento_Test_Helper_Api::simpleXmlToArray(
            $valueFixture->customOptionValuesToUpdate
        );
        $toUpdateValues = $optionValuesToUpdate['value_1'];

        $updateResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueUpdate',
            array(
                'valueId' => self::$_lastAddedOption['value_id'],
                'data' => $toUpdateValues
            )
        );
        $this->assertTrue((bool)$updateResult);

        $optionAfterUpdate = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueInfo',
            array(
                'valueId' => self::$_lastAddedOption['value_id']
            )
        );

        foreach ($toUpdateValues as $key => $value) {
            if (is_string($value)) {
                $this->assertEquals($value, $optionAfterUpdate->$key);
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
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_files/_data/xml/CustomOptionValue.xml');
        $customOptionValues = Magento_Test_Helper_Api::simpleXmlToArray($valueFixture->customOptionValues);
        // for wsi complexObjectArray
        $customOptionValue = array(reset($customOptionValues));
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueAdd',
            array(
                'optionId' => 'invalid_id',
                'data' => $customOptionValue,
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
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_files/_data/xml/CustomOptionValue.xml');
        $optionValuesToUpdate = Magento_Test_Helper_Api::simpleXmlToArray(
            $valueFixture->customOptionValuesToUpdate
        );

        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueUpdate',
            array(
                'valueId' => 'invalid_id',
                'data' => $optionValuesToUpdate['value_1']
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
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_files/_data/xml/CustomOptionValue.xml');
        $optionValuesToUpdate = Magento_Test_Helper_Api::simpleXmlToArray(
            $valueFixture->customOptionValuesToUpdate
        );
        $optionValuesToUpdate['value_1']['title'] = false;

        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueUpdate',
            array(
                'valueId' => self::$_lastAddedOption['value_id'],
                'data' => $optionValuesToUpdate['value_1']
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
        $removeResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueRemove',
            array(
                'valueId' => self::$_lastAddedOption['value_id']
            )
        );
        $this->assertTrue((bool)$removeResult);

        // Delete exception test
        $this->setExpectedException('SoapFault', 'Option value with requested ID does not exist.');
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionValueRemove',
            array('valueId' => self::$_lastAddedOption['value_id'])
        );
    }
}
