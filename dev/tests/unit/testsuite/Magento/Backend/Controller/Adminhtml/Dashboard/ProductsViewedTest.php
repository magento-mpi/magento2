<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Dashboard;

/**
 * Test for \Magento\Backend\Controller\Adminhtml\Dashboard\ProductViewed
 */
class ProductsViewedTest extends AbstractTestCase
{
    public function testExecute()
    {
        $this->assertExecute(
            'Magento\Backend\Controller\Adminhtml\Dashboard\ProductsViewed',
            'Magento\Backend\Block\Dashboard\Tab\Products\Viewed'
        );
    }
}