<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
     */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main');
    }

    protected function tearDown()
    {
        $this->_block = null;
        Mage::unregister('entity_attribute');
        Mage::unregister('attribute_type_hidden_fields');
        Mage::unregister('attribute_type_disabled_types');
    }

    public function testPrepareFormSystemAttribute()
    {
        Mage::register('entity_attribute', new Varien_Object(
                array('entity_type' => new Varien_Object(), 'id' => 1, 'is_user_defined' => false))
        );
        $this->_block->toHtml();
        $this->assertTrue(
            $this->_block->getForm()->getElement('base_fieldset')->getContainer()->getElement('apply_to')->getDisabled()
        );
    }

    public function testPrepareFormUserDefinedAttribute()
    {
        Mage::register('entity_attribute', new Varien_Object(
                array('entity_type' => new Varien_Object(), 'id' => 1, 'is_user_defined' => true))
        );
        $this->_block->toHtml();
        $this->assertFalse(
            $this->_block->getForm()->getElement('base_fieldset')->getContainer()->getElement('apply_to')->getDisabled()
        );
    }
}
