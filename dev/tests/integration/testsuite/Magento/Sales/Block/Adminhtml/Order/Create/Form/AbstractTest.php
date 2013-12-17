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

/**
 * Test class for \Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Form;

class AbstractTest
    extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddAttributesToForm()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);

        $objectManager->get('Magento\View\DesignInterface')
            ->setDefaultDesignTheme();
        $arguments = array(
            $objectManager->get('Magento\Backend\Block\Template\Context'),
            $objectManager->get('Magento\Backend\Model\Session\Quote'),
            $objectManager->get('Magento\Sales\Model\AdminOrder\Create'),
            $objectManager->get('Magento\Data\FormFactory'),
        );

        /** @var $block \Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm */
        $block = $this
            ->getMockForAbstractClass('Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm', $arguments);
        $block->setLayout($objectManager->create('Magento\Core\Model\Layout'));

        $method = new \ReflectionMethod(
            'Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm', '_addAttributesToForm');
        $method->setAccessible(true);

        /** @var $formFactory \Magento\Data\FormFactory */
        $formFactory = $objectManager->get('Magento\Data\FormFactory');
        $form = $formFactory->create();
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
