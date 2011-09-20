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
        $createdOptionAfter = $this->call('product_custom_option.list', array($fixtureProductId));

        $this->assertTrue(is_array($createdOptionAfter));
        $this->assertEquals(6, count($createdOptionAfter));

        foreach ($createdOptionAfter as $option) {
            $this->assertEquals($customOptions[$option['type']]['title'], $option['title']);
        }

        // update & info
        $customOptionsToUpdate = self::simpleXmlToArray($attributeSetFixture->CustomOptionsToUpdate);
        foreach ($createdOptionAfter as $option) {
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

        // Remove
        foreach ($createdOptionAfter as $option) {
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

    public function testCustomOptionTypes()
    {
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOptionTypes.xml');
        $customOptionsTypes = self::simpleXmlToArray($attributeSetFixture);

        $optionTypes = $this->call('product_custom_option.types', array());
        $this->assertEquals($customOptionsTypes['customOptionTypes']['types'], $optionTypes);
    }
}
