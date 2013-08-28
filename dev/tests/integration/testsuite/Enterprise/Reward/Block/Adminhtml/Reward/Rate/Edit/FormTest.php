<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Enterprise_Reward_Block_Adminhtml_Reward_Rate_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /** @var Enterprise_Reward_Block_Adminhtml_Reward_Rate_Edit_Form */
    protected $_block;

    public function setUp()
    {
        parent::setUp();
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        if (!Mage::registry('current_reward_rate')) {
            $rate = Mage::getModel('Enterprise_Reward_Model_Reward_Rate');
            Mage::register('current_reward_rate', $rate);
        }

        $this->_block = $layout
            ->createBlock('Enterprise_Reward_Block_Adminhtml_Reward_Rate_Edit_Form');
    }

    /**
     * Test Prepare Form in Single Store mode
     *
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testPrepareFormSingleStore()
    {
        $this->_block->toHtml();
        $form = $this->_block->getForm();
        $this->assertInstanceOf('Magento_Data_Form', $form);
        $this->assertNull($form->getElement('website_id'));
    }

    /**
     * Test Prepare Form in Multiple Store mode
     *
     * @magentoConfigFixture current_store general/single_store_mode/enabled 0
     */
    public function testPrepareFormMultipleStore()
    {
        $this->markTestIncomplete('Test used wrong area, as area was not set to layout previously');
        $this->_block->toHtml();
        $form = $this->_block->getForm();
        $this->assertInstanceOf('Magento_Data_Form', $form);
        $element = $form->getElement('website_id');
        $this->assertNotNull($element);
        $this->assertInstanceOf('Magento_Data_Form_Element_Select', $element);
        $this->assertEquals('website_id', $element->getId());
    }
}
