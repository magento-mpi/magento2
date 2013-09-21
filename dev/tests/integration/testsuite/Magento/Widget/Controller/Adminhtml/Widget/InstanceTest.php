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

namespace Magento\Widget\Controller\Adminhtml\Widget;

/**
 * @magentoAppArea adminhtml
 */
class InstanceTest extends \Magento\Backend\Utility\Controller
{
    protected function setUp()
    {
        parent::setUp();

        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\View\DesignInterface')
            ->setDefaultDesignTheme()
            ->getDesignTheme();
        $this->getRequest()->setParam('type', 'Magento\Cms\Block\Widget\Page\Link');
        $this->getRequest()->setParam('theme_id', $theme->getId());
    }

    /**
     * @magentoConfigFixture adminhtml/design/theme/full_name magento_basic
     */
    public function testEditAction()
    {
        $this->dispatch('backend/admin/widget_instance/edit');
        $this->assertContains('<option value="Magento\Cms\Block\Widget\Page\Link" selected="selected">',
            $this->getResponse()->getBody()
        );
    }

    /**
     * @magentoConfigFixture adminhtml/design/theme/full_name magento_basic
     */
    public function testBlocksAction()
    {
        $this->dispatch('backend/admin/widget_instance/blocks');
        $this->assertStringStartsWith('<select name="block" id=""', $this->getResponse()->getBody());
    }

    /**
     * @magentoConfigFixture adminhtml/design/theme/full_name magento_basic
     */
    public function testTemplateAction()
    {
        $this->dispatch('backend/admin/widget_instance/template');
        $this->assertStringStartsWith('<select name="template" id=""', $this->getResponse()->getBody());
    }
}
