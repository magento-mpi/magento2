<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Block\Adminhtml\Event\Edit;

/**
 * Test that Catalog Event Edit form is wrapped by AdminGws
 *
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/AdminGws/_files/role_websites_login.php
     * @magentoDataFixture Magento/CatalogEvent/_files/events.php
     */
    public function testToHtmlDisabledTickerDisplay()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\User\Model\Role $adminRole */
        $adminRole = $objectManager->get('Magento\User\Model\Role');
        $adminRole->load('admingws_role', 'role_name');

        /** @var \Magento\AdminGws\Model\Role $adminGwsRole */
        $adminGwsRole = $objectManager->get('Magento\AdminGws\Model\Role');
        $adminGwsRole->setAdminRole($adminRole);

        /** @var $event \Magento\CatalogEvent\Model\Event */
        $event = $objectManager->create('Magento\CatalogEvent\Model\Event');
        $event->load(1, 'category_id');
        $objectManager->get('Magento\Core\Model\Registry')->register('magento_catalogevent_event', $event);

        /** @var \Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Form $block */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Form');
        $block->toHtml();

        $checkboxValues = array(
            \Magento\CatalogEvent\Model\Event::DISPLAY_CATEGORY_PAGE,
            \Magento\CatalogEvent\Model\Event::DISPLAY_PRODUCT_PAGE
        );
        /** @var \Magento\Data\Form\Element\AbstractElement $element */
        $element = $block->getForm()->getElement('display_state_array');
        foreach ($checkboxValues as $value) {
            $this->assertEquals('disabled', $element->getDisabled($value));
        }
    }
}
