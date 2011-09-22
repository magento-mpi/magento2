<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @magentoDataFixture Catalog/Product/_fixtures/CustomOptionValue.php
 */
class Catalog_Product_CustomOptionValueCRUDTest extends Magento_Test_Webservice
{
    protected static $_lastAddedOption;

    /**
     * Product Custom Option Value CRUD test
     */
    public function testCustomOptionValueCRUD()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/CustomOptionValue.xml');
        $customOptionValues = self::simpleXmlToArray($valueFixture->CustomOptionValues);

        $store = (string) $valueFixture->store;
        $fixtureCustomOptionId = Magento_Test_Webservice::getFixture('customOptionId');

        $createdOptionValuesBefore = $this->call('product_custom_option_value.list', array(
            $fixtureCustomOptionId,
            $store
        ));

        $this->assertTrue(is_array($createdOptionValuesBefore));
        $this->assertEquals(2, count($createdOptionValuesBefore));

        // Add test
        $addResult = $this->call('product_custom_option_value.add', array(
            $fixtureCustomOptionId,
            $customOptionValues,
            $store
        ));
        $this->assertTrue($addResult);

        // Get list test
        $createdOptionValuesAfter = $this->call('product_custom_option_value.list', array(
            $fixtureCustomOptionId,
            $store
        ));
        $this->assertTrue(is_array($createdOptionValuesAfter));
        $this->assertEquals(3, count($createdOptionValuesAfter));

        self::$_lastAddedOption = array_pop($createdOptionValuesAfter);
        $this->assertEquals($customOptionValues['value_1']['title'], self::$_lastAddedOption['title']);

        // Update & info tests
        $customOptionValuesToUpdate = self::simpleXmlToArray($valueFixture->CustomOptionValuesToUpdate);
        $toUpdateValues = $customOptionValuesToUpdate['value_1'];

        $updateOptionValueResult = $this->call('product_custom_option_value.update', array(
            self::$_lastAddedOption['value_id'],
            $toUpdateValues
        ));
        $this->assertTrue($updateOptionValueResult);

        $optionValueInfoAfterUpdate = $this->call('product_custom_option_value.info', array(
            self::$_lastAddedOption['value_id']
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
     * @expectedException Exception
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueAddExceptionInvalidOptionId()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/CustomOptionValue.xml');
        $customOptionValues = self::simpleXmlToArray($valueFixture->CustomOptionValues);

        $this->call('product_custom_option_value.add', array(
            'invalid_id',
            $customOptionValues,
            (string) $valueFixture->store
        ));
    }

    /**
     * Test product custom option values list with invalid option id (exception)
     *
     * @expectedException Exception
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueListExceptionInvalidOptionId()
    {
        $this->call('product_custom_option_value.list', array('invalid_id'));
    }

    /**
     * Test product custom option values update with invalid value id
     *
     * @expectedException Exception
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueUpdateExceptionValueId()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/CustomOptionValue.xml');
        $customOptionValuesToUpdate = self::simpleXmlToArray($valueFixture->CustomOptionValuesToUpdate);

        $this->call('product_custom_option_value.update', array(
            'invalid_id',
            $customOptionValuesToUpdate['value_1']
        ));
    }

    /**
     * Test product custom option values update with invalid title
     *
     * @expectedException Exception
     * @depends testCustomOptionValueCRUD
     */
    public function testCustomOptionValueUpdateExceptionTitle()
    {
        $valueFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/CustomOptionValue.xml');
        $customOptionValuesToUpdate = self::simpleXmlToArray($valueFixture->CustomOptionValuesToUpdate);
        unset($customOptionValuesToUpdate['value_1']['title']);

        $this->call('product_custom_option_value.update', array(
            self::$_lastAddedOption['value_id'],
            $customOptionValuesToUpdate['value_1']
        ));
    }

    /**
     * Test successful option value remove
     *
     * @depends testCustomOptionValueUpdateExceptionTitle
     */
    public function testCustomOptionValueRemove()
    {
        // Remove
        $removeOptionValueResult = $this->call('product_custom_option_value.remove', array(
            self::$_lastAddedOption['value_id']
        ));
        $this->assertTrue($removeOptionValueResult);

        // Delete exception test
        $this->setExpectedException('Exception');
        $this->call('product_custom_optionv.remove', array(self::$_lastAddedOption['value_id']));
    }
}
