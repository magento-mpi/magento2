<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Block_Recurring_Profile_ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Block_Recurring_Profile_View
     */
    protected $_block;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Sales_Model_Recurring_Profile
     */
    protected $_profile;

    protected function setUp()
    {
        $this->_profile = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Recurring_Profile');
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('current_recurring_profile', $this->_profile);

        $this->_layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Magento_Sales_Block_Recurring_Profile_View', 'block');
    }

    protected function tearDown()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('current_recurring_profile');
        $this->_profile = null;
        $this->_block = null;
        $this->_layout = null;
    }

    public function testToHtmlPropagatesUrl()
    {
        $this->_block->setShouldPrepareInfoTabs(true);
        $childOne = $this->_layout->addBlock('Magento_Core_Block_Text', 'child1', 'block');
        $this->_layout->addToParentGroup('child1', 'info_tabs');
        $childTwo = $this->_layout->addBlock('Magento_Core_Block_Text', 'child2', 'block');
        $this->_layout->addToParentGroup('child2', 'info_tabs');

        $this->assertEmpty($childOne->getViewUrl());
        $this->assertEmpty($childTwo->getViewUrl());
        $this->_block->toHtml();
        $this->assertNotEmpty($childOne->getViewUrl());
        $this->assertNotEmpty($childTwo->getViewUrl());
    }
}
