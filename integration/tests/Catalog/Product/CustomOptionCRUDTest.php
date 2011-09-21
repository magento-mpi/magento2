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
 * @magentoDataFixture Catalog/Product/_fixtures/CustomOption.php
 */
class Catalog_Product_CustomOptionCRUDTest extends Magento_Test_Webservice
{
    protected static $createdOptionAfter;

    /**
     * Product Custom Option CRUD test
     */
    public function testCustomOptionCRUD()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOption.xml');
        $customOptions = self::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);
        $store = (string) $customOptionFixture->store;
        $fixtureProductId = Magento_Test_Webservice::getFixture('productData')->getId();

        $createdOptionBefore = $this->call('product_custom_option.list', array(
                $fixtureProductId,
                $store
            ));
        $this->assertEmpty($createdOptionBefore);

        foreach ($customOptions as $option) {
            if (isset($option['additional_fields'])
                and !is_array(reset($option['additional_fields']))) {
                $option['additional_fields'] = array($option['additional_fields']);
            }

            $addedOptionResult = $this->call('product_custom_option.add', array(
                $fixtureProductId,
                $option,
                $store
            ));
            $this->assertTrue($addedOptionResult);
        }

        // list
        self::$createdOptionAfter = $this->call('product_custom_option.list', array($fixtureProductId));

        $this->assertTrue(is_array(self::$createdOptionAfter));
        $this->assertEquals(6, count(self::$createdOptionAfter));

        foreach (self::$createdOptionAfter as $option) {
            $this->assertEquals($customOptions[$option['type']]['title'], $option['title']);
        }

        // update & info
        $updateCounter = 0;
        $customOptionsToUpdate = self::simpleXmlToArray($customOptionFixture->CustomOptionsToUpdate);
        foreach (self::$createdOptionAfter as $option) {
            $optionInfo = $this->call('product_custom_option.info', array(
                $option['option_id']
            ));
            $this->assertTrue(is_array($optionInfo));
            $this->assertGreaterThan(3, count($optionInfo));

            if (isset($customOptionsToUpdate[$option['type']])) {
                $toUpdateValues = $customOptionsToUpdate[$option['type']];
                if (isset($toUpdateValues['additional_fields'])
                    and !is_array(reset($toUpdateValues['additional_fields']))) {
                    $toUpdateValues['additional_fields'] = array($toUpdateValues['additional_fields']);
                }

                $updateOptionResult = $this->call('product_custom_option.update', array(
                    $option['option_id'],
                    $toUpdateValues
                ));
                $this->assertTrue($updateOptionResult);
                $updateCounter ++;

                $optionInfoAfterUpdate = $this->call('product_custom_option.info', array(
                    $option['option_id']
                ));

                foreach($toUpdateValues as $key => $value) {
                    if(is_string($value)) {
                        self::assertEquals($value, $optionInfoAfterUpdate[$key]);
                    }
                }

                if (isset($toUpdateValues['additional_fields'])) {
                    $updateAdditionalFields = reset($toUpdateValues['additional_fields']);
                    $actualAdditionalFields = reset($optionInfoAfterUpdate['additional_fields']);
                    foreach ($updateAdditionalFields as $key => $value) {
                        if (is_string($value)) {
                            self::assertEquals($value, $actualAdditionalFields[$key]);
                        }
                    }
                }
            }
        }

        self::assertEquals(3, $updateCounter);
    }

    /**
     * Product Custom Option ::types() method test 
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionTypes()
    {
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOptionTypes.xml');
        $customOptionsTypes = self::simpleXmlToArray($attributeSetFixture);

        $optionTypes = $this->call('product_custom_option.types', array());
        $this->assertEquals($customOptionsTypes['customOptionTypes']['types'], $optionTypes);
    }

    protected function _createOption($productId, $option, $store = null)
    {
        if (isset($option['additional_fields'])
            and !is_array(reset($option['additional_fields']))) {
            $option['additional_fields'] = array($option['additional_fields']);
        }

        return $this->call('product_custom_option.add', array(
            $productId,
            $option,
            $store
        ));
    }

    protected function _updateOption($optionId, $option, $store = null)
    {
        if (isset($option['additional_fields'])
            and !is_array(reset($option['additional_fields']))) {
            $option['additional_fields'] = array($option['additional_fields']);
        }

        return $this->call('product_custom_option.update', array(
            $optionId,
            $option,
            $store
        ));
    }

    /**
     * Test option add exception: product_not_exists
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionAddExceptionProductNotExists()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOption.xml');
        $customOptions = self::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $option = reset($customOptions);
        $this->setExpectedException('Exception');
        $this->call('product_custom_option.add', array(
            'invalid_id',
            $option
        ));
    }

    /**
     * Test option add without additional fields exception: invalid_data
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionAddExceptionAdditionalFieldsNotSet()
    {
        $fixtureProductId = Magento_Test_Webservice::getFixture('productData')->getId();
        $customOptionFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOption.xml');
        $customOptions = self::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $option = reset($customOptions);
        unset($option['additional_fields']);
        $this->setExpectedException('Exception');
        $this->call('product_custom_option.add', array(
            $fixtureProductId,
            $option
        ));
    }

    /**
     * Test option date_time add with store id exception: store_not_exists
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionDateTimeAddExceptionStoreNotExist()
    {
        $fixtureProductId = Magento_Test_Webservice::getFixture('productData')->getId();
        $customOptionFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOption.xml');
        $customOptions = self::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $option = reset($customOptions);
        $this->setExpectedException('Exception');
        $this->call('product_custom_option.add', array(
            $fixtureProductId,
            $option,
            'some_store_name'
        ));
    }

    /**
     * Test product custom options list exception: product_not_exists
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionListExceptionProductNotExists()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOption.xml');
        $store = (string) $customOptionFixture->store;

        $this->setExpectedException('Exception');
        $createdOptionBefore = $this->call('product_custom_option.list', array(
                'unknown_id',
                $store
            ));
    }

    /**
     * Test product custom options list exception: store_not_exists
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionListExceptionStoreNotExists()
    {
        $fixtureProductId = Magento_Test_Webservice::getFixture('productData')->getId();

        $this->setExpectedException('Exception');
        $createdOptionBefore = $this->call('product_custom_option.list', array(
                $fixtureProductId,
                'unknown_store_name'
            ));
    }


    /**
     * Test option add with invalid type
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionUpdateExceptionInvalidType()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOption.xml');
        $customOptions = self::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $store = (string) $customOptionFixture->store;
        $fixtureProductId = Magento_Test_Webservice::getFixture('productData')->getId();
        $fixtureCustomOptionId = Magento_Test_Webservice::getFixture('customOptionId');

        $customOptionsToUpdate = self::simpleXmlToArray($customOptionFixture->CustomOptionsToUpdate);
        $option = reset(self::$createdOptionAfter);

        $toUpdateValues = $customOptionsToUpdate[$option['type']];
        if (isset($toUpdateValues['additional_fields'])
            and !is_array(reset($toUpdateValues['additional_fields']))) {
            $toUpdateValues['additional_fields'] = array($toUpdateValues['additional_fields']);
        }
        $toUpdateValues['type'] = 'unknown_type';

        $this->setExpectedException('Exception');
        $this->_updateOption($option['option_id'], $toUpdateValues);
    }


    /**
     * Test option remove and exception
     *
     * @depends testCustomOptionUpdateExceptionInvalidType
     */
    public function testCustomOptionRemove()
    {
        // Remove
        foreach (self::$createdOptionAfter as $option) {
            $removeOptionResult = $this->call('product_custom_option.remove', array(
                $option['option_id']
            ));
            $this->assertTrue($removeOptionResult);
        }

        // Delete exception test
        try {
            $this->call('product_custom_option.remove', array(mt_rand(99999, 999999)));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }
    }

}
