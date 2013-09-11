<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Backend\Block\Widget\Form
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Block_Widget_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testSetFieldset()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $layout = $objectManager->create('Magento\Core\Model\Layout');
        $formBlock = $layout->addBlock('\Magento\Backend\Block\Widget\Form');
        $fieldSet = $objectManager->create('Magento\Data\Form\Element\Fieldset');
        $arguments = array(
            'data' => array(
                'attribute_code' => 'date',
                'backend_type'   => 'datetime',
                'frontend_input' => 'date',
                'frontend_label' => 'Date',
            )
        );
        $attributes = array($objectManager->create('Magento\Eav\Model\Entity\Attribute', $arguments));
        $method = new ReflectionMethod('\Magento\Backend\Block\Widget\Form', '_setFieldset');
        $method->setAccessible(true);
        $method->invoke($formBlock, $attributes, $fieldSet);
        $fields = $fieldSet->getElements();

        $this->assertEquals(1, count($fields));
        $this->assertInstanceOf('\Magento\Data\Form\Element\Date', $fields[0]);
        $this->assertNotEmpty($fields[0]->getDateFormat());
    }
}
