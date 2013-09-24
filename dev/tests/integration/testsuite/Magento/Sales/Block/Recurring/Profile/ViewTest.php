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

namespace Magento\Sales\Block\Recurring\Profile;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Block\Recurring\Profile\View
     */
    protected $_block;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Sales\Model\Recurring\Profile
     */
    protected $_profile;

    protected function setUp()
    {
        $this->_profile = \Mage::getModel('Magento\Sales\Model\Recurring\Profile');
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_recurring_profile', $this->_profile);

        $this->_layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $this->_block = $this->_layout->createBlock('Magento\Sales\Block\Recurring\Profile\View', 'block');
    }

    protected function tearDown()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->unregister('current_recurring_profile');
        $this->_profile = null;
        $this->_block = null;
        $this->_layout = null;
    }

    public function testToHtmlPropagatesUrl()
    {
        $this->_block->setShouldPrepareInfoTabs(true);
        $childOne = $this->_layout->addBlock('Magento\Core\Block\Text', 'child1', 'block');
        $this->_layout->addToParentGroup('child1', 'info_tabs');
        $childTwo = $this->_layout->addBlock('Magento\Core\Block\Text', 'child2', 'block');
        $this->_layout->addToParentGroup('child2', 'info_tabs');

        $this->assertEmpty($childOne->getViewUrl());
        $this->assertEmpty($childTwo->getViewUrl());
        $this->_block->toHtml();
        $this->assertNotEmpty($childOne->getViewUrl());
        $this->assertNotEmpty($childTwo->getViewUrl());
    }
}
