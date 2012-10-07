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
class Mage_Adminhtml_Block_System_Store_Edit_Form_GroupTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Adminhtml_Block_System_Store_Edit_Form_Group */
    protected $_block;

    public static function setUpBeforeClass()
    {
        $registryData = array(
            'store_type' => 'group',
            'store_data' => new Mage_Core_Model_Store_Group(),
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

        $this->_block = new Mage_Adminhtml_Block_System_Store_Edit_Form_Group();
        $this->_block->setLayout($layout);

        $this->_block->toHtml();
    }

    public function testPrepareForm()
    {
        $form = $this->_block->getForm();
        $this->assertEquals('group_fieldset', $form->getElement('group_fieldset')->getId());
        $this->assertEquals('group_name', $form->getElement('group_name')->getId());
        $this->assertEquals('group', $form->getElement('store_type')->getValue());
    }
}
