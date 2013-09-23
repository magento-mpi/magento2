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
class Magento_Adminhtml_Block_System_Store_Edit_Form_WebsiteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Block_System_Store_Edit_Form_Website
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $registryData = array(
            'store_type' => 'website',
            'store_data' => Mage::getModel('Magento_Core_Model_Website'),
            'store_action' => 'add'
        );
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        foreach ($registryData as $key => $value) {
            $objectManager->get('Magento_Core_Model_Registry')->register($key, $value);
        }

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');

        $this->_block = $layout->createBlock('Magento_Adminhtml_Block_System_Store_Edit_Form_Website');

        $this->_block->toHtml();
    }

    protected function tearDown()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('store_type');
        $objectManager->get('Magento_Core_Model_Registry')->unregister('store_data');
        $objectManager->get('Magento_Core_Model_Registry')->unregister('store_action');
    }

    public function testPrepareForm()
    {
        $form = $this->_block->getForm();
        $this->assertEquals('website_fieldset', $form->getElement('website_fieldset')->getId());
        $this->assertEquals('website_name', $form->getElement('website_name')->getId());
        $this->assertEquals('website', $form->getElement('store_type')->getValue());
    }
}
