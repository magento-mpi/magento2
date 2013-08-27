<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_SendTest extends PHPUnit_Framework_TestCase
{
    /** @var Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Send */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $model = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');
        Mage::register('current_giftcardaccount', $model);

        $layout = Mage::getModel('Magento_Core_Model_Layout');

        $this->_block = $layout
            ->createBlock('Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Send');
    }

    protected function tearDown()
    {
        Mage::unregister('current_giftcardaccount');
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
