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

/**
 * Test class for \Magento\Adminhtml\Block\Sales\Order\Create\Form\AbstractForm
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Form_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddAttributesToForm()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $arguments = array($objectManager->get('Magento\Backend\Block\Template\Context'));
        /** @var $block \Magento\Adminhtml\Block\Sales\Order\Create\Form\AbstractForm */
        $block = $this->getMockForAbstractClass('\Magento\Adminhtml\Block\Sales\Order\Create\Form\AbstractForm', $arguments);
        $block->setLayout($objectManager->create('Magento\Core\Model\Layout'));

        $method = new ReflectionMethod(
            '\Magento\Adminhtml\Block\Sales\Order\Create\Form\AbstractForm', '_addAttributesToForm');
        $method->setAccessible(true);

        $form = new \Magento\Data\Form();
        $fieldset = $form->addFieldset('test_fieldset', array());
        $arguments = array(
            'data' => array(
                'attribute_code' => 'date',
                'backend_type' => 'datetime',
                'frontend_input' => 'date',
                'frontend_label' => 'Date',
            )
        );
        $dateAttribute = $objectManager->create('Magento\Customer\Model\Attribute', $arguments);
        $attributes = array('date' => $dateAttribute);
        $method->invoke($block, $attributes, $fieldset);

        $element = $form->getElement('date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
