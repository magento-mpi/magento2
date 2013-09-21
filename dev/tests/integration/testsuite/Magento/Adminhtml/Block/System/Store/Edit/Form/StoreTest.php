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
class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\System\Store\Edit\Form\Store
     */
    protected $_block;

    public function setUp()
    {
        parent::setUp();

        $registryData = array(
            'store_type' => 'store',
            'store_data' => \Mage::getModel('Magento\Core\Model\Store'),
            'store_action' => 'add'
        );
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        foreach ($registryData as $key => $value) {
            $objectManager->get('Magento\Core\Model\Registry')->register($key, $value);
        }

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');

        $this->_block = $layout->createBlock('Magento\Adminhtml\Block\System\Store\Edit\Form\Store');

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
        $this->assertEquals('store_fieldset', $form->getElement('store_fieldset')->getId());
        $this->assertEquals('store_name', $form->getElement('store_name')->getId());
        $this->assertEquals('store', $form->getElement('store_type')->getValue());
    }
}
