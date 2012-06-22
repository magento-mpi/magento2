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
class Mage_Selenium_Uimap_FieldsetTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Uimap_Fieldset::__construct
     */
    public function test__construct()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $dataArray = $fileHelper->loadYamlFile
                (SELENIUM_TESTS_BASEDIR . '\fixture\default\core\Mage\UnitTest\data\UimapTests.yml');
        $fieldsetContainer = $dataArray['fieldset'];
        $instance = new Mage_Selenium_Uimap_Fieldset('fieldsetId', $fieldsetContainer);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Fieldset', $instance);
        $this->assertEquals("//div[@class='the-fieldset']", $instance->getXPath());
    }

    /**
     * @covers Mage_Selenium_Uimap_Fieldset::getFieldsetElements
     */
    public function testGetFieldsetElements()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $dataArray = $fileHelper->loadYamlFile
                (SELENIUM_TESTS_BASEDIR . '\fixture\default\core\Mage\UnitTest\data\UimapTests.yml');
        $fieldsetContainer = $dataArray['fieldset'];
        $instance = new Mage_Selenium_Uimap_Fieldset('fieldsetId', $fieldsetContainer);
        $elements = $instance->getFieldsetElements();
        $this->assertInternalType('array', $elements);
        $this->assertArrayHasKey('button', $elements);
        $this->assertArrayHasKey('checkbox', $elements);
        $this->assertEquals($elements['checkbox']['first_checkbox'], "//div[@class='the-fieldset']//input[@id='the-first-checkbox']");

        $fieldsetContainer = array();
        $instance = new Mage_Selenium_Uimap_Fieldset('fieldsetId', $fieldsetContainer);
        $elements = $instance->getFieldsetElements();
        $this->assertInternalType('array', $elements);
        $this->assertEmpty($elements);
    }
}