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
 * Test class for Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info
 */
class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_InfoTest extends PHPUnit_Framework_TestCase
{
    /** @var Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info */
    protected $_block;

    public static function setUpBeforeClass()
    {
        $model = new Enterprise_GiftCardAccount_Model_Giftcardaccount();
        Mage::register('current_giftcardaccount', $model);
    }

    public static function tearDownAfterClass()
    {
        Mage::unregister('current_giftcardaccount');
    }

    public function setUp()
    {
        $layout = new Mage_Core_Model_Layout();

        $this->_block = new Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info();
        $this->_block->setLayout($layout);
    }

    /**
     * Test Prepare Form in Single Store mode
     *
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testPrepareFormSingleStore()
    {
        $this->_block->initForm();
        $form = $this->_block->getForm();
        $this->assertEquals('base_fieldset', $form->getElement('base_fieldset')->getId());
        $this->assertNull($form->getElement('website_id'));
        $note = $form->getElement('balance')->getNote();
        $note = strip_tags($note);
        $this->assertNotEmpty($note);
    }

    /**
     * Test Prepare Form in Multiple Store mode
     *
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store general/single_store_mode/enabled 0
     */
    public function testPrepareFormMultipleStore()
    {
        $this->_block->initForm();
        $form = $this->_block->getForm();
        $this->assertEquals('base_fieldset', $form->getElement('base_fieldset')->getId());

        $element = $form->getElement('website_id');
        $this->assertNotNull($element);
        $this->assertInstanceOf('Varien_Data_Form_Element_Select', $element);
        $this->assertEquals('website_id', $element->getId());

        $note = $form->getElement('balance')->getNote();
        $note = strip_tags($note);
        $this->assertEmpty($note);
    }

    public function testGetCurrencyJson()
    {
        $currencies = $this->_block->getCurrencyJson();
        $currencies = json_decode($currencies, true);
        $this->assertCount(1, $currencies);
        $this->assertEquals('USD', $currencies[1]);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInitForm()
    {
        Mage::register('current_giftcardaccount', Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount'));
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $block = $layout->addBlock('Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info');

        $element = $block->initForm()->getForm()->getElement('date_expires');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
