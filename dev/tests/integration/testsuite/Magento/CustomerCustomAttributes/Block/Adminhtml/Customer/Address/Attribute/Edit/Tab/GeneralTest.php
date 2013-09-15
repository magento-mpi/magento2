<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Address\Attribute\Edit\Tab\General
 * @magentoAppArea adminhtml
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_GeneralTest
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
        /** @var $model \Magento\Customer\Model\Attribute */
        $model = Mage::getModel('Magento\Customer\Model\Attribute');
        $model->setEntityTypeId($entityType->getId());
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('entity_attribute', $model);

        $block = Mage::app()->getLayout()->createBlock(
            'Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Address\Attribute\Edit\Tab\General'
        );
        $prepareFormMethod = new ReflectionMethod(
            'Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Address\Attribute\Edit\Tab\General',
            '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('date_range_min', 'date_range_max') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
