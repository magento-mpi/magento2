<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * @magentoDataFixture Mage/Catalog/Model/Product/Api/_fixture/CustomOption.php
 */
class Mage_Catalog_Model_Product_Api_CustomOptionCRUDTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected static $createdOptionAfter;

    /**
     * Product Custom Option CRUD test
     */
    public function testCustomOptionCRUD()
    {
        $this->markTestSkipped("Skipped due to bug MAGETWO-5273.");
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOption.xml');
        $customOptions = Magento_Test_Helper_Api::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);
        $store = (string)$customOptionFixture->store;
        $fixtureProductId = Mage::registry('productData')->getId();

        $createdOptionBefore = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionList',
            array(
                'productId' => $fixtureProductId,
                'store' => $store
            )
        );
        $this->assertEmpty($createdOptionBefore);

        foreach ($customOptions as $option) {
            if (isset($option['additional_fields'])
                and !is_array(reset($option['additional_fields']))
            ) {
                $option['additional_fields'] = array($option['additional_fields']);
            }

            $addedOptionResult = Magento_Test_Helper_Api::call(
                $this,
                'catalogProductCustomOptionAdd',
                array(
                    'productId' => $fixtureProductId,
                    'data' => (object)$option,
                    'store' => $store
                )
            );
            $this->assertTrue((bool)$addedOptionResult);
        }

        // list
        self::$createdOptionAfter = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionList',
            array(
                'productId' => $fixtureProductId,
                'store' => $store
            )
        );

        $this->assertTrue(is_array(self::$createdOptionAfter));
        $this->assertEquals(count($customOptions), count(self::$createdOptionAfter));

        foreach (self::$createdOptionAfter as $option) {
            $this->assertEquals($customOptions[$option['type']]['title'], $option['title']);
        }

        // update & info
        $updateCounter = 0;
        $customOptionsToUpdate = Magento_Test_Helper_Api::simpleXmlToArray(
            $customOptionFixture->CustomOptionsToUpdate
        );
        foreach (self::$createdOptionAfter as $option) {
            $optionInfo = Magento_Test_Helper_Api::call(
                $this,
                'catalogProductCustomOptionInfo',
                array(
                    'optionId' => $option['option_id']
                )
            );

            $this->assertTrue(is_array($optionInfo));
            $this->assertGreaterThan(3, count($optionInfo));

            if (isset($customOptionsToUpdate[$option['type']])) {
                $toUpdateValues = $customOptionsToUpdate[$option['type']];
                if (isset($toUpdateValues['additional_fields'])
                    and !is_array(reset($toUpdateValues['additional_fields']))
                ) {
                    $toUpdateValues['additional_fields'] = array($toUpdateValues['additional_fields']);
                }

                $updateOptionResult = Magento_Test_Helper_Api::call(
                    $this,
                    'catalogProductCustomOptionUpdate',
                    array(
                        'optionId' => $option['option_id'],
                        'data' => $toUpdateValues
                    )
                );
                $this->assertTrue((bool)$updateOptionResult);
                $updateCounter++;

                $optionInfoAfterUpdate = Magento_Test_Helper_Api::call(
                    $this,
                    'catalogProductCustomOptionInfo',
                    array(
                        'optionId' => $option['option_id']
                    )
                );

                foreach ($toUpdateValues as $key => $value) {
                    if (is_string($value)) {
                        self::assertEquals($value, $optionInfoAfterUpdate[$key]);
                    }
                }

                if (isset($toUpdateValues['additional_fields'])) {
                    $updateAdditionalFields = reset($toUpdateValues['additional_fields']);
                    if (TESTS_WEBSERVICE_TYPE == PHPUnit_Framework_TestCase::TYPE_SOAP_WSI) {
                        // incorrect in case additional_fields count > 1
                        $actualAdditionalFields = $optionInfoAfterUpdate['additional_fields'];
                    } else {
                        $actualAdditionalFields = reset($optionInfoAfterUpdate['additional_fields']);
                    }
                    foreach ($updateAdditionalFields as $key => $value) {
                        if (is_string($value)) {
                            self::assertEquals($value, $actualAdditionalFields[$key]);
                        }
                    }
                }
            }
        }

        $this->assertCount($updateCounter, $customOptionsToUpdate);
    }

    /**
     * Product Custom Option ::types() method test
     */
    public function testCustomOptionTypes()
    {
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOptionTypes.xml');
        $customOptionsTypes = Magento_Test_Helper_Api::simpleXmlToArray($attributeSetFixture);

        $optionTypes = Magento_Test_Helper_Api::call($this, 'catalogProductCustomOptionTypes', array());
        $this->assertEquals($customOptionsTypes['customOptionTypes']['types'], $optionTypes);
    }

    /**
     * Update custom option
     *
     * @param int $optionId
     * @param array $option
     * @param int $store
     *
     * @return boolean
     */
    protected function _updateOption($optionId, $option, $store = null)
    {
        if (isset($option['additional_fields'])
            and !is_array(reset($option['additional_fields']))
        ) {
            $option['additional_fields'] = array($option['additional_fields']);
        }

        return Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionUpdate',
            array(
                'optionId' => $optionId,
                'data' => $option,
                'store' => $store
            )
        );
    }

    /**
     * Test option add exception: product_not_exists
     */
    public function testCustomOptionAddExceptionProductNotExists()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOption.xml');
        $customOptions = Magento_Test_Helper_Api::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $option = reset($customOptions);
        if (isset($option['additional_fields'])
            and !is_array(reset($option['additional_fields']))
        ) {
            $option['additional_fields'] = array($option['additional_fields']);
        }
        $this->setExpectedException('SoapFault');
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionAdd',
            array(
                'productId' => 'invalid_id',
                'data' => $option
            )
        );
    }

    /**
     * Test option add without additional fields exception: invalid_data
     */
    public function testCustomOptionAddExceptionAdditionalFieldsNotSet()
    {
        $fixtureProductId = Mage::registry('productData')->getId();
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOption.xml');
        $customOptions = Magento_Test_Helper_Api::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $option = $customOptions['field'];
        $option['additional_fields'] = array();

        $this->setExpectedException('SoapFault', 'Provided data is invalid.');
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionAdd',
            array('productId' => $fixtureProductId, 'data' => $option)
        );
    }

    /**
     * Test option date_time add with store id exception: store_not_exists
     */
    public function testCustomOptionDateTimeAddExceptionStoreNotExist()
    {
        $fixtureProductId = Mage::registry('productData')->getId();
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOption.xml');
        $customOptions = Magento_Test_Helper_Api::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $option = reset($customOptions);
        if (isset($option['additional_fields'])
            and !is_array(reset($option['additional_fields']))
        ) {
            $option['additional_fields'] = array($option['additional_fields']);
        }
        $this->setExpectedException('SoapFault');
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionAdd',
            array(
                'productId' => $fixtureProductId,
                'data' => $option,
                'store' => 'some_store_name'
            )
        );
    }

    /**
     * Test product custom options list exception: product_not_exists
     */
    public function testCustomOptionListExceptionProductNotExists()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOption.xml');
        $store = (string)$customOptionFixture->store;

        $this->setExpectedException('SoapFault');
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionList',
            array(
                'productId' => 'unknown_id',
                'store' => $store
            )
        );
    }

    /**
     * Test product custom options list exception: store_not_exists
     */
    public function testCustomOptionListExceptionStoreNotExists()
    {
        $fixtureProductId = Mage::registry('productData')->getId();

        $this->setExpectedException('SoapFault');
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionList',
            array(
                'productId' => $fixtureProductId,
                'store' => 'unknown_store_name'
            )
        );
    }

    /**
     * Test option add with invalid type
     *
     * @expectedException SoapFault
     */
    public function testCustomOptionUpdateExceptionInvalidType()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/CustomOption.xml');

        $customOptionsToUpdate = Magento_Test_Helper_Api::simpleXmlToArray(
            $customOptionFixture->CustomOptionsToUpdate
        );
        $option = reset(self::$createdOptionAfter);

        $toUpdateValues = $customOptionsToUpdate[$option->type];
        $toUpdateValues['type'] = 'unknown_type';

        $this->_updateOption($option->option_id, $toUpdateValues);
    }

    /**
     * Test option remove and exception
     *
     * @expectedException SoapFault
     * @depends testCustomOptionUpdateExceptionInvalidType
     */
    public function testCustomOptionRemove()
    {
        // Remove
        foreach (self::$createdOptionAfter as $option) {
            $removeOptionResult = Magento_Test_Helper_Api::call(
                $this,
                'catalogProductCustomOptionRemove',
                array(
                    'optionId' => $option->option_id
                )
            );
            $this->assertTrue((bool)$removeOptionResult);
        }

        // Delete exception test
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCustomOptionRemove',
            array('optionId' => mt_rand(99999, 999999))
        );
    }
}
