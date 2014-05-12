<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Controller\Adminhtml\GoogleShopping;

/**
 * @magentoAppArea adminhtml
 */
class TypesTest extends \Magento\Backend\Utility\Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/googleshopping_types/index/');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('[data-role="row"]', 1, $body, 'Grid with row exists');
    }
}
