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
 * @magentoAppIsolation enabled
 */
class Mage_Adminhtml_Block_System_Store_Edit_Form_WebsiteTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Adminhtml_Block_System_Store_Edit_Form_Website */
    protected $_block;

    public static function setUpBeforeClass()
    {
        $registryData = array(
            'store_type' => 'website',
            'store_data' => new Mage_Core_Model_Website(),
            'store_action' => 'add'
        );
        foreach ($registryData as $key => $value) {
            Mage::register($key, $value);
        }
    }

    public static function tearDownAfterClass()
    {
        Mage::unregister('store_type');
        Mage::unregister('store_data');
        Mage::unregister('store_action');
    }

    public function setUp()
    {
        $layout = new Mage_Core_Model_Layout();

        $this->_block = new Mage_Adminhtml_Block_System_Store_Edit_Form_Website();
        $this->_block->setLayout($layout);

        $this->_block->toHtml();
    }

    public function testPrepareForm()
    {
        $form = $this->_block->getForm();
        $this->assertEquals('website_fieldset', $form->getElement('website_fieldset')->getId());
        $this->assertEquals('website_name', $form->getElement('website_name')->getId());
        $this->assertEquals('website', $form->getElement('store_type')->getValue());
    }
}
