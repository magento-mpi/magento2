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
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_System_Store_Edit_Form_GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Block_System_Store_Edit_Form_Group
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $registryData = array(
            'store_type' => 'group',
            'store_data' => Mage::getModel('Magento_Core_Model_Store_Group'),
            'store_action' => 'add'
        );
        foreach ($registryData as $key => $value) {
            Mage::register($key, $value);
        }


        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout');

        $this->_block = $layout->createBlock('Magento_Adminhtml_Block_System_Store_Edit_Form_Group');

        $this->_block->toHtml();
    }

    protected function tearDown()
    {
        Mage::unregister('store_type');
        Mage::unregister('store_data');
        Mage::unregister('store_action');
    }

    public function testPrepareForm()
    {
        $form = $this->_block->getForm();
        $this->assertEquals('group_fieldset', $form->getElement('group_fieldset')->getId());
        $this->assertEquals('group_name', $form->getElement('group_name')->getId());
        $this->assertEquals('group', $form->getElement('store_type')->getValue());
    }
}
