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

namespace Magento\Adminhtml\Block\Promo\Quote\Edit\Tab;

/**
 * Test class for \Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Main
 *
 * @magentoAppArea adminhtml
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $objectManager->get('Magento\Core\Model\Registry')
            ->register('current_promo_quote_rule', $objectManager->create('Magento\SalesRule\Model\Rule'));

        $layout = $objectManager->create('Magento\Core\Model\Layout');
        $block = $layout->addBlock('Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Main');
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Main', '_prepareForm'
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
