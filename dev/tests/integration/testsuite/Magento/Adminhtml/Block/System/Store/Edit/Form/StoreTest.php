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
class Magento_Adminhtml_Block_System_Store_Edit_Form_StoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\System\Store\Edit\Form\Store
     */
    protected $_block;

    public function setUp()
    {
        parent::setUp();

        $registryData = array(
            'store_type' => 'store',
            'store_data' => Mage::getModel('Magento\Core\Model\Store'),
            'store_action' => 'add'
        );
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        foreach ($registryData as $key => $value) {
            $objectManager->get('Magento_Core_Model_Registry')->register($key, $value);
        }

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getSingleton('Magento\Core\Model\Layout');

        $this->_block = $layout->createBlock('Magento\Adminhtml\Block\System\Store\Edit\Form\Store');

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
        $this->assertEquals('store_fieldset', $form->getElement('store_fieldset')->getId());
        $this->assertEquals('store_name', $form->getElement('store_name')->getId());
        $this->assertEquals('store', $form->getElement('store_type')->getValue());
    }
}
