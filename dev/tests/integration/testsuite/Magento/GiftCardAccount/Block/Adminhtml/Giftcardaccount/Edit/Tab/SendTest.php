<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_SendTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Send */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_GiftCardAccount_Model_Giftcardaccount');
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('current_giftcardaccount', $model);

        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');

        $this->_block = $layout
            ->createBlock('Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Send');
    }

    protected function tearDown()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('current_giftcardaccount');
        parent::tearDown();
    }

    /**
     * Test Prepare Form in Single Store mode
     *
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testPrepareFormSingleStore()
    {
        $this->_block->initForm();
        $form = $this->_block->getForm();
        $this->assertEquals('base_fieldset', $form->getElement('base_fieldset')->getId());
        $this->assertNull($form->getElement('store_id'));
    }

    /**
     * Test Prepare Form in Multiple Store mode
     *
     * @magentoConfigFixture current_store general/single_store_mode/enabled 0
     */
    public function testPrepareFormMultipleStore()
    {
        $this->_block->initForm();
        $form = $this->_block->getForm();
        $this->assertEquals('base_fieldset', $form->getElement('base_fieldset')->getId());

        $element = $form->getElement('store_id');
        $this->assertNotNull($element);
        $this->assertInstanceOf('Magento_Data_Form_Element_Select', $element);
        $this->assertEquals('store_id', $element->getId());
    }
}
