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
namespace Magento\Adminhtml\Block\Promo\Catalog\Edit\Tab;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $rule = $objectManager->create('Magento\CatalogRule\Model\Rule');
        $objectManager->get('Magento\Core\Model\Registry')->register('current_promo_catalog_rule', $rule);

        $block = $objectManager->create('Magento\Adminhtml\Block\Promo\Catalog\Edit\Tab\Main');
        $block->setLayout($objectManager->create('Magento\Core\Model\Layout'));
        $prepareFormMethod = new \ReflectionMethod(
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
