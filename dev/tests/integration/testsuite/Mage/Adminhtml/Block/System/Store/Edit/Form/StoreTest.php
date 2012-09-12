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
class Mage_Adminhtml_Block_System_Store_Edit_Form_StoreTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Adminhtml_Block_System_Store_Edit_Form_Store */
    protected $_block;

    public static function setUpBeforeClass()
    {
        $registryData = array(
            'store_type' => 'store',
            'store_data' => new Mage_Core_Model_Store(),
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

        $this->_block = new Mage_Adminhtml_Block_System_Store_Edit_Form_Store();
        $this->_block->setLayout($layout);

        $this->_block->toHtml();
    }

    public function testPrepareForm()
    {
        $form = $this->_block->getForm();
        $this->assertEquals('store_fieldset', $form->getElement('store_fieldset')->getId());
        $this->assertEquals('store_name', $form->getElement('store_name')->getId());
        $this->assertEquals('store', $form->getElement('store_type')->getValue());
    }
}
