<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Controller\Adminhtml\GoogleShopping;

/**
 * @magentoAppArea adminhtml
 */
class ItemsTest extends \Magento\Backend\Utility\Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/googleshopping_items/index/store/1/');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('div#items', 1, $body);
        $this->assertSelectCount('div#googleshopping_selection_search_grid_', 1, $body);
    }
}
