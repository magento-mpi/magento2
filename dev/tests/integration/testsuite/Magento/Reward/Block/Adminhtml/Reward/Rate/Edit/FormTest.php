<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Block\Adminhtml\Reward\Rate\Edit;

/**
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Reward\Block\Adminhtml\Reward\Rate\Edit\Form */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Layout');
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        if (!$objectManager->get('Magento\Core\Model\Registry')->registry('current_reward_rate')) {
            $rate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Reward\Model\Reward\Rate');
            $objectManager->get('Magento\Core\Model\Registry')->register('current_reward_rate', $rate);
        }

        $this->_block = $layout
            ->createBlock('Magento\Reward\Block\Adminhtml\Reward\Rate\Edit\Form');
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
        $this->assertInstanceOf('Magento\Data\Form', $form);
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
        $this->assertInstanceOf('Magento\Data\Form', $form);
        $element = $form->getElement('website_id');
        $this->assertNotNull($element);
        $this->assertInstanceOf('Magento\Data\Form\Element\Select', $element);
        $this->assertEquals('website_id', $element->getId());
    }
}
