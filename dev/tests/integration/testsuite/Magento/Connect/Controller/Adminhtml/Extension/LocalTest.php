<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Connect\Controller\Adminhtml\Extension;

/**
 * Test \Magento\Connect\Controller\Adminhtml\Extension\Local
 *
 * @magentoAppArea adminhtml
 */
class LocalTest extends \Magento\Backend\Utility\Controller
{
    public function testIndexAction()
    {
        $expected = '?return=' . urlencode(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                    ->get('Magento\Backend\Helper\Data')->getHomePageUrl()
            );
        $this->dispatch('backend/admin/extension_local/index');
        $this->assertRedirect($this->stringEndsWith($expected));
    }
}
