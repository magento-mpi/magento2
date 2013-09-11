<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain
 */
class Magento_Eav_Block_Adminhtml_Attribute_Edit_Main_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $entityType = Mage::getSingleton('Magento\Eav\Model\Config')->getEntityType('customer');
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento\Customer\Model\Attribute');
        $model->setEntityTypeId($entityType->getId());
        Mage::register('entity_attribute', $model);

        $block = $this->getMockForAbstractClass(
            '\Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain',
            array(Mage::getSingleton('Magento\Backend\Block\Template\Context'))
        )
        ->setLayout(Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento\Core\Model\Layout'));

        $method = new ReflectionMethod(
            '\Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain', '_prepareForm');
        $method->setAccessible(true);
        $method->invoke($block);

        $element = $block->getForm()->getElement('default_value_date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
