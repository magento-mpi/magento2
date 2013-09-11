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
 * Test class for \Magento\Adminhtml\Block\Cms\Page\Edit\Tab\Design
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Cms_Page_Edit_Tab_DesignTest extends PHPUnit_Framework_TestCase
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
        $objectManager->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        Mage::register('cms_page', Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Cms\Model\Page'));

        $block = $objectManager->create('Magento\Adminhtml\Block\Cms\Page\Edit\Tab\Design');
        $prepareFormMethod = new ReflectionMethod(
            '\Magento\Adminhtml\Block\Cms\Page\Edit\Tab\Design', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('custom_theme_to', 'custom_theme_from') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
