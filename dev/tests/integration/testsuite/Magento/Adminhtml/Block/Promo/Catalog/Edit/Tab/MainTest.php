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
 * Test class for \Magento\Adminhtml\Block\Promo\Catalog\Edit\Tab\Main
 *
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Promo_Catalog_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $rule = $objectManager->create('Magento\CatalogRule\Model\Rule');
        Mage::register('current_promo_catalog_rule', $rule);

        $block = $objectManager->create('Magento\Adminhtml\Block\Promo\Catalog\Edit\Tab\Main');
        $block->setLayout($objectManager->create('Magento\Core\Model\Layout'));
        $prepareFormMethod = new ReflectionMethod(
            'Magento\Adminhtml\Block\Promo\Catalog\Edit\Tab\Main', '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('from_date', 'to_date') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
