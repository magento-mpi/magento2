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

namespace Magento\RecurringProfile\Block\Profile;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\RecurringProfile\Block\Profile\View
     */
    protected $_block;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\RecurringProfile\Model\Profile
     */
    protected $_profile;

    protected function setUp()
    {
        $this->_profile = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\RecurringProfile\Model\Profile');
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Registry')->register('current_recurring_profile', $this->_profile);

        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\LayoutInterface');
        $this->_block = $this->_layout->createBlock('Magento\RecurringProfile\Block\Profile\View', 'block');
    }

    protected function tearDown()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Registry')->unregister('current_recurring_profile');
        $this->_profile = null;
        $this->_block = null;
        $this->_layout = null;
    }

    public function testToHtmlPropagatesUrl()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
            ->setAreaCode('frontend');
        $this->_block->setShouldPrepareInfoTabs(true);
        $childOne = $this->_layout->addBlock('Magento\View\Element\Text', 'child1', 'block');
        $this->_layout->addToParentGroup('child1', 'info_tabs');
        $childTwo = $this->_layout->addBlock('Magento\View\Element\Text', 'child2', 'block');
        $this->_layout->addToParentGroup('child2', 'info_tabs');

        $this->assertEmpty($childOne->getViewUrl());
        $this->assertEmpty($childTwo->getViewUrl());
        $this->_block->toHtml();
        $this->assertNotEmpty($childOne->getViewUrl());
        $this->assertNotEmpty($childTwo->getViewUrl());
    }
}
