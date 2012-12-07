<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Selenium_Uimap_ElementsCollectionTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $objects = array();
        $instance = new Mage_Selenium_Uimap_ElementsCollection('elementType', $objects);
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $instance);
    }

    /**
     * @covers Mage_Selenium_Uimap_ElementsCollection::__get
     */
    public function test__get()
    {
        $uipage = $this->getUimapPage('admin', 'create_customer');
        $buttons = $uipage->getMainForm()->getAllButtons();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
        foreach ($buttons as $buttonName => $buttonXPath) {
            $this->assertNotEmpty($buttonXPath);
        }
    }

    /**
     * @covers Mage_Selenium_Uimap_ElementsCollection::get
     */
    public function testGet()
    {
        $uipage = $this->getUimapPage('admin', 'create_customer');
        $button = $uipage->getAllButtons()->get('save_customer');
        $this->assertInternalType('string', $button);
    }

    /**
     * @covers Mage_Selenium_Uimap_ElementsCollection::getType
     */
    public function testGetType()
    {
        $instance = new Mage_Selenium_Uimap_ElementsCollection('elementType', array());
        $this->assertEquals('elementType', $instance->getType());

        $uipage = $this->getUimapPage('admin', 'create_customer');
        $fieldsets = $uipage->getMainForm()->getAllFieldsets();
        $fieldsetsType = $fieldsets->getType();
        $this->assertEquals('fieldsets', $fieldsetsType);
    }
}