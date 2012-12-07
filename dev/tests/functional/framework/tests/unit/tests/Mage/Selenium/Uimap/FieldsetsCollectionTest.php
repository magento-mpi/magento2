<?php

/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Selenium_Uimap_FieldsetsCollectionTest extends Mage_PHPUnit_TestCase
{
    /**
         * @covers Mage_Selenium_TestCase::__construct
         */
        public function test__construct()
        {
            $instance = new Mage_Selenium_Uimap_FieldsetsCollection();
            $this->assertInstanceOf('Mage_Selenium_Uimap_FieldsetsCollection', $instance);
        }

        /**
         * @covers Mage_Selenium_TestCase::getFieldset
         */
        public function testGetFieldsetNotNull()
        {
            $instance = new Mage_Selenium_Uimap_FieldsetsCollection();
            $fieldsetValue = array();
            $fieldSet = new Mage_Selenium_Uimap_Fieldset('fieldSetId', $fieldsetValue);
            $instance['testName'] = $fieldSet;
            $this->assertEquals($instance->getFieldset('testName'), $fieldSet);
        }

        /**
         * @covers Mage_Selenium_TestCase::getFieldset
         */
        public function testGetFieldsetNull()
        {
            $instance = new Mage_Selenium_Uimap_FieldsetsCollection();
            $fieldsetValue = array();
            $fieldSet = new Mage_Selenium_Uimap_Fieldset('fieldSetId', $fieldsetValue);
            $instance['testName'] = $fieldSet;
            $this->assertEquals($instance->getFieldset('notExistingFieldSet'), null);
        }
}