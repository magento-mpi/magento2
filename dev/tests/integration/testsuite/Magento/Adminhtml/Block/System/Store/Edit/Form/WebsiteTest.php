<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\System\Store\Edit\Form;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea adminhtml
 */
class WebsiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\System\Store\Edit\Form\Website
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $registryData = array(
            'store_type' => 'website',
            'store_data' => \Mage::getModel('Magento\Core\Model\Website'),
            'store_action' => 'add'
        );
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        foreach ($registryData as $key => $value) {
            $objectManager->get('Magento\Core\Model\Registry')->register($key, $value);
        }

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');

        $this->_block = $layout->createBlock('Magento\Adminhtml\Block\System\Store\Edit\Form\Website');

        $this->_block->toHtml();
    }

    protected function tearDown()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->unregister('store_type');
        $objectManager->get('Magento\Core\Model\Registry')->unregister('store_data');
        $objectManager->get('Magento\Core\Model\Registry')->unregister('store_action');
    }

    public function testPrepareForm()
    {
        $form = $this->_block->getForm();
        $this->assertEquals('website_fieldset', $form->getElement('website_fieldset')->getId());
        $this->assertEquals('website_name', $form->getElement('website_name')->getId());
        $this->assertEquals('website', $form->getElement('store_type')->getValue());
    }
}
