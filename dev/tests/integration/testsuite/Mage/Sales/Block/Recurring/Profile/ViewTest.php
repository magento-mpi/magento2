<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Block_Recurring_Profile_ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Block_Recurring_Profile_View
     */
    protected $_block;

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Mage_Sales_Model_Recurring_Profile
     */
    protected $_profile;

    public function setUp()
    {
        $this->_profile = new Mage_Sales_Model_Recurring_Profile;
        Mage::register('current_recurring_profile', $this->_profile);

        $this->_block = new Mage_Sales_Block_Recurring_Profile_View;
        $this->_layout = new Mage_Core_Model_Layout;
        $this->_layout->addBlock($this->_block, 'block');
    }

    public function tearDown()
    {
        Mage::unregister('current_recurring_profile');
        $this->_profile = null;
        $this->_block = null;
        $this->_layout = null;
    }

    public function testPrepareAddressInfo()
    {
        $this->_profile->setData('billing_address_info', array('city' => 'Los Angeles'));
        $this->_block->prepareAddressInfo();
        $info = $this->_block->getRenderedInfo();
        $this->assertContains('Los Angeles', $info[0]->getValue());
    }

    public function testToHtmlPropagatesUrl()
    {
        $this->_block->setShouldPrepareInfoTabs(true);
        $child1 = $this->_layout->addBlock('Mage_Core_Block_Text', 'child1', 'block');
        $this->_layout->addToParentGroup('child1', 'info_tabs');
        $child2 = $this->_layout->addBlock('Mage_Core_Block_Text', 'child2', 'block');
        $this->_layout->addToParentGroup('child2', 'info_tabs');

        $this->assertEmpty($child1->getViewUrl());
        $this->assertEmpty($child2->getViewUrl());
        $this->_block->toHtml();
        $this->assertNotEmpty($child1->getViewUrl());
        $this->assertNotEmpty($child2->getViewUrl());
    }
}
