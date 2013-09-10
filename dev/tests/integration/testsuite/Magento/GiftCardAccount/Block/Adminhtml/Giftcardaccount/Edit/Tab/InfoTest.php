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
 * Test class for Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info
 *
 * @magentoAppArea adminhtml
 */
class Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_InfoTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $model = Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount');
        Mage::register('current_giftcardaccount', $model);

        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        $this->_block = $layout
            ->createBlock('Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info');
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
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
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
     * @magentoConfigFixture current_store general/single_store_mode/enabled 0
     */
    public function testPrepareFormMultipleStore()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $this->_block->initForm();
        $form = $this->_block->getForm();
        $this->assertEquals('base_fieldset', $form->getElement('base_fieldset')->getId());

        $element = $form->getElement('website_id');
        $this->assertNotNull($element);
        $this->assertInstanceOf('Magento_Data_Form_Element_Select', $element);
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
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        $block = $layout->addBlock('Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info');

        $element = $block->initForm()->getForm()->getElement('date_expires');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
