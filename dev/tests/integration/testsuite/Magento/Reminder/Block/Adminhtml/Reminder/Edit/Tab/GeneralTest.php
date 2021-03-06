<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab;

/**
 * Test class for \Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\General
 * @magentoAppArea adminhtml
 */
class GeneralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\DesignInterface'
        )->setArea(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get(
            'Magento\Framework\Registry'
        )->register(
            'current_reminder_rule',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Reminder\Model\Rule')
        );

        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\General'
        );
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\General',
            '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (['from_date', 'to_date'] as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
        $element = $form->getElement('salesrule_id');
        $this->assertNotNull($element);
        $this->assertRegExp('#http://[a-z\./sales_rule/promo_quote/chooser/.*]#', $element->getAfterElementHtml());
    }
}
