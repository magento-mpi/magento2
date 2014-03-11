<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Block\Adminhtml\Page\Edit\Tab;

/**
 * Test class for \Magento\Cms\Block\Adminhtml\Page\Edit\Tab\Design
 * @magentoAppArea adminhtml
 */
class DesignTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\View\DesignInterface')
            ->setArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
            ->setDefaultDesignTheme();
        $objectManager->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $objectManager->get('Magento\Registry')
            ->register('cms_page', $objectManager->create('Magento\Cms\Model\Page'));

        $block = $objectManager->create('Magento\Cms\Block\Adminhtml\Page\Edit\Tab\Design');
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\Cms\Block\Adminhtml\Page\Edit\Tab\Design', '_prepareForm');
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
